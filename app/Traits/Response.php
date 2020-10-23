<?php
namespace App\Traits;

trait Response {

    /**
     * @method success - return a success json response
     *
     * @param json | string $data
     * @param string $message
     * @return json
     */
    public function success($data = null, $message = null, $status) {
        return response()->json([
            "message"   => $message,
            "data"      => $data
        ], $status);
    }

    /**
     * @method error - return a error json response
     *
     * @param string $message
     * @return json
     */
    public function error($error = null, $message = null, $status = 401){
        return response()->json([
            "message"   => $message,
            "error"     => $error
        ], $status);
    }
}
