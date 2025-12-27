<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpKernel\DependencyInjection;

use OmniIconDeps\Symfony\Contracts\Service\ResetInterface;
/**
 * Resets provided services.
 */
interface ServicesResetterInterface extends ResetInterface
{
}
