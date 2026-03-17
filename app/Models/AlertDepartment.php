<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertDepartment extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type'];

    public function books()
    {
        return $this->hasMany(AlertBook::class, 'department_id');
    }
}
