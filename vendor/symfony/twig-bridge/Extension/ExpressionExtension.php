<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bridge\Twig\Extension;

use OmniIconDeps\Symfony\Component\ExpressionLanguage\Expression;
use OmniIconDeps\Twig\Extension\AbstractExtension;
use OmniIconDeps\Twig\TwigFunction;
/**
 * ExpressionExtension gives a way to create Expressions from a template.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class ExpressionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [new TwigFunction('expression', $this->createExpression(...))];
    }
    public function createExpression(string $expression): Expression
    {
        return new Expression($expression);
    }
}
