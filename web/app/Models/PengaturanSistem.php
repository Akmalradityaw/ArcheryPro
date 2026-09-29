<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    protected $table = 'pengaturan_sistem';

    public $timestamps = false;

    protected $fillable = ['key_setting', 'value_setting', 'keterangan'];

    public static function getValue(string $key, $default = null): ?string
    {
        $setting = static::where('key_setting', $key)->first();

        return $setting ? $setting->value_setting : $default;
    }
}
