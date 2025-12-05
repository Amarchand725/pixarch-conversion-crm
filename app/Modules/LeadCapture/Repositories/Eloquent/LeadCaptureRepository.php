<?php

namespace App\Modules\LeadCapture\Repositories\Eloquent;

use App\Modules\LeadCapture\Models\CaptureFormField;
use App\Repositories\Eloquent\BaseRepository;
use App\Modules\LeadCapture\Repositories\Contracts\LeadCaptureContract;
use App\Modules\LeadCapture\Models\LeadCapture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LeadCaptureRepository extends BaseRepository implements LeadCaptureContract
{
    public function __construct(LeadCapture $model)
    {
        parent::__construct($model);
    }

    public function storeModel(array $payload): Model
    {
        $model = $this->model;
        $model->toFill($payload, ['fields']);
        $model->save();

        $model->shareable_link = route('lead-capture.public', $model->uuid);
        $model->save();

        if (!empty($payload['fields'])) {
            // Save multiple fields
            foreach ($payload['fields'] as $field) {
                if ($field['type'] === 'select' && !empty($field['options'])) {
                    $field['options'] = array_map('trim', explode(',', $field['options']));
                } else {
                    $field['options'] = null;
                }
                $model->fields()->create([
                    'label' => $field['label'],
                    'name' => Str::snake($field['label']),
                    'type' => $field['type'],
                    'placeholder' => $field['placeholder'] ?? null,
                    'required' => $field['required'] ?? 0,
                    'options' => $field['options'] ?? null,
                ]);
            }
        }

        return $model;
    }

    public function updateModel(Model $model, array $payload): Model
    {
        // Fill model attributes except fields
        $model->toFill($payload, ['fields']);
        $model->save();

        if (!empty($payload['fields'])) {

            $existingIds = collect($payload['fields'])
                ->pluck('id')
                ->filter() // keep only non-null IDs
                ->toArray();

            // Delete fields that were removed in the form
            $model->fields()->whereNotIn('id', $existingIds)->delete();

            // Loop through fields
            foreach ($payload['fields'] as $field) {
                if (!empty($field['id'])) {
                    // Update existing field
                    $model->fields()->where('id', $field['id'])->update([
                        'label' => $field['label'],
                        'name' => Str::snake($field['label']),
                        'type' => $field['type'],
                        'placeholder' => $field['placeholder'] ?? null,
                        'required' => $field['required'] ?? 0,
                        'options' => $field['options'] ?? null,
                    ]);
                } else {
                    // Create new field
                    $model->fields()->create([
                        'label' => $field['label'],
                        'name' => Str::snake($field['label']),
                        'type' => $field['type'],
                        'placeholder' => $field['placeholder'] ?? null,
                        'required' => $field['required'] ?? 0,
                        'options' => $field['options'] ?? null,
                    ]);
                }
            }
        } else {
            // If no fields submitted, remove all existing
            $model->fields()->delete();
        }

        return $model;
    }
}