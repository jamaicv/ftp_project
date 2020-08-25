<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'homeworks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename', 'location', 'student_id', 'corrector_id', 'corrected'
    ];

    public function student()
    {
        return $this->hasOne('App\User', 'id', 'student_id');
    }
}
