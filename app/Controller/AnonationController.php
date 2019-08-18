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

/**
 * Class TestController
 * @package App\Controller
 * @AutoController()
 */
class AnonationController extends Controller
{
    /**
     * @var UserService;
     */
    public $userService;

    /**
     * @return string
     * @
     */
    public function __construct(UserService $userService)
    {
        $this->userService =$userService;
    }


    public function index()
    {
        return $this->userService;

    }

    public function getUser()
    {
        return $this->userService->getUserById();
    }

}
