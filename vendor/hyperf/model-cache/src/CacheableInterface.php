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

namespace Hyperf\ModelCache;

use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;

interface CacheableInterface
{
    public static function findFromCache($id): ?Model;

    public static function findManyFromCache($ids): Collection;

    public function deleteCache(): bool;
}
