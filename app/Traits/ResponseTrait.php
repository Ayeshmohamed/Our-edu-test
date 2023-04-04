<?php
namespace App\Traits;

trait ResponseTrait{

    public function json_response($data,$status,$message)
    {
        $data = [
            'data' => $data,
            'status' => $status,
            'message' => $message
        ];

        return response()->json($data);
    }
}
