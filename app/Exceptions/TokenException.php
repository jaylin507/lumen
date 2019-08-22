<?php
/**
 * Created by PhpStorm.
 * User: jaylin
 * Date: 2019-08-22
 * Time: 16:29
 */

namespace App\Exceptions;


use Throwable;

class TokenException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}