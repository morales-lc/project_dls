from pymarc import MARCReader
import mysql.connector
import re
import sys
import hashlib

# === MYSQL CONNECTION ===
conn = mysql.connector.connect(
    host="127.0.0.1",
    user="root",
    password="",
    database="dls_project"
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
    if alters:
        cursor.execute("ALTER TABLE catalogs " + ", ".join(alters))
        conn.commit()
except Exception as e:
    # Non-fatal; continue without timestamps if privilege/schema issues
    print(f"⚠️ Skipping timestamp column ensure: {e}")

# === BACKFILL timestamps for legacy rows (set created_at/updated_at if NULL) ===
try:
    cursor.execute("UPDATE catalogs SET created_at = NOW() WHERE created_at IS NULL")
    cursor.execute("UPDATE catalogs SET updated_at = created_at WHERE updated_at IS NULL AND created_at IS NOT NULL")
    conn.commit()
except Exception as e:
    print(f"⚠️ Skipping timestamp backfill: {e}")

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
            print("ℹ️ Added FULLTEXT index 'fulltext_catalog_search' on catalogs.")
        except Exception as e:
            print(f"⚠️ Could not add FULLTEXT index automatically: {e}")
except Exception as e:
    # If information_schema is unavailable or permissions restricted, proceed without failing
    print(f"⚠️ Skipping FULLTEXT index check: {e}")

# === FORMATTER FUNCTIONS ===
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

# === READ FILE ARGUMENT ===
if len(sys.argv) < 2:
    print("❌ No MARC file provided")
    sys.exit(1)
input_file = sys.argv[1]

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

        # === Use LCCN if available, otherwise hash of title+author ===
        unique_key = lccn if lccn else hashlib.md5((title + author).encode("utf-8")).hexdigest()
        new_keys.add(unique_key)

        # Collect all data for insert/update
        call_number = (
            get_field_concat(record, "090", ["a", "b"]) or
            get_field_concat(record, "082", ["a", "b"]) or
            get_field_concat(record, "852", ["h", "i"]) or
            get_field_concat(record, "050", ["a", "b"])
        )
        sublocation = get_field(record, "852", "b")
        publisher = get_field_concat(record, "260", ["a", "b"]) or get_field_concat(record, "264", ["a", "b"])
        year = get_field(record, "260", "c") or get_field(record, "264", "c")
        edition = get_field(record, "250", "a")
        format_ = get_field_concat(record, "300", ["a", "b", "c"])
        content_type = get_field(record, "336", "a")
        media_type = get_field(record, "337", "a")
        carrier_type = get_field(record, "338", "a")
        isbn = format_isbn(get_field(record, "020", "a"))
        issn = format_issn(get_field(record, "022", "a"))
        subjects = "; ".join([f["a"] for f in record.get_fields("650") if "a" in f])

        extras = []
        for tag in ["246", "490", "500", "504", "505", "520", "700", "740"]:
            for f in record.get_fields(tag):
                if "a" in f:
                    extras.append(f["a"])
        additional_details = "\n".join(extras) if extras else None

        records_data.append((
            unique_key, title, author, call_number, sublocation, publisher, year,
            edition, format_, content_type, media_type, carrier_type,
            isbn, issn, lccn, subjects, additional_details
        ))

# === STEP 2: Keep All Existing Records (No Delete) ===
print("ℹ️ Skipping deletion step. Existing records will be preserved.")

# === STEP 3: Insert or Update Records ===
for data in records_data:
    try:
        cursor.execute(
            """
            INSERT INTO catalogs (
                unique_key, title, author, call_number, sublocation, publisher, year,
                edition, format, content_type, media_type, carrier_type,
                isbn, issn, lccn, subjects, additional_details, created_at, updated_at
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
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
                isbn=VALUES(isbn),
                issn=VALUES(issn),
                lccn=VALUES(lccn),
                subjects=VALUES(subjects),
                additional_details=VALUES(additional_details),
                updated_at=NOW()
            """,
            data,
        )
    except Exception as e:
        # Fallback for legacy schema without timestamps
        cursor.execute(
            """
            INSERT INTO catalogs (
                unique_key, title, author, call_number, sublocation, publisher, year,
                edition, format, content_type, media_type, carrier_type,
                isbn, issn, lccn, subjects, additional_details
            ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
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
                isbn=VALUES(isbn),
                issn=VALUES(issn),
                lccn=VALUES(lccn),
                subjects=VALUES(subjects),
                additional_details=VALUES(additional_details)
            """,
            data,
        )

conn.commit()
cursor.close()
conn.close()

print("✅ Catalog successfully synchronized (insert/update only, no deletions)!")
