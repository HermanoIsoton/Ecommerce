<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Address extends Model
{
     protected $fillable = [
        'user_id',
        'recipient',
        'cep',
        'street',
        'number',
        'complement',
        'district',
        'city',
        'state'
    ];
    public $timestamps = false;

    public function user(): belongsTo{
        return $this->belongsTo(User::class);
    }
}
