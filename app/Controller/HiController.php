<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/19
 * Time: 0:46
 */
declare(strict_types=1);

namespace App\Controller;

class HiController extends Controller
{
    public function sayHello(HiUser $user)
    {
        $message = new HiReply();
        $message->setMessage("Hello World");
        $message->setUser($user);
        return $message;
    }
}