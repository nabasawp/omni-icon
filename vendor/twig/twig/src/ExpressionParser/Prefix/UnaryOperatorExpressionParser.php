<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\ExpressionParser\Prefix;

use OmniIconDeps\Twig\ExpressionParser\AbstractExpressionParser;
use OmniIconDeps\Twig\ExpressionParser\ExpressionParserDescriptionInterface;
use OmniIconDeps\Twig\ExpressionParser\PrecedenceChange;
use OmniIconDeps\Twig\ExpressionParser\PrefixExpressionParserInterface;
use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
use OmniIconDeps\Twig\Node\Expression\Unary\AbstractUnary;
use OmniIconDeps\Twig\Parser;
use OmniIconDeps\Twig\Token;
/**
 * @internal
 */
final class UnaryOperatorExpressionParser extends AbstractExpressionParser implements PrefixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    public function __construct(
        /** @var class-string<AbstractUnary> */
        private string $nodeClass,
        private string $name,
        private int $precedence,
        private ?PrecedenceChange $precedenceChange = null,
        private ?string $description = null,
        private array $aliases = []
    )
    {
    }
    /**
     * @return AbstractUnary
     */
    public function parse(Parser $parser, Token $token): AbstractExpression
    {
        return new $this->nodeClass($parser->parseExpression($this->precedence), $token->getLine());
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): string
    {
        return $this->description ?? '';
    }
    public function getPrecedence(): int
    {
        return $this->precedence;
    }
    public function getPrecedenceChange(): ?PrecedenceChange
    {
        return $this->precedenceChange;
    }
    public function getAliases(): array
    {
        return $this->aliases;
    }
}
