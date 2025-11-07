<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanningServiceSettings extends Model
{
    protected $fillable = [
        'title',
        'images',
        'steps',
        'important_note',
        'scanning_request_note',
        'extract_limits',
    ];

    protected $casts = [
        'images' => 'array',
        'steps' => 'array',
    ];

    /**
     * Get singleton instance
     */
    public static function get()
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'Scanning Service',
                'images' => [],
                'steps' => [
                    'Log in using your <strong>@lccdo.edu.ph</strong> email.',
                    'Search the catalog you want to scan: <a href="/dashboard" target="_blank">Search Here</a>',
                    'Select your desired catalog/book and press <em>"Request Scan"</em> button. This will redirect you to the LiRA Web Form with fields pre-filled.',
                    '<span style="color:#e83e8c;">An email will be sent confirming the request as well as the Table of Contents so that you can choose the specific chapter/article needed</span>',
                    '<span style="color:#e83e8c;">When the digital file is available, an email notification will be sent with a link for downloading.</span><br><span style="color:#333;">The library will aim to provide you with a copy of the item in digital format within an average of three working days from the date it is requested.<br>Exact time may vary based on the size of the file to be scanned.</span>',
                ],
                'important_note' => 'The Library Scanning Service is available only to the Lourdes College community.<br><br>Scanning request applies only to books and journals available at the Learning Commons, including the Graduate Library and Integrated Basic Education Libraries.',
                'scanning_request_note' => '',
                'extract_limits' => 'Normal extract limits pursuant to Fair Use<br><em>Up to one chapter of a book or 10% of the total, whichever is greater</em><br><em>Up to one article from one journal issue or 10% of the total, whichever is greater</em>',
            ]
        );
    }
}
