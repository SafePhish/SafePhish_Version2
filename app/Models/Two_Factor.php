<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Two_Factor extends Model
{
    protected $table = 'two_factor_codes';

    protected $primaryKey = 'Id';

    protected $fillable = ['UserId',
        'Ip',
        'Code'];
}