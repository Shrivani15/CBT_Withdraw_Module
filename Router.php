<?php

class Router
{
    private array $routes = [];

    public function add(string $_method, string $_pattern, callable $_handler) {
        $this->routes[] = [
            "method" => strtoupper($_method),
            "pattern" => $_pattern,
            "handler" => $_handler
        ];
    }

    public function dispatch(string $_method, string $_path) {
        foreach ($this->routes as $route) {

            if ($route["method"] !== strtoupper($_method)) {
                continue;
            }

            $params = $this->match($route["pattern"], $_path);

            if ($params !== null) {
                call_user_func_array($route["handler"], $params);

                return;
            }
        }

        http_response_code(404);

        echo json_encode([ "status" => false, "message" => "Route Not Found"]);
    }

    private function match(string $_pattern, string $_path) {
        $pattern_parts = array_values(array_filter(explode("/", $_pattern)));

        $path_parts = array_values(array_filter(explode("/", $_path)));

        if (count($pattern_parts) !== count($path_parts)) {
            return null;
        }

        $params = [];

        foreach ($pattern_parts as $index => $part) {

            if (preg_match('/^{.+}$/', $part)) {
                $params[] = $path_parts[$index];

            } elseif ($part !== $path_parts[$index]) {

                return null;
            }
        }

        return $params;
    }
}