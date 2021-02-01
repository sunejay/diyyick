<?php
namespace Diyyick\Lib\Core;
/**
 * Description of Cookie
 *
 * @author Sune
 */
class Cookie 
{
    public static function set(string $name, string $value, int $expiry)
    {
        if (setcookie($name, $value, time()+$expiry, '/')) return true;
        return false;
    }
    
    public static  function get(string $name)
    {
        return $_COOKIE[$name] ?? false;
    }
    
    public static  function delete(string $name)
    {
        self::set($name, '', time()-1);
    }
    
    public static  function exist(string $name)
    {
        return isset($_COOKIE[$name]) ? true : false;
    }
}
