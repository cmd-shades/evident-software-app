<?php

namespace App\Models\Web;

final class TestsModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * splat operation
    */

    public function get_invoice_total(...$x)
    {
        $result = 0;
        foreach ($sum as $num) {
            $sum += $num;
        }
        return $result;
    }
}
