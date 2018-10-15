<?php

namespace Tohidplus\Zarrinpal\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ZarrinpalLog extends Model
{
    protected $fillable=[
        'authority',
        'price',
        'status',
        'status_code',
        'ref_id'
    ];

    public function scopeSuccessful(Builder $builder)
    {
        return $builder->where('status','successful');
    }

    public function scopeUnsuccessful(Builder $builder)
    {
        return $builder->where('status','unsuccessful');
    }

    public function scopePending(Builder $builder)
    {
        return $builder->where('status','pending');
    }

    public function scopeCanceled(Builder $builder)
    {
        return $builder->where('status','canceled');
    }
}
