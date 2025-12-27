<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\Node\Expression\Binary;

use OmniIconDeps\Twig\Compiler;
use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
use OmniIconDeps\Twig\Node\Expression\OperatorEscapeInterface;
use OmniIconDeps\Twig\Node\Node;
final class ElvisBinary extends AbstractBinary implements OperatorEscapeInterface
{
    /**
     * @param AbstractExpression $left
     * @param AbstractExpression $right
     */
    public function __construct(Node $left, Node $right, int $lineno)
    {
        parent::__construct($left, $right, $lineno);
        $this->setNode('test', clone $left);
        $left->setAttribute('always_defined', \true);
    }
    public function compile(Compiler $compiler): void
    {
        $compiler->raw('((')->subcompile($this->getNode('test'))->raw(') ? (')->subcompile($this->getNode('left'))->raw(') : (')->subcompile($this->getNode('right'))->raw('))');
    }
    public function operator(Compiler $compiler): Compiler
    {
        return $compiler->raw('?:');
    }
    public function getOperandNamesToEscape(): array
    {
        return ['left', 'right'];
    }
}
