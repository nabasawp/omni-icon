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

use OmniIconDeps\Symfony\Component\Translation\IdentityTranslator;
use OmniIconDeps\Symfony\Contracts\Translation\TranslatorInterface;
return static function (ContainerConfigurator $container) {
    $container->services()->set('translator', IdentityTranslator::class)->alias(TranslatorInterface::class, 'translator')->set('identity_translator', IdentityTranslator::class);
};
