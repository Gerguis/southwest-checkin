<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    protected $fillable = ['payload', 'status'];

    public function account()
    {
        return $this->hasMany(SouthwestAccount::class);
    }

    public function setPayloadAttribute($value)
    {
        $this->attributes['payload'] = json_encode($value);
    }

    public function getPayloadAttribute($value)
    {
        return json_decode($value, true);
    }
}
