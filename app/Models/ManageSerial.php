<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManageSerial extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'serial_number',
        'batch_number', 
        "is_link",
        'uploaded_by'
    ];
}
