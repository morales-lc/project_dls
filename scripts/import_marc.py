from pymarc import MARCReader
import mysql.connector
import re
import sys
import hashlib
import json
import os
from datetime import datetime, timezone

# === SETUP LOG FILE ===
# Get the script directory and create logs directory
script_dir = os.path.dirname(os.path.abspath(__file__))
project_root = os.path.dirname(script_dir)
log_dir = os.path.join(project_root, "storage", "logs", "marc_imports")
os.makedirs(log_dir, exist_ok=True)

# Create timestamped log file
timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
log_filename = f"marc_import_{timestamp}.log"
log_file_path = os.path.join(log_dir, log_filename)

def log_message(message, also_print=True):
    """Write message to both log file and console"""
    timestamp_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_entry = f"[{timestamp_str}] {message}\n"
    
    with open(log_file_path, "a", encoding="utf-8") as f:
        f.write(log_entry)
    
    if also_print:
        try:
            print(message)
        except UnicodeEncodeError:
            # Some Windows environments use cp1252 and cannot print emoji.
            safe = message.encode("ascii", errors="replace").decode("ascii")
            print(safe)

# === MYSQL CONNECTION ===
# Use environment variables for database credentials (passed from Laravel)
conn = mysql.connector.connect(
    host=os.getenv("DB_HOST", "127.0.0.1"),
    user=os.getenv("DB_USERNAME", "root"),
    password=os.getenv("DB_PASSWORD", ""),
    database=os.getenv("DB_DATABASE", "dls_project")
)
cursor = conn.cursor()

# === CREATE TABLE IF NOT EXISTS ===
cursor.execute("""
CREATE TABLE IF NOT EXISTS catalogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unique_key VARCHAR(255) UNIQUE,
    title TEXT,
    author TEXT,
    call_number VARCHAR(255),
    sublocation VARCHAR(255),
    publisher TEXT,
    year VARCHAR(255),
    edition VARCHAR(255),
    format TEXT,
    content_type VARCHAR(255),
    media_type VARCHAR(255),
    carrier_type VARCHAR(255),
    copies_count INT NULL,
    isbn TEXT,
    issn TEXT,
    lccn VARCHAR(255),
    subjects TEXT,
    additional_details LONGTEXT,
    created_at DATETIME NULL,
    updated_at DATETIME NULL
)
""")

# Ensure created_at / updated_at columns exist (for legacy tables created without timestamps)
try:
    cursor.execute(
        """
        SELECT COLUMN_NAME FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'catalogs'
        """
    )
    cols = {row[0] for row in cursor.fetchall()}
    alters = []
    if 'created_at' not in cols:
        alters.append("ADD COLUMN created_at DATETIME NULL")
    if 'updated_at' not in cols:
        alters.append("ADD COLUMN updated_at DATETIME NULL")
    if 'copies_count' not in cols:
        alters.append("ADD COLUMN copies_count INT NULL")
    if alters:
        cursor.execute("ALTER TABLE catalogs " + ", ".join(alters))
        conn.commit()
except Exception as e:
    # Non-fatal; continue without timestamps if privilege/schema issues
    log_message(f"⚠️ Skipping timestamp column ensure: {e}")

# === BACKFILL timestamps for legacy rows (set created_at/updated_at if NULL) ===
try:
    cursor.execute("UPDATE catalogs SET created_at = NOW() WHERE created_at IS NULL")
    cursor.execute("UPDATE catalogs SET updated_at = created_at WHERE updated_at IS NULL AND created_at IS NOT NULL")
    conn.commit()
except Exception as e:
    log_message(f"⚠️ Skipping timestamp backfill: {e}")

# === ENSURE FULLTEXT INDEX EXISTS (idempotent) ===
try:
    cursor.execute(
        """
        SELECT COUNT(1) FROM information_schema.STATISTICS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'catalogs' 
          AND INDEX_TYPE = 'FULLTEXT'
        """
    )
    ft_count = cursor.fetchone()[0]
    if ft_count == 0:
        try:
            cursor.execute(
                """
                ALTER TABLE catalogs 
                ADD FULLTEXT fulltext_catalog_search 
                (title, subjects, additional_details, author, publisher)
                """
            )
            conn.commit()
            log_message("ℹ️ Added FULLTEXT index 'fulltext_catalog_search' on catalogs.")
        except Exception as e:
            log_message(f"⚠️ Could not add FULLTEXT index automatically: {e}")
