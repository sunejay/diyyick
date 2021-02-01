<?php
namespace Diyyick\Lib\Core;

use Diyyick\Lib\PadamORM\DBContext;
/**
 * Description of Router
 *
 * @author Sune
 */
class Router 
{
    public static ?Controller $controller = null;
    private static array $validUrl = [];
    private static array $params = [];
    private static $paramInt;
    private static $paramStr;

    /**
     * @param string $path route URL 
     * @param mixed $callback callback function 
     */
    public static function add(string $path, $callback)
    {
        // Get request URL
        $getUrl = isset($_REQUEST['uri']) ? '/' . $_REQUEST['uri'] : '/';
        // Filter URL
        $getUrl = strtolower(filter_var($getUrl, FILTER_SANITIZE_URL));
        
        /**
         * @var string $p1 string parameters e.g '/user/{username}'
         */ 
        $p1 = '/^\{([a-z]+)\}$/'; 
        /**
         * @var int $p2 int parameters e.g '/user/{id:\d+}'
         */ 
        $p2 = '/^\{([a-z]+):([^\}]+)\}$/'; 

        $routerUrls = explode('/', $path);
        $requestUrls = explode('/', $getUrl);
        
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $path);
        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';
        $isMatch = preg_match($route, $getUrl, $matches, PREG_OFFSET_CAPTURE);
        
        if (count($requestUrls) == count($routerUrls)) {
            self::$params = array_combine($routerUrls, $requestUrls);
        }

        if ($isMatch) {
            self::$validUrl[$path] = $callback; 
            
            foreach (self::$params as $key => $value) {
                if (preg_match($p1, $key)) {
                    $re = trim($key, '{}');
                    $$re = $value;
                    self::$paramStr = $$re;
                }
                if (preg_match($p2, $key)) {
                    $re = trim($key, '{:\d+}');
                    $$re = $value;
                    self::$paramInt = $$re;
                }
            }
            /**
             * if callback is an array e.g ('/', [Controller::class, 'action'])
             */
            if (is_array($callback)){
                $controller = new $callback[0]();
                self::$controller = $controller;
                self::$controller->action = $callback[1];
                $callback[0] = $controller;
            }
            /**
             * if callback is a string e.g ('/', 'Controller::action') 
             */
            if (is_string($callback)) {
                if (!empty(self::$paramInt) && !empty(self::$paramStr)) {
                    call_user_func($callback, new Request(), new Response(), (int)self::$paramInt, self::$paramStr);
                } elseif (!empty(self::$paramInt)) {
                    call_user_func($callback, new Request(), new Response(), (int)self::$paramInt);
                } elseif (!empty(self::$paramStr)) {
                    call_user_func($callback, new Request(), new Response(), self::$paramStr);
                } else {
                    call_user_func($callback, new Request(), new Response(),);
                }
            }

            if (!empty(self::$paramInt) && !empty(self::$paramStr)) {
                call_user_func($callback, new Request(), new Response(), (int)self::$paramInt, self::$paramStr);
            } elseif (!empty(self::$paramInt)) {
                call_user_func($callback, new Request(), new Response(), (int)self::$paramInt);
            } elseif (!empty(self::$paramStr)) {
                call_user_func($callback, new Request(), new Response(), self::$paramStr);
            } else {
                call_user_func($callback, new Request(), new Response(),);
            }
        } /*else {
            // throw new \Exception("Page not found");
            echo '<h1>Page not found</h1>';
        } */
    }
    
    public static function url(string $param) 
    {
        return BASEPATH . $param;
    }
}
