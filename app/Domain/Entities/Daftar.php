<?php

namespace App\Domain\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Daftar
 * @package App\Domain\Entities
 */
class Daftar extends Authenticatable

{
    use Notifiable;



    /**
     * @var array
     */
    //  protected $table = 'daftar';
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'status', 'level', 'nip',	
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
}