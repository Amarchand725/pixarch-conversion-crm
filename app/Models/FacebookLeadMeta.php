<?php

namespace App\Models;

use App\Modules\Lead\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\ModelTrait;

class FacebookLeadMeta extends Model
{
    use ModelTrait;
    
    protected $table = 'facebook_lead_meta';
    protected $fillable = [
        'lead_id',
        'leadgen_id',
        'form_id',
        'page_id',
        'raw_payload',
        'received_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'received_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}