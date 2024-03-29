<?php

namespace App\Exceptions;

use App\Enums\ResponseCodes\MainRespCode;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => [
                        'message' => 'Not authorized',
                        'code' => 401,
                    ],
                ], 401);
            }
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || ($request->getHost() === config('app.api_domain'))) {
                if (app()->isDownForMaintenance()) {
                    return response()->json(['success' => false, 'data' => null, 'error' => ['message' => 'Server is under maintenance.', 'code' => '503']], 503);
                }
                $error = match ($e::class) {
//                    HttpException::class => ['Bad request', 400],
                    AuthenticationException::class => ['Not authorized', 401],
                    NotFoundHttpException::class => ['Not found', 404],
                    MethodNotAllowedHttpException::class => ['Method not allowed', 405],
                    ModelNotFoundException::class => [$e->getMessage(), 404],
//                    TooManyRequestsHttpException::class => ['', 429],
                    ThrottleRequestsException::class => [$e->getMessage(), 429],
                    ErrorResponse::class => [$e->getMessage(), $e->getCode()],
//                    ValidationException::class => [$e->getMessage(), -12222],
                    default => [$e->getMessage(), $e->getCode()],
                    //default => ['Server error', 500],
                };

                list($message, $code) = $error;

                if ($code === 500) {
                    try {
                        $message2 = view('tg-debug', ['debug' => $e])->render();
                        telegram_bot_send($message2, parse_mode: 'html');
                    } catch (Throwable) {

                    }
                }

                $http_code = match ($code) {
                    401 => 401,
                    403 => 403,
                    404 => 404,
                    422 => 422,
                    429 => 429,
                    426 => 426,
                    default => 500,
                };

                $errorsData = null;

                if ($e instanceof ValidationException) {
                    $message = 'Invalid params';
                    $code = -32602;
                    $errorsData = collect($e->validator->errors())
                        ->mapWithKeys(fn($errors, $key) => [$key => $errors])->toArray();
                }

                return response()->json([
                    'success' => false,
                    'data' => null,
                    'error' => [
                        'message' => mb_convert_encoding($message, 'UTF-8', 'UTF-8'),
                        'errors' => $errorsData, // 'data' => $e->getMessage(),
                        'code' => (int)mb_convert_encoding($code, 'UTF-8', 'UTF-8'),
                    ],
                ], $http_code);
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
