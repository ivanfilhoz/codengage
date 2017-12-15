<?php

namespace App\Tool;

class Formatter
{
    public function formatCurrency(float $value)
    {
        return 'R$ ' . str_replace('.', ',', number_format($value, 2));
    }

    public function formatDate(\DateTime $date)
    {
        return $date->format('d/m/Y');
    }
}
