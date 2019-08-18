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

namespace Hyperf\GrpcClient;

final class Status
{
    public const CLOSE_KEYWORD = '>>>SWOOLE|CLOSE<<<';

    public const WAIT_PENDDING = 0;

    public const WAIT_FOR_ALL = 1;

    public const WAIT_CLOSE = 2;

    public const WAIT_CLOSE_FORCE = 3;
}
