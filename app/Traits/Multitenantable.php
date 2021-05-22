<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Multitenantable {

  protected static function bootMultitenantable()
  {
    if (auth()->check()) {
      static::addGlobalScope('user_id', function (Builder $builder) {
        $builder->where('user_id', auth()->user()->id);
      });
    }
  }

}