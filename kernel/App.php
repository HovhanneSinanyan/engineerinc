<?php 

namespace Kernel;
use Kernel\Exceptions\ValidationException;


class App {
    private Router $router;
    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function captureRequest() {
        $uri = $_SERVER['REDIRECT_URL'] ?? '/';   

        $route = $this->router->getAction($uri, $_SERVER['REQUEST_METHOD']);
        if ($route) {
            $this->handleRequest($route);
        } else {
            http_response_code(404);
            echo "404 not found!";
        }
        
    }

    private function wantsJson() {
        return array_key_exists('CONTENT_TYPE', $_SERVER) && $_SERVER['CONTENT_TYPE'] === 'application/json' ;
    }
    private function getRequestBody() {
            return $this->wantsJson()
            ? json_decode(file_get_contents('php://input'), true)
            : $_REQUEST;
    }
 
    private function handleRequest($route) {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $action = $route['action'];
        $params = $route['params'];
        $controller = new $route['controller']();
        $middlewares = $route['middlewares'];
        $requestBody = $this->getRequestBody() ?? [];
        if (method_exists($controller, $action) && $requestMethod === $route['method']) {
            foreach ($middlewares as $middlewareClass) {
                $middleware = new $middlewareClass();
                if(!$middleware->handle()) {
                    http_response_code($middleware->statusCode());
                    echo $middleware->error();
                    return;
                }
            }
            
            try {

                $result = $controller->$action(...$params, ...[$requestBody]);
                $successStatusCode = $requestMethod === 'POST' ? 201: 200;
                http_response_code($successStatusCode);
                if($this->wantsJson()) {
                    header("Content-Type: application/json; charset=utf-8");
                    echo json_encode($result);
                } elseif(is_string($result)) {
                    echo $result;
                } else {
                    echo json_encode($result);
                }
            }
             catch (\Exception $e) {
                http_response_code($e->getCode());
                echo $e->getMessage(); 
            }
            
        } else {
            // Handle action not found
            http_response_code(404);
            echo "Action not found!";
        }
    }
}