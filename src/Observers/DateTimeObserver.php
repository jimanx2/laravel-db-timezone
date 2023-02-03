<?php namespace Jimanx2\LaravelDbTimezone\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class DateTimeObserver
{
    private function dbtz()
    {
        return config("database.connections.".config("database.default").".timezone");
    }
    
    private function getDatetimeAttributes(Model $model) {
        $out = [];
        foreach (Schema::getColumnListing($model->getTable()) as $key) {
            if ($model->$key instanceof Carbon) {
                $out[] = $key;
            }
        }
        return $out;
    }

    public function created(Model $model) {
        foreach ($this->getDatetimeAttributes($model) as $key) {
            $model->$key = (new Carbon($model->$key . " " . $this->dbtz()))->tz(config('app.timezone'));
        }
    }

    public function saving(Model $model)
    {
        foreach ($this->getDatetimeAttributes($model) as $key) {
            if (in_array($key, [Model::CREATED_AT, Model::UPDATED_AT])) {
                continue;
            }
            if ($model->$key->getTimezone()->getName() == config('app.timezone')) {
                $model->$key = $model->$key->tz($this->dbtz());
            }
        }
        if (is_null($model->{Model::CREATED_AT})) {
            $model->setCreatedAt(Carbon::now()->tz($this->dbtz()));
        }
        $model->setUpdatedAt(Carbon::now()->tz($this->dbtz()));
    }

    public function retrieved(Model $model)
    {
        foreach ($this->getDatetimeAttributes($model) as $key) {
            $model->$key = (new Carbon($model->$key . " " . $this->dbtz()))->tz(config('app.timezone'));
        }
    }
}