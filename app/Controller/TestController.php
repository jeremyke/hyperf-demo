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

/**
 * Class TestController
 * @package App\Controller
 * @AutoController
 */
class TestController extends Controller
{
    public function index()
    {
        return "<h3>欢迎来到hyperf</h3>";
    }

    public function anonation()
    {
        return "注解定义路由";
    }
}
