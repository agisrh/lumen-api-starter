<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Fungsi str untuk uuid
use Ramsey\Uuid\Uuid;

class Log extends Model
{
    protected $table        = "log_devices";
    protected $primaryKey   = 'id';
    public $incrementing    = false;
    public $timestamps      = false;

    
    /**
     * The "booting" function of model
     *
     * @return void
     */
    protected static function boot() {
        static::creating(function ($model) {
            if ( ! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Uuid::uuid4();
            }
        });
    }


    protected $guarded = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    // protected $hidden = [
    //     'deleted_at'
    // ];


    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
