<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/18
 * Time: 2:41
 */
declare(strict_types=1);
namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Class TestController
 * @package App\Controller
 * @Controller
 */
class RouterController extends Controller
{
    public function index()
    {
        return "<h3>欢迎来到hyperf</h3>";
    }

    /**
     * @return string
     * @RequestMapping(path="anonation", methods="get,post")
     */
    public function anonation()
    {
        return "注解定义路由(第二种)";
    }
}
