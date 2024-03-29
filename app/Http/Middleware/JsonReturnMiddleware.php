<?php

/*
 * Copyright (C) 2022 Inoyatullokhon <https://professor.uz>
 * */

namespace App\Http\Middleware;

use App\Models\UserDevice;
use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;

class JsonReturnMiddleware
{
    protected ResponseFactory $factory;

    private array $except = [
        'api/v1/resources/error'
    ];

    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);
        if(in_array($request->path(), $this->except)) {
            return $response;
        }
        $code = $response->status();
        $ok = floor($code / 100) == 2;
        if ($ok) {
            $data_key = 'data';
            $err_key = 'error';
        } else {
            $data_key = 'error';
            $err_key = 'data';
        }

        if (!in_array($response->status(), [401, 403, 404, 422, 501])) {
            $response->setStatusCode(200);
        }

        if ($response instanceof JsonResponse) {
            $data = $ok ? $response->getData(true) : ($response->getData(true)['error'] ?? 'Internal Error');
            $content = ['success' => $ok, $data_key => $data, $err_key => null];
            $response->setData($content);
        } else {
            if (is_string($response->content()) || $response->content() instanceof Stringable) {
                $response->header('Content-Type', 'application/json');
                $result = ['message' => $response->content()];
            } else {
                $result = $response->content();
            }
            $content = ['success' => $ok, $data_key => $result, $err_key => null];

            $response = $this->factory->json($content, $response->status(), $response->headers->all());
        }

        return $response;
    }
}
