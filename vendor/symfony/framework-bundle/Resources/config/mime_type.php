<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\DependencyInjection\Loader\Configurator;

use OmniIconDeps\Symfony\Component\Mime\MimeTypeGuesserInterface;
use OmniIconDeps\Symfony\Component\Mime\MimeTypes;
use OmniIconDeps\Symfony\Component\Mime\MimeTypesInterface;
return static function (ContainerConfigurator $container) {
    $container->services()->set('mime_types', MimeTypes::class)->call('setDefault', [service('mime_types')])->alias(MimeTypesInterface::class, 'mime_types')->alias(MimeTypeGuesserInterface::class, 'mime_types');
};