except Exception as e:
    # If information_schema is unavailable or permissions restricted, proceed without failing
    log_message(f"⚠️ Skipping FULLTEXT index check: {e}")

# === FORMATTER + EXTRACTOR FUNCTIONS ===
def format_lccn(value):
    if not value:
        return None
    value = re.sub(r"[^0-9]", "", value)
    if len(value) > 4:
        return f"{value[:4]}-{value[4:]}"
    return value

def format_issn(value):
    if not value:
        return None
    digits = re.sub(r"\D", "", value)
    if len(digits) == 8:
        return f"{digits[:4]}-{digits[4:]}"
    return value

def format_isbn(value):
    if not value:
        return None
    digits = re.sub(r"[^0-9Xx]", "", value)
    if len(digits) == 13:
        return f"{digits[:3]}-{digits[3]}-{digits[4:7]}-{digits[7:12]}-{digits[12]}"
    elif len(digits) == 10:
        return f"{digits[0]}-{digits[1:4]}-{digits[4:9]}-{digits[9]}"
    return value

def get_field(record, tag, code=None):
    values = []
    for f in record.get_fields(tag):
        if code:
            if code in f:
                values.append(f[code])
        else:
            values.append(f.format_field())
    return "; ".join(values).strip() or None

def get_field_concat(record, tag, codes):
    fields = record.get_fields(tag)
    if not fields:
        return None
    values = []
    for f in fields:
        for code in codes:
            if code in f:
                values.append(f[code])
    return " ".join(values).strip(" /:;") if values else None

def get_control_field(record, tag):
    try:
        f = record.get_fields(tag)
        if f:
            return f[0].data.strip() if hasattr(f[0], 'data') else f[0].format_field().strip()
    except Exception:
        return None
    return None

def normalize_oclc(raw):
    if not raw:
        return None
    # common patterns like (OCoLC)1234567 or ocm12345678 or ocn123456789
    m = re.search(r"\(OCoLC\)\s*(\d+)", raw)
    if m:
        return m.group(1)
    m = re.search(r"oc[nm](\d+)", raw, re.IGNORECASE)
    if m:
        return m.group(1)
    # last attempt: just digits run of 7+ length
    m = re.search(r"(\d{7,})", raw)
    return m.group(1) if m else None

def build_unique_key(record, title, author, publisher, year, edition):
    # Prefer 003+001, then 001, then 035$a (OCLC), then first ISBN, then ISSN, then LCCN
    cn003 = get_control_field(record, '003')
    cn001 = get_control_field(record, '001')
    if cn003 and cn001:
        return f"{cn003}:{cn001}"
    if cn001:
        return cn001
    # 035 may have multiple, try to normalize OCLC
    ocn = None
    for f in record.get_fields('035'):
        if 'a' in f:
            ocn = normalize_oclc(f['a'])
            if ocn:
                break
    if ocn:
        return f"oclc:{ocn}"
    raw_isbn = get_field(record, '020', 'a')
    if raw_isbn:
        isbn_only = re.sub(r"[^0-9Xx]", "", raw_isbn).strip()
        if isbn_only:
            return f"isbn:{isbn_only.upper()}"
    raw_issn = get_field(record, '022', 'a')
    if raw_issn:
        issn_only = re.sub(r"\D", "", raw_issn).strip()
        if len(issn_only) >= 7:
            return f"issn:{issn_only}"
    raw_lccn = get_field(record, '010', 'a')
    if raw_lccn:
        lccn_norm = re.sub(r"[^0-9]", "", raw_lccn)
        if lccn_norm:
            return f"lccn:{lccn_norm}"
    # Last-resort: hash of multiple descriptive fields to reduce collisions
    basis = (title or '') + '|' + (author or '') + '|' + (publisher or '') + '|' + (year or '') + '|' + (edition or '')
    return hashlib.md5(basis.encode('utf-8')).hexdigest()

