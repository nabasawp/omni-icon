<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bundle\FrameworkBundle\DataCollector;

use OmniIconDeps\Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
/**
 * @author Laurent VOULLEMIER <laurent.voullemier@gmail.com>
 */
interface TemplateAwareDataCollectorInterface extends DataCollectorInterface
{
    public static function getTemplate(): ?string;
}
