<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        TokenException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     * @throws Exception
     */
    public function report(Exception $e)
    {
        if (!$this->shouldntReport($e)) {
            $this->sendErrorMail($e);
        } elseif ($e instanceof ValidationException) {
            abort(400, $this->handleValidationException($e));
        }
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $response = parent::render($request, $e);
        $result = [
            'status_code' => $response->getStatusCode(),
            'message' => $e->getMessage()
        ];
        if (app()->environment('local', 'dev', 'test')) {
            $result['debug'] = [
                'class' => $e instanceof FatalThrowableError ? $e->getOriginalClassName() : \get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString()),
            ];
        }
        $response->setContent($result);
        return $response;
    }

    /**
     * @param Exception $exception
     */
    private function sendErrorMail(Exception $exception)
    {
        // 代码出错
        if (app()->environment('prod', 'test')) {
            $request = app('request');
            $requestMethod = $request->getRealMethod();
            $messages = [
                'referer' => $request->headers->get('referer'),
                'url' => $request->url(),
                'method' => $requestMethod,
                'get' => $request->query(),
                'post' => $request->post(),
                'message' => PHP_EOL . $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine() . PHP_EOL,
                'trace' => $exception->getTraceAsString()
            ];
            if ($requestMethod === 'GET') {
                unset($messages['post']);
            }
            try {
                Log::error('Handler抛出错误', $messages);
            } catch (\Exception $e) {

            }
            abort(500, '系统维护中，请稍后再试');
        }
    }

    /**
     * 获取自带数据验证错误信息
     * @param ValidationException $e
     * @return null|string
     */
    protected function handleValidationException(ValidationException $e)
    {
        $errors = @$e->validator->errors()->toArray();
        $message = null;
        if (count($errors)) {
            $firstKey = array_keys($errors)[0];
            $message = @$e->validator->errors()->get($firstKey)[0];
            if (strlen($message) == 0) {
                $message = "一个未知的参数错误";
            }
        }
        if ($message == null) {
            $message = "一个未知的参数错误";
        }
        return $message;
    }

}
