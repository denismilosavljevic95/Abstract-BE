<?php

namespace App\Helpers;

class Helpers
{
    public function response($data, $status) {
        $response = [];
        $code = 200;
        if (is_numeric($data) && $data == 0) {
            $response = ["Message" => "Bad request!"];
            $code = 400;
        } elseif (is_numeric($data)) {
            $response = ["Message" => "Successfully!"];
            $code = 200;
        } else {
            $response = $data;
            $code = $status;
        }
        return response($response, $code);
    }
}