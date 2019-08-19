<?php
declare(strict_types=1);
namespace App\Grpc;

use Hyperf\GrpcClient\BaseClient;
use Grpc\HiUser;
use Grpc\HiReply;

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