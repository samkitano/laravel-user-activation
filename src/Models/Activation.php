<?php

namespace Kitano\UserActivation\Models;

use Illuminate\Database\Eloquent\Model;

class Activation extends Model
{
    /** @var string */
    protected $table = 'activations';

    /** @var array */
    protected $fillable = ['user_id', 'token', 'created_at'];

    /** @var string */
    protected $primaryKey = 'token';

    /** @var bool */
    public $incrementing = false;

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $dates = ['created_at'];
}
