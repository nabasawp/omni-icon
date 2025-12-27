<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bridge\Twig\Node;

use OmniIconDeps\Twig\Attribute\YieldReady;
use OmniIconDeps\Twig\Compiler;
use OmniIconDeps\Twig\Node\Expression\AssignNameExpression;
use OmniIconDeps\Twig\Node\Expression\Variable\LocalVariable;
use OmniIconDeps\Twig\Node\Node;
/**
 * Represents a stopwatch node.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
#[YieldReady]
final class StopwatchNode extends Node
{
    public function __construct(Node $name, Node $body, AssignNameExpression|LocalVariable $var, int $lineno = 0)
    {
        parent::__construct(['body' => $body, 'name' => $name, 'var' => $var], [], $lineno);
    }
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this)->write('')->subcompile($this->getNode('var'))->raw(' = ')->subcompile($this->getNode('name'))->write(";\n")->write("\$this->env->getExtension('Symfony\\Bridge\\Twig\\Extension\\StopwatchExtension')->getStopwatch()->start(")->subcompile($this->getNode('var'))->raw(", 'template');\n")->subcompile($this->getNode('body'))->write("\$this->env->getExtension('Symfony\\Bridge\\Twig\\Extension\\StopwatchExtension')->getStopwatch()->stop(")->subcompile($this->getNode('var'))->raw(");\n");
    }
}
