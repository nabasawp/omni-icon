<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\Node\Expression\Ternary;

use OmniIconDeps\Twig\Compiler;
use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
use OmniIconDeps\Twig\Node\Expression\OperatorEscapeInterface;
use OmniIconDeps\Twig\Node\Expression\ReturnPrimitiveTypeInterface;
use OmniIconDeps\Twig\Node\Expression\Test\TrueTest;
use OmniIconDeps\Twig\TwigTest;
final class ConditionalTernary extends AbstractExpression implements OperatorEscapeInterface
{
    public function __construct(AbstractExpression $test, AbstractExpression $left, AbstractExpression $right, int $lineno)
    {
        if (!$test instanceof ReturnPrimitiveTypeInterface) {
            $test = new TrueTest($test, new TwigTest('true'), null, $test->getTemplateLine());
        }
        parent::__construct(['test' => $test, 'left' => $left, 'right' => $right], [], $lineno);
    }
    public function compile(Compiler $compiler): void
    {
        $compiler->raw('((')->subcompile($this->getNode('test'))->raw(') ? (')->subcompile($this->getNode('left'))->raw(') : (')->subcompile($this->getNode('right'))->raw('))');
    }
    public function getOperandNamesToEscape(): array
    {
        return ['left', 'right'];
    }
}
