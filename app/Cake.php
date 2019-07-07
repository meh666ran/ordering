<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cake extends Model
{
    public function admin() {
      return $this->belongsTo('App\Admin');
    }
}
