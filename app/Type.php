<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public const TYPE1 = "type1";
    public const TYPE2 = "type2";
    public const TYPE3 = "type3";
    public const TYPE4 = "type4";

    public const TYPES = [
        'TYPE1' => self::TYPE1,
        'TYPE2' => self::TYPE2,
        'TYPE3' => self::TYPE3,
        'TYPE4' => self::TYPE4,
    ];

    public const MAX_TYPE = 4;

    protected $table = 'result_type';
}
