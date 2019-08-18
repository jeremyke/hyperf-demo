<?php

declare(strict_types=1);

namespace Zipkin\Reporters;

use Zipkin\Recording\Span;
use Zipkin\Reporter;

final class Noop implements Reporter
{
    /**
     * @param Span[] $spans
     * @return void
     */
    public function report(array $spans): void
    {
    }
}
