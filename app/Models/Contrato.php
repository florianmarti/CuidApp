<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'zona_id',
        'vigilador_id',
        'start_date',
        'end_date',
        'type',
        'status',
        'is_working',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_working' => 'boolean',
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }
    public function vigilador()
    {
        return $this->belongsTo(User::class, 'vigilador_id');
    }
}
