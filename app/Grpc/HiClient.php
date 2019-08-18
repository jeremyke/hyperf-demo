<?php
namespace App\Grpc;

use Hyperf\GrpcClient\BaseClient;
use Grpc\HiUser;

class HiClient extends BaseClient
{
    public function sayHello(HiUser $argument)
    {
        return $this->simpleRequest(
            '/grpc.hi/sayHello',
            $argument,
            [HiReply::class, 'decode']
        );
    }
}