<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidlakJournal extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'month', 'year', 'cover_photo', 'print_issn'];

    public function articles() {
        return $this->hasMany(SidlakArticle::class);
    }

    public function editors() {
        return $this->hasMany(SidlakJournalEditor::class);
    }

    public function peerReviewers() {
        return $this->hasMany(SidlakJournalPeerReviewer::class);
    }
}