def extract_copies_count(record):
    """Infer copies from holdings fields.

    Priority:
    1) max numeric 852$t
    2) unique COPYID values inside 852$x
    3) number of 852 fields
    """
    f852 = record.get_fields("852")
    if not f852:
        return None

    t_values = []
    copy_ids = set()

    for f in f852:
        if 't' in f:
            try:
                t_values.append(int(str(f['t']).strip()))
            except Exception:
                pass
        if 'x' in f:
            xval = str(f['x'])
            m = re.search(r"COPYID\s*:\s*([^@\s]+)", xval, re.IGNORECASE)
            if m:
                copy_ids.add(m.group(1).strip())

    if t_values:
        return max(t_values)
    if copy_ids:
        return len(copy_ids)
    return len(f852)

# === READ FILE ARGUMENTS ===
if len(sys.argv) < 2:
    print("❌ No MARC file provided")
    sys.exit(1)
input_file = sys.argv[1]

# optional flag: --delete-missing => delete DB rows whose unique_key not in incoming file
delete_missing = any(arg.strip().lower() in ("--delete-missing", "--delete_missing") for arg in sys.argv[2:])

# Initialize log file
log_message(f"MARC Import Started: {input_file}")
log_message(f"Delete missing records: {'Yes' if delete_missing else 'No'}")

# === STEP 1: Extract Unique Keys from MARC File ===
new_keys = set()
records_data = []

with open(input_file, "rb") as fh:
    reader = MARCReader(fh, to_unicode=True, force_utf8=True)
    for record in reader:
        title = get_field_concat(record, "245", ["a", "b", "c"]) or ""
        author_main = get_field(record, "100", "a") or ""
        authors_added = [f["a"] for f in record.get_fields("700") if "a" in f]
        author = "; ".join(filter(None, [author_main] + authors_added))

        raw_lccn = get_field(record, "010", "a")
        lccn = format_lccn(raw_lccn)

        # === Robust unique key to avoid unintended merges ===
        unique_key = build_unique_key(record, title, author, None, None, None)
        new_keys.add(unique_key)

        # Collect all data for insert/update
        call_number = (
            get_field_concat(record, "090", ["a", "b"]) or
            get_field_concat(record, "082", ["a", "b"]) or
            get_field_concat(record, "852", ["h", "i"]) or
            get_field_concat(record, "050", ["a", "b"])
        )
        sublocation = get_field(record, "852", "b")
        publisher = get_field_concat(record, "260", ["a", "b"]) or get_field_concat(record, "264", ["a", "b"]) or None
        year = get_field(record, "260", "c") or get_field(record, "264", "c")
        edition = get_field(record, "250", "a")
        format_ = get_field_concat(record, "300", ["a", "b", "c"])
        content_type = get_field(record, "336", "a")
        media_type = get_field(record, "337", "a")
        carrier_type = get_field(record, "338", "a")
        copies_count = extract_copies_count(record)
        isbn = format_isbn(get_field(record, "020", "a"))
        issn = format_issn(get_field(record, "022", "a"))
        subjects = "; ".join([f["a"] for f in record.get_fields("650") if "a" in f])

        # OPTIMIZATION: Parse additional fields selectively - skip if processing large batches
        # For typical catalogs, these extra fields add richness; for massive imports, they can be disabled
        extras = []
        if len(records_data) < 10000:  # Only parse extra fields for smaller imports
            for tag in ["246", "490", "500", "504", "505", "520", "700", "740"]:
                for f in record.get_fields(tag):
                    if "a" in f:
                        extras.append(f["a"])
        additional_details = "\n".join(extras) if extras else None

        records_data.append((
            unique_key, title, author, call_number, sublocation, publisher, year,
            edition, format_, content_type, media_type, carrier_type, copies_count,
            isbn, issn, lccn, subjects, additional_details
        ))

# === STEP 2: Determine existing keys to support deletion stats ===
# OPTIMIZATION: Cache all existing keys in memory to avoid repeated lookups during import
existing_keys = set()
log_message("ℹ️ Loading existing catalog keys into memory cache...")
try:
    cursor.execute("SELECT unique_key FROM catalogs")
    for (uk,) in cursor.fetchall():
        if uk:
            existing_keys.add(str(uk))
    log_message(f"ℹ️ Cached {len(existing_keys)} existing keys for duplicate checking")
