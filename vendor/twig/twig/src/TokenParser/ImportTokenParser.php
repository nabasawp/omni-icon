<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\TokenParser;

use OmniIconDeps\Twig\Node\Expression\Variable\AssignTemplateVariable;
use OmniIconDeps\Twig\Node\Expression\Variable\TemplateVariable;
use OmniIconDeps\Twig\Node\ImportNode;
use OmniIconDeps\Twig\Node\Node;
use OmniIconDeps\Twig\Token;
/**
 * Imports macros.
 *
 *   {% import 'forms.html.twig' as forms %}
 *
 * @internal
 */
final class ImportTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): Node
    {
        $macro = $this->parser->parseExpression();
        $this->parser->getStream()->expect(Token::NAME_TYPE, 'as');
        $name = $this->parser->getStream()->expect(Token::NAME_TYPE)->getValue();
        $var = new AssignTemplateVariable(new TemplateVariable($name, $token->getLine()), $this->parser->isMainScope());
        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);
        $this->parser->addImportedSymbol('template', $name);
        return new ImportNode($macro, $var, $token->getLine());
    }
    public function getTag(): string
    {
        return 'import';
    }
}
