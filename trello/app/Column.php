<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    protected $fillable = ['name', 'position'];

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
