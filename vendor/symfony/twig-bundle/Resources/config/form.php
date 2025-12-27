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

use OmniIconDeps\Symfony\Bridge\Twig\Extension\FormExtension;
use OmniIconDeps\Symfony\Bridge\Twig\Form\TwigRendererEngine;
use OmniIconDeps\Symfony\Component\Form\FormRenderer;
return static function (ContainerConfigurator $container) {
    $container->services()->set('twig.extension.form', FormExtension::class)->args([service('translator')->nullOnInvalid()])->set('twig.form.engine', TwigRendererEngine::class)->args([param('twig.form.resources'), service('twig')])->set('twig.form.renderer', FormRenderer::class)->args([service('twig.form.engine'), service('security.csrf.token_manager')->nullOnInvalid()])->tag('twig.runtime');
};
