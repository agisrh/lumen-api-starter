<?php


/**
* generate PIN
*
* @param $length
*/
function generatePin( $length ) {
    
    // Generate set of alpha characters
    $alpha = array();
    for ($u = 65; $u <= 90; $u++) {
        array_push($alpha, chr($u));
    }

    // Get random alpha character
    $rand_alpha_key = array_rand($alpha);
    $rand_alpha = $alpha[$rand_alpha_key];

    // Add the other missing integers
    $rand = array($rand_alpha);
    for ($c = 0; $c < $length - 1; $c++) {
        array_push($rand, mt_rand(0, 9));
        shuffle($rand);
    }

    return implode('', $rand);
}

  /**
     * Get the message array structure.
     *
     * @param  string $message
     * @param  boolean $success
     * @param  int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function respondWithMessage($message, $success, $status)
    {
        return response()->json([
            'success' => $success,
            'status'  => $status,
            'message' => $message,
        ], $status);
    }

     /**
     * Get the data json structure.
     *
     * @param  boolean $success
     * @param  int $status
     * @param  string $message
     * @param  array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function respondWithData($success, $status, $message, $data)
    {
        return response()->json([
            'success' => $success,
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }