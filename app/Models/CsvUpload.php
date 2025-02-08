<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',         // Add this line to allow mass assignment
        'file_path',         // Add this line to allow mass assignment
        'vendor',         // Add this line to allow mass assignment
        'hash',         // Add this line to allow mass assignment
        'status',
        'total_rows',
        'processed_rows',
        'error_message',
        'message',
        'uploaded_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
