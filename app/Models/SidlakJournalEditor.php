<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidlakJournalEditor extends Model
{
    use HasFactory;
    protected $fillable = ['sidlak_journal_id', 'name', 'title'];
    public function journal() {
        return $this->belongsTo(SidlakJournal::class);
    }
}
