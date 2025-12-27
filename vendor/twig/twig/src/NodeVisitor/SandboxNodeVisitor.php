<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\NodeVisitor;

use OmniIconDeps\Twig\Environment;
use OmniIconDeps\Twig\Node\CheckSecurityCallNode;
use OmniIconDeps\Twig\Node\CheckSecurityNode;
use OmniIconDeps\Twig\Node\CheckToStringNode;
use OmniIconDeps\Twig\Node\Expression\ArrayExpression;
use OmniIconDeps\Twig\Node\Expression\Binary\ConcatBinary;
use OmniIconDeps\Twig\Node\Expression\Binary\RangeBinary;
use OmniIconDeps\Twig\Node\Expression\FilterExpression;
use OmniIconDeps\Twig\Node\Expression\FunctionExpression;
use OmniIconDeps\Twig\Node\Expression\GetAttrExpression;
use OmniIconDeps\Twig\Node\Expression\Unary\SpreadUnary;
use OmniIconDeps\Twig\Node\Expression\Variable\ContextVariable;
use OmniIconDeps\Twig\Node\ModuleNode;
use OmniIconDeps\Twig\Node\Node;
use OmniIconDeps\Twig\Node\Nodes;
use OmniIconDeps\Twig\Node\PrintNode;
use OmniIconDeps\Twig\Node\SetNode;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @internal
 */
final class SandboxNodeVisitor implements NodeVisitorInterface
{
    private $inAModule = \false;
    /** @var array<string, int> */
    private $tags;
    /** @var array<string, int> */
    private $filters;
    /** @var array<string, int> */
    private $functions;
    private $needsToStringWrap = \false;
    public function enterNode(Node $node, Environment $env): Node
    {
        if ($node instanceof ModuleNode) {
            $this->inAModule = \true;
            $this->tags = [];
            $this->filters = [];
            $this->functions = [];
            return $node;
        } elseif ($this->inAModule) {
            // look for tags
            if ($node->getNodeTag() && !isset($this->tags[$node->getNodeTag()])) {
                $this->tags[$node->getNodeTag()] = $node->getTemplateLine();
            }
            // look for filters
            if ($node instanceof FilterExpression && !isset($this->filters[$node->getAttribute('name')])) {
                $this->filters[$node->getAttribute('name')] = $node->getTemplateLine();
            }
            // look for functions
            if ($node instanceof FunctionExpression && !isset($this->functions[$node->getAttribute('name')])) {
                $this->functions[$node->getAttribute('name')] = $node->getTemplateLine();
            }
            // the .. operator is equivalent to the range() function
            if ($node instanceof RangeBinary && !isset($this->functions['range'])) {
                $this->functions['range'] = $node->getTemplateLine();
            }
            if ($node instanceof PrintNode) {
                $this->needsToStringWrap = \true;
                $this->wrapNode($node, 'expr');
            }
            if ($node instanceof SetNode && !$node->getAttribute('capture')) {
                $this->needsToStringWrap = \true;
            }
            // wrap outer nodes that can implicitly call __toString()
            if ($this->needsToStringWrap) {
                if ($node instanceof ConcatBinary) {
                    $this->wrapNode($node, 'left');
                    $this->wrapNode($node, 'right');
                }
                if ($node instanceof FilterExpression) {
                    $this->wrapNode($node, 'node');
                    $this->wrapArrayNode($node, 'arguments');
                }
                if ($node instanceof FunctionExpression) {
                    $this->wrapArrayNode($node, 'arguments');
                }
            }
        }
        return $node;
    }
    public function leaveNode(Node $node, Environment $env): ?Node
    {
        if ($node instanceof ModuleNode) {
            $this->inAModule = \false;
            $node->setNode('constructor_end', new Nodes([new CheckSecurityCallNode(), $node->getNode('constructor_end')]));
            $node->setNode('class_end', new Nodes([new CheckSecurityNode($this->filters, $this->tags, $this->functions), $node->getNode('class_end')]));
        } elseif ($this->inAModule) {
            if ($node instanceof PrintNode || $node instanceof SetNode) {
                $this->needsToStringWrap = \false;
            }
        }
        return $node;
    }
    private function wrapNode(Node $node, string $name): void
    {
        $expr = $node->getNode($name);
        if (($expr instanceof ContextVariable || $expr instanceof GetAttrExpression) && !$expr->isGenerator()) {
            $node->setNode($name, new CheckToStringNode($expr));
        } elseif ($expr instanceof SpreadUnary) {
            $this->wrapNode($expr, 'node');
        } elseif ($expr instanceof ArrayExpression) {
            foreach ($expr as $name => $_) {
                $this->wrapNode($expr, $name);
            }
        }
    }
    private function wrapArrayNode(Node $node, string $name): void
    {
        $args = $node->getNode($name);
        foreach ($args as $name => $_) {
            $this->wrapNode($args, $name);
        }
    }
    public function getPriority(): int
    {
        return 0;
    }
}
