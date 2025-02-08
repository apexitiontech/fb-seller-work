<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'total_rows',
        'hash',
        'uploaded_by',
    ];
}
