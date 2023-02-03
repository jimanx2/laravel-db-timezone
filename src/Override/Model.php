<?php namespace Jimanx2\LaravelDbTimezone\Override;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel {

    public function setUpdatedAt($value)
    {
        dump('here');
        $this->{static::UPDATED_AT} = $value;

        return $this;
    }
}