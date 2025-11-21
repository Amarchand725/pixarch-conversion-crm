<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\BusinessSetting\Models\BusinessSetting;

class BusinessSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = config('system.settings');

        $businessSetting = new BusinessSetting();
        foreach ($settings as $category => $group) {
            if (is_array($group)) {
                foreach ($group as $key => $item) {
                    $value = is_array($item) ? $item['value'] ?? null : $item;
                    $inputType = is_array($item) ? $item['input_type'] ?? 'text' : 'text';

                    $businessSetting->firstOrCreate(
                        ['key' => $key],
                        [
                            'category' => $category,
                            'value' => $value,
                            'input_type' => $inputType,
                        ]
                    );
                }
            } else {
                $businessSetting->firstOrCreate(
                    ['key' => $category],
                    [
                        'category' => null,
                        'value' => $group,
                        'input_type' => 'text',
                    ]
                );
            }
        }
    }
}