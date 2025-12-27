<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\Node\Expression\Filter;

use OmniIconDeps\Twig\Attribute\FirstClassTwigCallableReady;
use OmniIconDeps\Twig\Compiler;
use OmniIconDeps\Twig\Node\EmptyNode;
use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
use OmniIconDeps\Twig\Node\Expression\ConstantExpression;
use OmniIconDeps\Twig\Node\Expression\FilterExpression;
use OmniIconDeps\Twig\Node\Node;
use OmniIconDeps\Twig\TwigFilter;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RawFilter extends FilterExpression
{
    /**
     * @param AbstractExpression $node
     */
    #[FirstClassTwigCallableReady]
    public function __construct(Node $node, TwigFilter|ConstantExpression|null $filter = null, ?Node $arguments = null, int $lineno = 0)
    {
        if (!$node instanceof AbstractExpression) {
            trigger_deprecation('twig/twig', '3.15', 'Not passing a "%s" instance to the "node" argument of "%s" is deprecated ("%s" given).', AbstractExpression::class, static::class, $node::class);
        }
        parent::__construct($node, $filter ?: new TwigFilter('raw', null, ['is_safe' => ['all']]), $arguments ?: new EmptyNode(), $lineno ?: $node->getTemplateLine());
    }
    public function compile(Compiler $compiler): void
    {
        $compiler->subcompile($this->getNode('node'));
    }
}
