<?php
namespace App\Classes;

use CyberWings\MyanmarPhoneNumber;

class CustomMyanmarPhoneNumber extends MyanmarPhoneNumber
{
    // Override the add_prefix method to fix the regex
    public function add_prefix($number)
    {
        // Use the original is_valid() method from the parent class
        if ($this->is_valid($number)) {
            // Correct the regex to only replace the first occurrence
            return preg_replace('/^(?:\+?959|09)/', '959', $number);
        }

        return false;
    }
}
