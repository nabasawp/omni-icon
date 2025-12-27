<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\Routing\Annotation;

// do not deprecate in 6.4/7.0, to make it easier for the ecosystem to support 6.4, 7.4 and 8.0 simultaneously
class_exists(\OmniIconDeps\Symfony\Component\Routing\Attribute\Route::class);
if (\false) {
    /**
     * @deprecated since Symfony 7.4, use {@see \Symfony\Component\Routing\Attribute\Route} instead
     */
    #[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
    class Route extends \OmniIconDeps\Symfony\Component\Routing\Attribute\Route
    {
    }
}
