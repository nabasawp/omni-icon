<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\UX\Icons;

use OmniIconDeps\Symfony\UX\Icons\Exception\IconNotFoundException;
/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @extends \IteratorAggregate<string>
 *
 * @internal
 */
interface IconRegistryInterface
{
    /**
     * @throws IconNotFoundException
     */
    public function get(string $name): Icon;
}
