<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ManageSerial extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'batch_number', 'serial_number', 'barcode_path', 'is_link', 'uploaded_by'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
