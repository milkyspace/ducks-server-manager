<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Vpnuser extends Model
{
    use HasFactory, Notifiable;

    public function getRouteKeyName()
    {
        return 'tg_id';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tg_id', 'name',
    ];
}
