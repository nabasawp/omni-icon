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

use OmniIconDeps\Twig\ExpressionParser\AbstractExpressionParser;
use OmniIconDeps\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use OmniIconDeps\Twig\ExpressionParser\InfixAssociativity;
use OmniIconDeps\Twig\ExpressionParser\InfixExpressionParserInterface;
use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
use OmniIconDeps\Twig\Node\Expression\ConstantExpression;
use OmniIconDeps\Twig\Node\Expression\Ternary\ConditionalTernary;
use OmniIconDeps\Twig\Parser;
use OmniIconDeps\Twig\Token;
/**
 * @internal
 */
final class ConditionalTernaryExpressionParser extends AbstractExpressionParser implements InfixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    public function parse(Parser $parser, AbstractExpression $left, Token $token): AbstractExpression
    {
        $then = $parser->parseExpression($this->getPrecedence());
        if ($parser->getStream()->nextIf(Token::PUNCTUATION_TYPE, ':')) {
            // Ternary operator (expr ? expr2 : expr3)
            $else = $parser->parseExpression($this->getPrecedence());
        } else {
            // Ternary without else (expr ? expr2)
            $else = new ConstantExpression('', $token->getLine());
        }
        return new ConditionalTernary($left, $then, $else, $token->getLine());
    }
    public function getName(): string
    {
        return '?';
    }
    public function getDescription(): string
    {
        return 'Conditional operator (a ? b : c)';
    }
    public function getPrecedence(): int
    {
        return 0;
    }
    public function getAssociativity(): InfixAssociativity
    {
        return InfixAssociativity::Left;
    }
}
