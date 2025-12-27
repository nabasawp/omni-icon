<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\ExpressionParser;

use OmniIconDeps\Twig\Error\SyntaxError;
use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
use OmniIconDeps\Twig\Parser;
use OmniIconDeps\Twig\Token;
interface InfixExpressionParserInterface extends ExpressionParserInterface
{
    /**
     * @throws SyntaxError
     */
    public function parse(Parser $parser, AbstractExpression $left, Token $token): AbstractExpression;
    public function getAssociativity(): InfixAssociativity;
}
