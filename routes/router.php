<?php 
use App\Middlewares\AuthMiddleware;
use App\Middlewares\GuestMiddleware;
use Kernel\Router;
use App\Controllers\AuthController;
use App\Controllers\TaskController;


Router::post('/auth/register', AuthController::class, 'register')
    ->middlewares(GuestMiddleware::class);
Router::post('/auth/login', AuthController::class, 'login')
    ->middlewares(GuestMiddleware::class);
Router::post('/auth/logout', AuthController::class, 'logout');
Router::get('/auth/user', AuthController::class, 'user')
    ->middlewares(AuthMiddleware::class);

Router::get('/api/tasks', TaskController::class, 'getAll')
    ->middlewares(AuthMiddleware::class);
Router::get('/api/tasks/:id', TaskController::class, 'getbyId')
    ->middlewares(AuthMiddleware::class);
Router::post('/api/tasks', TaskController::class, 'create')
    ->middlewares(AuthMiddleware::class);
Router::put('/api/tasks/:id', TaskController::class, 'update')
    ->middlewares(AuthMiddleware::class);
Router::delete('/api/tasks/:id', TaskController::class, 'delete')
    ->middlewares(AuthMiddleware::class);
Router::patch('/api/tasks/:id/increase', TaskController::class, 'increasePoints')
    ->middlewares(AuthMiddleware::class);
Router::patch('/api/tasks/:id/decrease', TaskController::class, 'decreasePoints')
    ->middlewares(AuthMiddleware::class);
Router::patch('/api/tasks/:id/complete', TaskController::class, 'complete')
    ->middlewares(AuthMiddleware::class);