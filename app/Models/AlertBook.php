<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'call_number',
        'author',
        'pdf_path',
        'cover_image',
        'department_id',
        'month',
        'year',
    ];

    public function department()
    {
        return $this->belongsTo(AlertDepartment::class, 'department_id');
    }
}
