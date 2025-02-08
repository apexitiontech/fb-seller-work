<?php

namespace App\Models;

use App\Models\ManageSerial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'status'];

    public function serials()
    {
        return $this->hasMany(ManageSerial::class);
    }
}
