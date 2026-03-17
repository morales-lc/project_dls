<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetzoneSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'images',
        'reminders',
    ];

    protected $casts = [
        'images' => 'array',
        'reminders' => 'array',
    ];

    /**
     * Get the singleton instance of Netzone settings
     */
    public static function get()
    {
        return static::firstOrCreate([
            'id' => 1
        ], [
            'title' => 'Netzone',
            'description' => 'The College Library provides 30 terminals for the use of students. The Senior High School has its own Internet Room and has 10 terminals for use. In addition, there are spaces for students to use their laptops or notebooks.',
            'images' => ['images/netzone1.png', 'images/netzone2.png'],
            'reminders' => [
                ['text' => 'BE RESPECTFUL! Always treat the computer lab equipment AND your teacher and classmates the way that you would want your belongings and yourself to be treated.', 'type' => 'danger'],
                ['text' => 'No food or drinks near the computers. NO EXCEPTIONS.', 'type' => 'danger'],
                ['text' => 'Enter the Netzone quietly and work quietly. There are other individuals who may be using the Netone. Please be respectful.', 'type' => 'warning'],
                ['text' => 'Surf safely! Only visit assigned websites. Some web links can contain viruses or malware. Others may contain inappropriate content.', 'type' => 'warning'],
                ['text' => 'Clean up your work area before you leave.', 'type' => 'warning'],
                ['text' => 'Do not change computer settings or backgrounds.', 'type' => 'warning'],
                ['text' => 'For your saving and printing needs, proceed to the Concierge.', 'type' => 'warning'],
            ],
        ]);
    }
}
