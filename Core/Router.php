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
    private static $paramId;
    private static $paramStr;

    public static function add(string $path, $callback)
    {
        // Get request URL
        $getUrl = isset($_REQUEST['uri']) ? '/' . $_REQUEST['uri'] : '/';
        // Filter URL
        $getUrl = filter_var($getUrl, FILTER_SANITIZE_URL);
        $getUrl = strtolower($getUrl);
        
        $p1 = '/^\{([a-z]+)\}$/'; 
        $p2 = '/^\{([a-z]+):([^\}]+)\}$/'; 
        $uris = explode('/', $path);
        $urls = explode('/', $getUrl);
        if (count($urls) == count($uris)) {
            self::$params = array_combine($uris, $urls);
        }
        
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $path);
        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';
        $isMatch = preg_match($route, $getUrl, $matches, PREG_OFFSET_CAPTURE);
        
        if ($isMatch) {
            self::$validUrl[$path] = $callback;
            if (is_array($callback)){
                $controller = new $callback[0]();
                self::$controller = $controller;
                self::$controller->action = $callback[1];
                $callback[0] = $controller;
            }
            
            foreach (self::$params as $key => $value) {
                if (preg_match($p1, $key)) {
                    $re = trim($key, '{}');
                    $$re = $value;
                    self::$paramStr = $$re;
                }
                if (preg_match($p2, $key)) {
                    $re = trim($key, '{:\d+}');
                    $$re = $value;
                    self::$paramId = $$re;
                }
            }
            
            if (!empty(self::$paramId) && !empty(self::$paramStr)) {
                call_user_func($callback, new Response(), new Request(), (int)self::$paramId, self::$paramStr);
            } elseif (!empty(self::$paramId)) {
                call_user_func($callback, new Response(), new Request(), (int)self::$paramId);
            } elseif (!empty(self::$paramStr)) {
                call_user_func($callback, new Response(), new Request(), self::$paramStr);
            } else {
                call_user_func($callback, new Response(), new Request());
            }
        }
    }
    
    public static function url(string $param) 
    {
        return BASEPATH . $param;
    }
}
