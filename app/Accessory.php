<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{

  protected $fillable = [
      'title', 'price', 'category', 'admin_id', 
  ];

  public function admin() {
    return $this->belongsTo('App\Admin');
  }
}
