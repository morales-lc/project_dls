<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $fillable = [
        'phone_college',
        'phone_graduate',
        'phone_senior_high',
        'phone_ibed',
        'facebook_url',
        'email',
        'website_url',
    ];
}
