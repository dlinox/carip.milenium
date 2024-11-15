<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDescription extends Model
{
    use HasFactory;
    protected $table        = 'purchase_descriptions';
    protected $primaryKey   = 'id';
    protected $fillable     = 
    [
        'descripcion'
    ];
}
