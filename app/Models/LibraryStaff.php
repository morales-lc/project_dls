<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LibraryStaff extends Model
{
    protected $table = 'library_staff';
    protected $fillable = [
        'prefix', 'first_name', 'middlename', 'last_name', 'role', 'email', 'photo', 'description', 'department'
    ];
}
