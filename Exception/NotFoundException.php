<?php
namespace Diyyick\Lib\Exception;

/**
 * Description of NotFoundException
 *
 * @author Sune
 */
class NotFoundException extends \Exception
{
    protected string $message = 'Page not found';
    protected int $code = 404;
}
