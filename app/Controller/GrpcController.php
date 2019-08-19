<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/19
 * Time: 0:46
 */
declare(strict_types=1);

namespace App\Controller;

use Grpc\HiUser;
use Grpc\HiReply;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * Class GrpcController
 * @package App\Controller
 * @AutoController()
 */
class GrpcController extends Controller
{
    public function hello()
    {
        $client = new \App\Grpc\HiClient('127.0.0.1:9503', [
            'credentials' => null,
        ]);
        $request = new HiUser();
        $request->setName('hyperf');
        $request->setSex(1);
        /**
         * @var HiReply $reply
         */
        list($reply, $status) = $client->sayHello($request);
        $message = $reply->getMessage();
        $user = $reply->getUser();

        $client->close();
        var_dump(memory_get_usage(true));
        return $message;
    }
}