<?php

namespace App\Middlewares;

use Kernel\Middleware;
use App\Services\AuthService;

class GuestMiddleware extends Middleware {
    public function handle(): bool {
        return is_null(AuthService::user());
    }

    public function error(): string {
        return "403 Forbidden";
    }

    public function statusCode(): int {
        return 403;
    }
}