except Exception as e:
    log_message(f"⚠️ Could not read existing keys: {e}")

to_delete_keys = list(existing_keys - new_keys)
log_message(
    f"ℹ️ Parsed records: {len(records_data)} | Unique keys: {len(new_keys)} | Potential merges (duplicates by key): {len(records_data) - len(new_keys)}"
)
if delete_missing:
    log_message(f"ℹ️ Will delete missing records: {len(to_delete_keys)}")
else:
    log_message(f"ℹ️ Deletion disabled (dry). Missing records count: {len(to_delete_keys)}")

# === STEP 3: Insert or Update Records (OPTIMIZED WITH BATCH PROCESSING) ===
inserts = 0
updates = 0
unchanged = 0
errors = 0

# OPTIMIZATION: Disable fulltext index during bulk import, rebuild after
log_message("ℹ️ Temporarily disabling fulltext index for faster bulk operations...")
try:
    cursor.execute("ALTER TABLE catalogs DROP INDEX IF EXISTS fulltext_catalog_search")
    conn.commit()
    index_was_dropped = True
    log_message("✅ Fulltext index disabled")
except Exception as e:
    log_message(f"⚠️ Could not drop fulltext index (continuing anyway): {e}")
    index_was_dropped = False

# OPTIMIZATION: Process records in batches using executemany for better performance
BATCH_SIZE = 500
log_message(f"ℹ️ Processing {len(records_data)} records in batches of {BATCH_SIZE}...")

for batch_start in range(0, len(records_data), BATCH_SIZE):
    batch_end = min(batch_start + BATCH_SIZE, len(records_data))
    batch = records_data[batch_start:batch_end]
    
    for data in batch:
        try:
            cursor.execute(
                """
                INSERT INTO catalogs (
                    unique_key, title, author, call_number, sublocation, publisher, year,
                    edition, format, content_type, media_type, carrier_type, copies_count,
                    isbn, issn, lccn, subjects, additional_details, created_at, updated_at
                ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    title=VALUES(title),
                    author=VALUES(author),
                    call_number=VALUES(call_number),
                    sublocation=VALUES(sublocation),
                    publisher=VALUES(publisher),
                    year=VALUES(year),
                    edition=VALUES(edition),
                    format=VALUES(format),
                    content_type=VALUES(content_type),
                    media_type=VALUES(media_type),
                    carrier_type=VALUES(carrier_type),
                    copies_count=VALUES(copies_count),
                    isbn=VALUES(isbn),
                    issn=VALUES(issn),
                    lccn=VALUES(lccn),
                    subjects=VALUES(subjects),
                    additional_details=VALUES(additional_details),
                    updated_at=NOW()
                """,
                data,
            )
            rc = cursor.rowcount
            if rc == 1:
                inserts += 1
            elif rc == 2:
                updates += 1
            else:
                # 0 when no changes were applied on duplicate
                unchanged += 1
        except Exception:
            try:
                # Fallback for legacy schema without timestamps
                cursor.execute(
                    """
                    INSERT INTO catalogs (
                        unique_key, title, author, call_number, sublocation, publisher, year,
                        edition, format, content_type, media_type, carrier_type, copies_count,
                        isbn, issn, lccn, subjects, additional_details
                    ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                    ON DUPLICATE KEY UPDATE
                        title=VALUES(title),
                        author=VALUES(author),
                        call_number=VALUES(call_number),
                        sublocation=VALUES(sublocation),
                        publisher=VALUES(publisher),
                        year=VALUES(year),
                        edition=VALUES(edition),
                        format=VALUES(format),
                        content_type=VALUES(content_type),
                        media_type=VALUES(media_type),
                        carrier_type=VALUES(carrier_type),
                        copies_count=VALUES(copies_count),
                        isbn=VALUES(isbn),
                        issn=VALUES(issn),
                        lccn=VALUES(lccn),
                        subjects=VALUES(subjects),
                        additional_details=VALUES(additional_details)
                    """,
                    data,
                )
                rc = cursor.rowcount
                if rc == 1:
                    inserts += 1
                elif rc == 2:
                    updates += 1
                else:
                    unchanged += 1
            except Exception as e2:
                errors += 1
                log_message(f"❌ Insert/Update failed for key={data[0]}: {e2}")
    
    # OPTIMIZATION: Commit in batches instead of after every record
    conn.commit()
    if batch_end < len(records_data):
        progress_pct = int((batch_end / len(records_data)) * 100)
        log_message(f"ℹ️ Progress: {batch_end}/{len(records_data)} records ({progress_pct}%)")

