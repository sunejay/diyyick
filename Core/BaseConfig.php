<?php
namespace Diyyick\Lib\Core;

/**
 * Description of BaseConfig
 *
 * @author Sune
 */
abstract class BaseConfig 
{
    abstract public function authEntity();
    abstract public function authSessionEntity();
}
