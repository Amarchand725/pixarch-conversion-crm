<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityRelationship extends Model
{
    public $timestamps = false; // optional, since pivot
    protected $fillable = ['user_id', 'model_type', 'model_id', 'created_at', 'updated_at'];

    public function model()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
