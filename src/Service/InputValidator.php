<?php

namespace App\Service;

class InputValidator
{
    public function validateDate(string $date): bool
    {
        return \DateTime::createFromFormat('Y-m-d', $date) && $date <= (new \DateTime())->format('Y-m-d');
    }
}
