<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable = ['key', 'label', 'color', 'sort_order', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const COLORS = [
        'slate' => 'bg-slate-100 text-slate-600',
        'yellow' => 'bg-yellow-100 text-yellow-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'blue' => 'bg-blue-100 text-blue-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
        'purple' => 'bg-purple-100 text-purple-700',
        'pink' => 'bg-pink-100 text-pink-700',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'cyan' => 'bg-cyan-100 text-cyan-700',
        'red' => 'bg-red-100 text-red-600',
    ];

    protected function badgeClass(): Attribute
    {
        return Attribute::get(fn () => self::COLORS[$this->color] ?? self::COLORS['slate']);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public static function defaultKey(): string
    {
        return static::where('is_default', true)->value('key') ?? static::ordered()->value('key') ?? 'pending';
    }
}
