<?php

namespace Core;

class Router{

    protected $routes = [

        'GET' => [],
        'POST' => []
    ];

    //registro de rutas que el usuario accede escribiendo en el navegador.
    public function get($uri, $controller, $action){
        $this->routes['GET'][$uri] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    //registro de rutas por donde viajan los datos de los formularios ocultos.
    public function post($uri, $controller, $action){
        $this->routes['POST'][$uri]=[
            'controller' => $controller,
            'action' => $action
        ];
    }

    //motor principal que evalua la URI entrante
    public function resolve($uri, $method){
        //limpieza de la uri para evitar errores.
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        //Se verifica si existes se combina metodo + url
        if(isset($this->routes[$method][$uri])){
            $route = $this->routes[$method][$uri];

            //reconstruccion del nombre
            $controllerName = "\\Controllers\\" . $route['controller'];
            $action = $route['action'];
        
            //se inicia el controlador y se ejecuta el metodo dinamicamente
            if(class_exists($controllerName)){

                $controller = new $controllerName();
                if(method_exists($controller, $action)){
                    $controller->$action();
                }else{
                    die("Error: El metodo {$action} no existe en {$controllerName}");
                }
            }else{
                die("Error: El controlador {$controllerName} no fue encontrado");
            }
        }else{
            http_response_code(404);
            echo "<h1>404 Not Found</h1>";
            echo "<p>La ruta no esta definida en el sistema</p>";
        }
    }
}