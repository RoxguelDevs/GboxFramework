<?php
namespace Gbox\exceptions;
use Gbox\Response;
class OrmException extends GboxException
{
    public $statusCode;
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    public function getName()
    {
        return 'ORM Error';
    }
}
