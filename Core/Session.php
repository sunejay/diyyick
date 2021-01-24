<?php
namespace Diyyick\Lib\Core;

use Diyyick\Lib\PadamORM\Entity;
use Diyyick\Lib\PadamORM\DBContext;
/**
 * Description of Session
 *
 * @author Sune
 */
class Session
{    
    public static function userAgentNoVersion() 
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $regx = '/\/[a-zA-Z0-9.]+/';
        $newString = preg_replace($regx, '', $userAgent);
        return $newString;
    }
   
    public static function flashType()
    {
        if (isset($_SESSION['flashType'])) {
            return $_SESSION['flashType'];
        }
    }
   
    public static function flashMessage()
    {
        if (isset($_SESSION['flashMessage'])) {
            return $_SESSION['flashMessage'];
        }
    } 
    
    public static function clearMessage()
    {
        unset($_SESSION['flashMessage']);
        unset($_SESSION['flashType']);
    }
    
    public static function flashTimeOut(int $milisec) 
    {
        echo "<script type='text/javascript'>
                setTimeout(function(){
                    let alert = document.querySelector('.alert');
                    alert.remove();
                }, $milisec);
            </script>";
    }
    
    public static  function exist(string $name)
    {
        return isset($_SESSION[$name]) ? true : false;
    }
    
    public static  function set(string $name, string $value)
    {
        $_SESSION[$name] = $value;
    }

    public static  function get(string $name)
    {
        return $_SESSION[$name] ?? false;
    }

    public static function remove(string $name)
    {
        if (self::exist($name)) {
            unset($_SESSION[$name]);
        }
    }
    
    public static function login(Entity $user)
    {
        $primaryKey = $user->primaryKeys[0];
        $primaryValue = $user->{$primaryKey};
        self::set(AUTH_USER_SESSION_NAME, $primaryValue);
        
        return self::user();
    }
    
    public static function loginWithToken(Entity $user)
    {
        $db = new DBContext();
        $authClass = AuthClass::authEntity();
        $entity = new $authClass();
        
        $token = sha1(md5(microtime(), + rand(0, 100))); 
        self::set(LOGIN_TOKEN, $token);
        
        //$primaryKey = $user->primaryKeys[0]; 
        $entity->{ID} = (int)$user->{$user->primaryKeys[0]}; 
        $entity->{TOKEN} = $token; 
        $db->update($entity);
        $db->commit();
        
        return self::user(); 
    }
    
    public static function loginUserFromCookie()
    {
        if (!self::exist(AUTH_USER_SESSION_NAME) && Cookie::exist(REMEMBER_ME_COOKIE_NAME)) {
            self::loginFromCookie();
        }
    }
    
    public static function loginFromCookie()
    {
        $db = new DBContext();
        $authClass = AuthClass::authEntity();
        $authSessionClass = AuthClass::authSessionEntity();
        $entity = new $authClass();
        $userSessionEntity = new $authSessionClass();
        $uanv = self::userAgentNoVersion();
        $rmcn = Cookie::get(REMEMBER_ME_COOKIE_NAME);
        $userSession = $db->findOne($userSessionEntity, ['user_agent'=>$uanv, 'session'=>$rmcn]);  
        if ($userSession->user_id != '') {
            $user = $db->findOne($entity, [$entity->primaryKeys[0] => $userSession->user_id]);
            self::login($user);
        }
        
        return self::user();
    }
    
    public static function loginUser(Entity $user, bool $rememberMe=false) 
    {
        $db = new DBContext();
        $authSessionClass = AuthClass::authSessionEntity();
        $userSessionEntity = new $authSessionClass();
        
        $primaryValue = $user->{$user->primaryKeys[0]};
        self::set(AUTH_USER_SESSION_NAME, $primaryValue);
        
        if ($rememberMe) {
            $hash = sha1(md5(uniqid(), + rand(0, 100)));
            $userAgent = self::userAgentNoVersion();
            Cookie::set(REMEMBER_ME_COOKIE_NAME, $hash, REMEMBER_ME_COOKIE_EXPIRY);
            $fields = [SESSION=>$hash, USER_AGENT=>$userAgent, USER_ID=>(int)$primaryValue];          
            $userSession = $db->findOne($userSessionEntity, [USER_ID=>(int)$primaryValue, USER_AGENT=>$userAgent]);

            if ($userSession) $db->remove($userSession);  
            $db->create($userSessionEntity, $fields);
            $db->commit();
        }
        return self::user();
    }

    public static function logoutUser()
    {
        if (Cookie::exist(REMEMBER_ME_COOKIE_NAME)) {
            $db = new DBContext();
            $authSessionClass = AuthClass::authSessionEntity();
            $userSessionEntity = new $authSessionClass();
            $uanv = self::userAgentNoVersion();
            $rmcn = Cookie::get(REMEMBER_ME_COOKIE_NAME);
            $userSession = $db->findOne($userSessionEntity, ['user_agent'=>$uanv, 'session'=>$rmcn]); 
            
            if ($userSession){
                $db->remove($userSession);
                $db->commit();
            }
    		Cookie::delete(REMEMBER_ME_COOKIE_NAME);
    	}
    	if (self::get(AUTH_USER_SESSION_NAME)) {
    	    self::remove(AUTH_USER_SESSION_NAME);
    	} else {
    	    self::remove(LOGIN_TOKEN);
    	}
    	session_destroy();
    	return true;
    }
    
    public static function isLoggedIn() 
    {
        return isset($_SESSION[AUTH_USER_SESSION_NAME]) || isset($_SESSION[LOGIN_TOKEN]) ? true : false;
    }
    
    public static function user(): ?Entity 
    {
        $db = new DBContext();
        $authClass = AuthClass::authEntity();
        $entity = new $authClass();
        $primaryValue = self::get(AUTH_USER_SESSION_NAME);
        $tokenValue = self::get(LOGIN_TOKEN);
        if ($primaryValue){
            $user = $db->findOne($entity, [ID => $primaryValue]);
            return $user;
        } elseif ($tokenValue) {
            $user = $db->findOne($entity, [TOKEN => $tokenValue]);
            return $user;
        } else {
            return null;
        }
    }
}
