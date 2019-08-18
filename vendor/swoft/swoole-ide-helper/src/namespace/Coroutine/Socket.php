<?php
namespace Swoole\Coroutine;

/**
 * @since 4.4.0
 */
class Socket
{

    public $fd;
    public $errCode;
    public $errMsg;

    /**
     * @param $domain [required]
     * @param $type [required]
     * @param $protocol [optional]
     * @return mixed
     */
    public function __construct($domain, $type, $protocol=null){}

    /**
     * @param $address [required]
     * @param $port [optional]
     * @return mixed
     */
    public function bind($address, int $port=null){}

    /**
     * @param $backlog [optional]
     * @return mixed
     */
    public function listen($backlog=null){}

    /**
     * @param $timeout [optional]
     * @return mixed
     */
    public function accept(float $timeout=null){}

    /**
     * @param $host [required]
     * @param $port [optional]
     * @param $timeout [optional]
     * @return mixed
     */
    public function connect(string $host, int $port=null, float $timeout=null){}

    /**
     * @param $length [optional]
     * @param $timeout [optional]
     * @return mixed
     */
    public function recv(int $length=null, float $timeout=null){}

    /**
     * @param $timeout [optional]
     * @return mixed
     */
    public function recvPacket(float $timeout=null){}

    /**
     * @param $data [required]
     * @param $timeout [optional]
     * @return mixed
     */
    public function send($data, float $timeout=null){}

    /**
     * @param $filename [required]
     * @param $offset [optional]
     * @param $length [optional]
     * @return mixed
     */
    public function sendFile(string $filename, int $offset=null, int $length=null){}

    /**
     * @param $length [optional]
     * @param $timeout [optional]
     * @return mixed
     */
    public function recvAll(int $length=null, float $timeout=null){}

    /**
     * @param $data [required]
     * @param $timeout [optional]
     * @return mixed
     */
    public function sendAll($data, float $timeout=null){}

    /**
     * @param $peername [required]
     * @param $timeout [optional]
     * @return mixed
     */
    public function recvfrom($peername, float $timeout=null){}

    /**
     * @param $addr [required]
     * @param $port [required]
     * @param $data [required]
     * @return mixed
     */
    public function sendto($addr, int $port, $data){}

    /**
     * @param $level [required]
     * @param $opt_name [required]
     * @return mixed
     */
    public function getOption($level, $opt_name){}

    /**
     * @param $settings [required]
     * @return mixed
     */
    public function setProtocol(array $settings){}

    /**
     * @param $level [required]
     * @param $opt_name [required]
     * @param $opt_value [required]
     * @return mixed
     */
    public function setOption($level, $opt_name, $opt_value){}

    /**
     * @param $how [required]
     * @return mixed
     */
    public function shutdown($how){}

    /**
     * @param $event [optional]
     * @return mixed
     */
    public function cancel($event=null){}

    /**
     * @return mixed
     */
    public function close(){}

    /**
     * @return mixed
     */
    public function getpeername(){}

    /**
     * @return mixed
     */
    public function getsockname(){}


}
