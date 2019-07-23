<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cake extends Model
{
    public function admin() {
      return $this->belongsTo('App\Admin');
    }

    protected $fillable = [
        'name', 'price', 'main_category', 'sub_category', 'weights',
    ];

    protected $casts = [
      'weights' => 'array', 
    ];
}
