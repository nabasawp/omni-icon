<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\ExpressionParser\Infix;

use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
use OmniIconDeps\Twig\Node\Expression\Unary\NotUnary;
use OmniIconDeps\Twig\Parser;
use OmniIconDeps\Twig\Token;
/**
 * @internal
 */
final class IsNotExpressionParser extends IsExpressionParser
{
    public function parse(Parser $parser, AbstractExpression $expr, Token $token): AbstractExpression
    {
        return new NotUnary(parent::parse($parser, $expr, $token), $token->getLine());
    }
    public function getName(): string
    {
        return 'is not';
    }
}
