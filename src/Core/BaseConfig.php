<?php
namespace Diyyick\Core;

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
