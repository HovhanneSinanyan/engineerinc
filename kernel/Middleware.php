<?php

namespace Kernel;

abstract class Middleware {
    abstract function handle(): bool;
    abstract function error(): string;
    abstract function statusCode(): int;
}