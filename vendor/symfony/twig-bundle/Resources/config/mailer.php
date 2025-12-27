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

use OmniIconDeps\Symfony\Bridge\Twig\Mime\BodyRenderer;
use OmniIconDeps\Symfony\Component\Mailer\EventListener\MessageListener;
use OmniIconDeps\Symfony\Component\Mime\BodyRendererInterface;
return static function (ContainerConfigurator $container) {
    $container->services()->set('twig.mailer.message_listener', MessageListener::class)->args([null, service('twig.mime_body_renderer')])->tag('kernel.event_subscriber')->set('twig.mime_body_renderer', BodyRenderer::class)->args([service('twig')])->alias(BodyRendererInterface::class, 'twig.mime_body_renderer');
};
