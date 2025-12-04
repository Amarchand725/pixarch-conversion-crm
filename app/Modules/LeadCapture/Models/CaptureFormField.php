<?php

namespace App\Modules\LeadCapture\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaptureFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_capture_id',
        'label',
        'name',
        'type',
        'placeholder',
        'required',
        'options',
        'order',
    ];

    protected $casts = [
        'required' => 'boolean',
        'options'  => 'array',
    ];

    /**
     * Relationship: A form field belongs to a lead capture
     */
    public function leadCapture()
    {
        return $this->belongsTo(LeadCapture::class, 'lead_capture_id');
    }

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\CaptureFormFieldFactory::new();
    }
}