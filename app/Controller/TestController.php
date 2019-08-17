<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/18
 * Time: 2:41
 */
declare(strict_types=1);
namespace App\Controller;

class TestController extends Controller
{
    public function index()
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return [
            'method' => $method,
            'message' => "Hello {$user}.",
        ];
    }
}
