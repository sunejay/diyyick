<?php
namespace Diyyick\Lib\Exception;

/**
 * Description of ForbiddenException
 *
 * @author Sune
 */
class ForbiddenException extends \Exception
{
    protected string $message = 'You don\'t have permission to access this page';
    protected int $code = 403;
}