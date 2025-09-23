<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    public $timestamps = false;
    protected $fillable = ['parametro','valor'];

    public static function get(string $param, $default = null)
    {
        $row = static::where('parametro', $param)->first();
        return $row ? $row->valor : $default;
    }

    public static function set(string $param, string $valor): void
    {
        static::updateOrCreate(['parametro'=>$param], ['valor'=>$valor]);
    }
}