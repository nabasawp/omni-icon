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
use OmniIconDeps\Twig\ExpressionParser\PrecedenceChange;
use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
use OmniIconDeps\Twig\Node\Expression\Binary\AbstractBinary;
use OmniIconDeps\Twig\Parser;
use OmniIconDeps\Twig\Token;
/**
 * @internal
 */
class BinaryOperatorExpressionParser extends AbstractExpressionParser implements InfixExpressionParserInterface, ExpressionParserDescriptionInterface
{
    public function __construct(
        /** @var class-string<AbstractBinary> */
        private string $nodeClass,
        private string $name,
        private int $precedence,
        private InfixAssociativity $associativity = InfixAssociativity::Left,
        private ?PrecedenceChange $precedenceChange = null,
        private ?string $description = null,
        private array $aliases = []
    )
    {
    }
    /**
     * @return AbstractBinary
     */
    public function parse(Parser $parser, AbstractExpression $left, Token $token): AbstractExpression
    {
        $right = $parser->parseExpression(InfixAssociativity::Left === $this->getAssociativity() ? $this->getPrecedence() + 1 : $this->getPrecedence());
        return new $this->nodeClass($left, $right, $token->getLine());
    }
    public function getAssociativity(): InfixAssociativity
    {
        return $this->associativity;
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
