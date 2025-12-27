<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\Node\Expression\Test;

use OmniIconDeps\Twig\Compiler;
use OmniIconDeps\Twig\Node\Expression\TestExpression;
/**
 * Checks that an expression is true.
 *
 *  {{ var is true }}
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TrueTest extends TestExpression
{
    public function compile(Compiler $compiler): void
    {
        $compiler->raw('(($tmp = ')->subcompile($this->getNode('node'))->raw(') && $tmp instanceof Markup ? (string) $tmp : $tmp)');
    }
}
