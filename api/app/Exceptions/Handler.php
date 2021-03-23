<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $rendered = parent::render($request, $e);

        // Handler for method
        if ($e instanceof HttpResponseException) {
          $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        } elseif ($e instanceof MethodNotAllowedHttpException) {
          $status = Response::HTTP_METHOD_NOT_ALLOWED;
          $e = new MethodNotAllowedHttpException([], 'HTTP_METHOD_NOT_ALLOWED', $e);
        } elseif ($e instanceof NotFoundHttpException) {
          $status = Response::HTTP_NOT_FOUND;
          $e = new NotFoundHttpException('HTTP_NOT_FOUND', $e);
        } elseif ($e instanceof AuthorizationException) {
          $status = Response::HTTP_FORBIDDEN;
          $e = new AuthorizationException('HTTP_FORBIDDEN', $status);
        } elseif ($e instanceof \Dotenv\Exception\ValidationException && $e->getResponse()) {
          $status = Response::HTTP_BAD_REQUEST;
          $e = new \Dotenv\Exception\ValidationException('HTTP_BAD_REQUEST', $status, $e);
        } elseif ($e) {
          $e = $e;
        }

        ################### RESPONSE CODE ##################
        /*
            200 = Sukses (Success)
            201 = Permintaan berhasil dibuat (Created)
            400 = Permintaan tidak sesuai (Bad Request)
            401 = Tidak sah (Unauthorized)
            402 = Dibutuhkan parameter transaksi (Payment Required)
            403 = Terlarang (Forbidden)
            404 = Tidak ditemukan (Not Found)
            405 = Metode permintaan tidak didukung misal GET jadi POST (Method Not Allowed)
            409 = Gagal mengirim data / Terjadi konflik (Failed / Conflict)

        */
        // Response json message
        return response()->json([
            'success'  => false,
            'status'   => $rendered->getStatusCode(),
            'message'  => $e->getMessage()
        ], $rendered->getStatusCode());
    }
}
