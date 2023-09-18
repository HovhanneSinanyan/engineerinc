<?php

namespace App\Middlewares;

use Kernel\Middleware;
use App\Services\AuthService;

class AuthMiddleware extends Middleware {
    public function handle(): bool {
        return !is_null(AuthService::user());
    }

    public function error(): string {
        return "401 Unauthorized";
    }

    public function statusCode(): int {
        return 401;
    }
}