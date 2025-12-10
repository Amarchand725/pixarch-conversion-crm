<?php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute must be a valid email address.',
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],

    'attributes' => [
        'name' => __('labels.name'),
        'email' => __('labels.email'),
        'password' => __('labels.password'),
        'phone' => __('labels.phone'),
        'status' => __('labels.status'),
    ],
];
