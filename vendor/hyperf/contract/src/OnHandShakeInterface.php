<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Contract;

use Swoole\Http\Request;
use Swoole\Http\Response;

interface OnHandShakeInterface
{
    public function onHandShake(Request $request, Response $response): void;
}
