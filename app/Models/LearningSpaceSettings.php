<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningSpaceSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'images',
        'content_sections',
    ];

    protected $casts = [
        'images' => 'array',
        'content_sections' => 'array',
    ];

    /**
     * Get the singleton instance of Learning Space settings
     */
    public static function get()
    {
        return static::firstOrCreate([
            'id' => 1
        ], [
            'title' => 'Learning Spaces',
            'description' => 'Learning spaces are areas in the library designed to support different learning activities, from individual study to group collaboration. These spaces are equipped with resources and facilities to enhance the learning experience of students and faculty.',
            'images' => ['images/IMG_1462.JPG', 'images/10.png'],
            'content_sections' => [
                [
                    'heading' => 'Types of Learning Spaces',
                    'type' => 'list',
                    'items' => [
                        'Individual Study Areas: Quiet zones for focused, independent work.',
                        'Group Study Rooms: Spaces for collaborative projects and discussions.',
                        'Technology Zones: Areas equipped with computers and multimedia tools.',
                        'Flexible Seating: Comfortable seating arrangements for reading and relaxation.',
                    ]
                ],
                [
                    'heading' => 'How to Reserve a Space',
                    'type' => 'numbered',
                    'items' => [
                        'Visit the library\'s main desk or use the online reservation system.',
                        'Choose the type of space and time slot you need.',
                        'Provide your student or faculty ID for verification.',
                        'Enjoy your reserved learning space!',
                    ]
                ],
            ],
        ]);
    }
}
