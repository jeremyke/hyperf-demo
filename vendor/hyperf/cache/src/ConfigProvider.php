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

namespace Hyperf\Cache;

use Psr\SimpleCache\CacheInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                CacheInterface::class => Cache::class,
            ],
            'commands' => [
            ],
            'scan' => [
                'paths' => [
                    __DIR__,
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for cache.',
                    'source' => __DIR__ . '/../publish/cache.php',
                    'destination' => BASE_PATH . '/config/autoload/cache.php',
                ],
            ],
        ];
    }
}
