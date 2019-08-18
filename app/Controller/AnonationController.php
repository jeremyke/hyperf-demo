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
use Psr\Container\ContainerInterface;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;

/**
 * Class TestController
 * @package App\Controller
 * @AutoController()
 */
class AnonationController extends Controller
{
    /**
     * @Inject()
     * @var UserService;
     */
    public $userService;

    /**
     * @return string
     */
    /*public function __construct(UserService $userService)
    {
        $this->userService =$userService;
    }*/


    /*public function index()
    {
        var_dump($this->userService);

    }*/

    public function getUser()
    {
        return $this->userService->getUserById();
    }

}
