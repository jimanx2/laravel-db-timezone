# laravel-db-timezone
(Experimental) Laravel package to handle date time with non-UTC databases
> Note: I made this only to support mysql driver as of now

# Installation
1. Add the following into `repositories` section in `composer.json`
```json
{
    "type": "package",
    "package": {
        "name": "jimanx2/laravel-db-timezone",
        "version": "0.0.1",
        "type":"package",
        "source": {
            "url": "https://github.com/jimanx2/laravel-db-timezone",
            "type": "git",
            "reference": "main"
        }
    }
}
```
2. `composer require jimanx2/laravel-db-timezone`

# Usage

1. Register provider `Jimanx2\LaravelDbTimezone\Providers\Provider` under config/app.php (Laravel 5+ can skip this)
2. Run `php artisan vendor:publish --provider=Jimanx2\LaravelDbTimezone\Providers\Provider` to copy `config/dbtz.php`
3. Add additional model search paths into `config/dbtz.php` as needed
4. After this, all models will have their created_at, updated_at timezone auto converted into your `config('app.timezone')` value

> Note: if your model have additional columns other than created_at / updated_at, please add it into your model's $casts attribute

# How does it look?

```sh
php artisan tinker
>>> $m = Sample::create(["value" => "12345"]);
=> App\Auxiliary\Models\Sample {#4034
     value: "12345",
     created_at: "2023-02-04 08:45:32",
     updated_at: "2023-02-04 08:45:32",
     id: 2,
   }
>>> $m->fresh();
=> App\Auxiliary\Models\Sample {#4185
     id: 2,
     value: 12345,
     collected_at: "2023-02-04 08:45:32", // $model->casts["collected_at"] = "datetime";
     created_at: "2023-02-04 08:45:32",
     updated_at: "2023-02-04 08:45:32",
   }
>>> $m->value = "23456";
>>> $m->save();
>>> $m->fresh();
=> App\Auxiliary\Models\Sample {#4194
     id: 2,
     value: 23456,
     collected_at: "2023-02-04 08:45:32",
     created_at: "2023-02-04 08:45:32",
     updated_at: "2023-02-04 08:47:16",
   }
>>> # Inside database it will look like this
>>> # Notice the timezone difference (my test database has +0800 offset)
>>> DB::select(DB::raw("SELECT * FROM samples"))
=> [
     {#3254
       +"id": 2,
       +"value": 23456,
       +"collected_at": "2023-02-04 16:45:32",
       +"created_at": "2023-02-04 16:45:32",
       +"updated_at": "2023-02-04 16:47:16",
     },
   ]
```