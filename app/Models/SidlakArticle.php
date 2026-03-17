<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidlakArticle extends Model
{
    use HasFactory;
    protected $fillable = ['sidlak_journal_id', 'title', 'authors', 'pdf_file'];
    public function journal() {
        return $this->belongsTo(SidlakJournal::class);
    }
}
