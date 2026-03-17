<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookBorrowingSettings extends Model
{
    protected $fillable = [
        'title',
        'images',
        'borrowing_steps',
        'returning_steps',
    ];

    protected $casts = [
        'images' => 'array',
        'borrowing_steps' => 'array',
        'returning_steps' => 'array',
    ];

    /**
     * Get singleton instance
     */
    public static function get()
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'Online Borrowing & Returning',
                'images' => [],
                'borrowing_steps' => [
                    'Log in using your <strong>@lccdo.edu.ph</strong> email.',
                    'Search the catalog you want to borrow: <a href="/dashboard" target="_blank">Search Here</a>',
                    'Select your desired catalog/book and press <em>"Request Borrow"</em> button. This will redirect you to the LiRA Web Form with fields pre-filled.<br><sub>The library staff will endeavor to process your request within 3–5 working days.</sub>',
                    'You will be notified when the material(s) can be picked up through your contact details provided in LiRA.',
                    'When notified, proceed to the designated area indicated by LiRA. Sign the two book receipts and drop one copy in the designated Drop Box.',
                ],
                'returning_steps' => [
                    'Return materials on the specified due date indicated in the book receipt.',
                    'Drop the returned material(s) in the Return Drop Box located outside the Learning Commons entrance or at the Guard House. Notices will be posted at the Guard House for instructions.',
                    'You will receive an email confirmation once the returned material(s) are recorded.',
                ],
            ]
        );
    }
}
