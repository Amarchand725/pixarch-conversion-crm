<?php
return [
    'settings' => [
        'company' => [
            'name' => ['value' => 'Pixarch CRM', 'input_type' => 'text'],
            'website_url' => ['value' => 'https://example.com', 'input_type' => 'url'],
            'address' => ['value' => '6600 N Lincoln Ave Ste 316 Acton California 60712 United States.', 'input_type' => 'textarea'],
            'zip_code' => ['value' => '60712', 'input_type' => 'text'],
            'currency_symbol' => ['value' => '$', 'input_type' => 'text'],
            'day_range' => ['value' => 'Mon-Fri', 'input_type' => 'text'],
            'timezone' => ['value' => 'UTC', 'input_type' => 'select'],
            'start_time' => ['value' => '09:00', 'input_type' => 'time'],
            'end_time' => ['value' => '18:00', 'input_type' => 'time'],
        ],
        'branding' => [
            'white_logo' => ['value' => null, 'input_type' => 'file'],
            'black_logo' => ['value' => null, 'input_type' => 'file'],
            'favicon' => ['value' => null, 'input_type' => 'file'],
            'admin_signature' => ['value' => null, 'input_type' => 'file'],
            'slip_stamp' => ['value' => null, 'input_type' => 'file'],
        ],
        'contact' => [
            'phone_number' => ['value' => '+1 234 567 890', 'input_type' => 'text'],
            'support_email' => ['value' => 'support@example.com', 'input_type' => 'email'],
            'sale_email' => ['value' => 'sales@example.com', 'input_type' => 'email'],
            'country' => ['value' => 'United States', 'input_type' => 'text'],
            'state' => ['value' => 'California', 'input_type' => 'text'],
            'city' => ['value' => 'Acton', 'input_type' => 'text'],
        ],
        'social' => [
            'facebook_link' => ['value' => 'https://facebook.com/company', 'input_type' => 'url'],
            'instagram_link' => ['value' => 'https://instagram.com/company', 'input_type' => 'url'],
            'linked_in_link' => ['value' => 'https://linkedin.com/company', 'input_type' => 'url'],
            'twitter_link' => ['value' => 'https://twitter.com/company', 'input_type' => 'url'],
        ],
        'location' => [
            'location_map_url' => ['value' => 'https://maps.google.com/?q=6600+N+Lincoln+Ave', 'input_type' => 'url'],
        ],
        'notifications' => [
            'admin_notification_enabled' => ['value' => true, 'input_type' => 'checkbox'],
            'user_notification_enabled' => ['value' => false, 'input_type' => 'checkbox'],
        ],
        'customer_info' => [
            'enabled' => ['value' => true, 'input_type' => 'checkbox'],
        ],
    ],
];