<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\Node\Expression\Binary;

use OmniIconDeps\Twig\Node\Expression\AbstractExpression;
/**
 * @internal
 */
interface BinaryInterface
{
    public function __construct(AbstractExpression $left, AbstractExpression $right, int $lineno);
}
