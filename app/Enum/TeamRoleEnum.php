<?php

namespace App\Enum;


enum TeamRoleEnum: string
{
    case LEAD = 'lead';
    
    case MEMBER = 'member';

    public function label(): string
    {
        return match ($this) {
            self::LEAD => 'Team Lead',
            self::MEMBER => 'Team Member',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        $data = [];

        foreach (self::cases() as $case) {
            $data[$case->value] = $case->label();
        }

        return $data;
    }
   
}
