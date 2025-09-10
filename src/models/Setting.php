<?php

namespace Agunbuhori\SettingsManager\Models;

use Agunbuhori\SettingsManager\SettingsBagManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = ['bag', 'group', 'key', 'type', 'value'];

    /**
     * The attributes that should be hidden for serialization.
     * 
     * @var array
     */
    public $hidden = ['created_at', 'updated_at', 'id'];

    /**
     * The "booted" method of the model.
     * 
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope('bag', function (Builder $builder) {
            $builder->where('bag', app(SettingsBagManager::class)->getBag());
        });
        
        static::addGlobalScope('group', function (Builder $builder) {
            $builder->where('group', app(SettingsBagManager::class)->getGroup());
        });

    }

    /**
     * Get the value of the setting.
     * 
     * @return Attribute
     */
    public function value(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => match ($this->type) {
                'array' => $value ? json_decode($value, true) : [],
                'float' => floatval($value),
                'integer' => intval($value),
                'boolean' => boolval($value),
                'string' => strval($value),
                default => $value,
            },
            set: fn ($value) => is_array($value) ? json_encode($value) : $value
        );
    }
}