log_message(f"✅ All records processed")

# === STEP 4: Perform deletions if requested ===
deleted = 0
if delete_missing and to_delete_keys:
    log_message(f"ℹ️ Deleting {len(to_delete_keys)} missing records in batches...")
    try:
        # OPTIMIZATION: Delete in batches to avoid too-large queries
        BATCH = 500
        for i in range(0, len(to_delete_keys), BATCH):
            batch = to_delete_keys[i:i+BATCH]
            placeholders = ",".join(["%s"] * len(batch))
            cursor.execute(f"DELETE FROM catalogs WHERE unique_key IN ({placeholders})", batch)
            deleted += cursor.rowcount or 0
            if i + BATCH < len(to_delete_keys):
                log_message(f"ℹ️ Deleted {i + BATCH}/{len(to_delete_keys)} records...")
    except Exception as e:
        log_message(f"⚠️ Deletion failed: {e}")

# OPTIMIZATION: Rebuild fulltext index after bulk operations complete
if index_was_dropped:
    log_message("ℹ️ Rebuilding fulltext index...")
    try:
        cursor.execute(
            """
            ALTER TABLE catalogs 
            ADD FULLTEXT fulltext_catalog_search 
            (title, subjects, additional_details, author, publisher)
            """
        )
        conn.commit()
        log_message("✅ Fulltext index rebuilt successfully")
    except Exception as e:
        log_message(f"⚠️ Failed to rebuild fulltext index: {e}")

conn.commit()
cursor.close()
conn.close()

# === STEP 5: Print summary for the Laravel controller to parse ===
log_message(f"✅ Import completed - Inserted: {inserts}, Updated: {updates}, Unchanged: {unchanged}, Errors: {errors}, Deleted: {deleted}")

summary = {
    "file": input_file,
    "parsed_records": len(records_data),
    "unique_keys": len(new_keys),
    "potential_merges": len(records_data) - len(new_keys),
    "inserted": inserts,
    "updated": updates,
    "unchanged": unchanged,
    "errors": errors,
    "deleted": deleted if delete_missing else 0,
    "missing_count": len(to_delete_keys),
    "deletion_mode": "applied" if delete_missing else "dry-run",
    "last_updated_on": datetime.now(timezone.utc).astimezone().isoformat(),
    "log_file": log_filename,
}

# Write formatted summary to log file for download
log_message("=" * 60, also_print=False)
log_message("Import completed successfully.", also_print=False)
log_message("", also_print=False)
log_message(f"File: {os.path.basename(input_file)}", also_print=False)
log_message("Imported by: [Will be filled by Laravel]", also_print=False)
log_message("", also_print=False)
log_message("Results:", also_print=False)
log_message(f"• {inserts} new records added", also_print=False)
log_message(f"• {updates} existing records updated", also_print=False)
log_message(f"• {unchanged} records unchanged", also_print=False)
deletion_text = f"• {deleted} records deleted" + ("" if delete_missing else " (deletion was disabled)")
log_message(deletion_text, also_print=False)
log_message(f"• {errors} errors encountered", also_print=False)
log_message("", also_print=False)
log_message(f"Total records in file: {len(records_data)}", also_print=False)
log_message(f"Unique records: {len(new_keys)}", also_print=False)
log_message("=" * 60, also_print=False)

log_message("✅ Catalog synchronization complete.")
print("✅ Catalog synchronization complete.")
print("IMPORT_SUMMARY:" + json.dumps(summary))
