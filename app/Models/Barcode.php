<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',          // Barcode data (e.g., product code)
        'barcode_svg',   // Generated SVG of the barcode
    ];
}
