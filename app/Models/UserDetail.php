<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'from_name',
        'from_company',
        'from_phone',
        'from_address1',
        'from_address2',
        'from_city',
        'from_state',
        'from_postcode',
        'from_country',
        'to_name',
        'to_company',
        'to_phone',
        'to_address1',
        'to_address2',
        'to_city',
        'to_state',
        'to_postcode',
        'to_country',
        'length',
        'width',
        'height',
        'weight',
        'notes',
        'barcode_path_gs128',
        'barcode_path_gs1_datamatrix',
        'is_link',
    ];

    // Define the inverse relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
