<?php
namespace Swoole\Http2;

/**
 * @since 4.4.0
 */
class Response
{

    public $streamId;
    public $errCode;
    public $statusCode;
    public $pipeline;
    public $headers;
    public $set_cookie_headers;
    public $cookies;
    public $data;


}
