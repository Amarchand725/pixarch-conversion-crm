<?php

namespace App\Services;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class PhoneNumberService
{
    protected $phoneUtil;

    public function __construct()
    {
        $this->phoneUtil = PhoneNumberUtil::getInstance();
    }

    /**
     * Parse and normalize a phone number
     *
     * @param string $phoneNumber
     * @return array
     *  - 'e164' => full number (+14155552671)
     *  - 'numeric_code' => +1
     *  - 'iso_code' => US
     * @throws \Exception if invalid
     */
    public static function parse(string $phoneNumber): array
    {
        $clean = preg_replace('/[^+\d]/', '', $phoneNumber); // remove spaces, parentheses, etc.
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $number = $phoneUtil->parse($clean, null);

            if (!$phoneUtil->isValidNumber($number)) {
                throw new \Exception('Invalid phone number');
            }

            return [
                'e164' => $phoneUtil->format($number, PhoneNumberFormat::E164),
                'numeric_code' => '+' . $number->getCountryCode(),
                'iso_code' => $phoneUtil->getRegionCodeForNumber($number),
            ];
        } catch (NumberParseException $e) {
            throw new \Exception('Invalid phone number format');
        }
    }
}