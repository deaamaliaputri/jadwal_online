<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Daftar
 * @package App\Domain\Entities
 */
class Daftar extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
     protected $table = 'daftar';
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'status', 'level', 'nip',	
    ];
 protected $hidden = [
    'deleted_at'
    ];
}