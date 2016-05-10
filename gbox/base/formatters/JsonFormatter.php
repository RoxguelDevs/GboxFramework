<?php
namespace Gbox\formatters;
use Gbox\helpers\Json;
class JsonFormatter
{
    public $contentType = 'application/json';
    public function format($response)
    {
        $response->getHeaders()->set('Content-Type', 'application/json; charset=UTF-8');
        if ($response->data !== null) {
            $response->content = Json::encode($response->data);
        }
    }
}
