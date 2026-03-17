<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidlakJournalPeerReviewer extends Model
{
    use HasFactory;
    protected $fillable = ['sidlak_journal_id', 'name', 'title', 'institution', 'city'];
    public function journal() {
        return $this->belongsTo(SidlakJournal::class);
    }
}
