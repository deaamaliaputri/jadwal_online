<?php

namespace App\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subjects
 * @package App\Domain\Entities
 */
class Subjects extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'teachers_id', 'descriptions',
    ];

}
