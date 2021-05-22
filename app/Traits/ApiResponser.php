<?php

namespace App\Traits;

use Carbon\Carbon;

trait ApiResponser
{
  protected function success($data = null, string $message = null, int $code = 200)
  {
    return response()->json([
      'status' => 'Success',
      'message' => $message,
      'data' => $data,
    ], $code);
  }

  protected function error($data = null, string $message = null, int $code)
  {
    return response()->json([
      'status' => 'Error',
      'message' => $message,
      'errors' => $data,
    ], $code);
  }
}