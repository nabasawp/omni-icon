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

use OmniIconDeps\Symfony\Bundle\FrameworkBundle\KernelBrowser;
use OmniIconDeps\Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use OmniIconDeps\Symfony\Component\BrowserKit\CookieJar;
use OmniIconDeps\Symfony\Component\BrowserKit\History;
use OmniIconDeps\Symfony\Component\DependencyInjection\ServiceLocator;
use OmniIconDeps\Symfony\Component\HttpKernel\EventListener\SessionListener;
return static function (ContainerConfigurator $container) {
    $container->parameters()->set('test.client.parameters', []);
    $container->services()->set('test.client', KernelBrowser::class)->args([service('kernel'), param('test.client.parameters'), service('test.client.history'), service('test.client.cookiejar')])->share(\false)->public()->set('test.client.history', History::class)->share(\false)->set('test.client.cookiejar', CookieJar::class)->share(\false)->set('test.session.listener', SessionListener::class)->args([service_locator(['session_factory' => service('session.factory')->ignoreOnInvalid()]), param('kernel.debug'), param('session.storage.options')])->tag('kernel.event_subscriber')->set('test.service_container', TestContainer::class)->args([service('kernel'), 'test.private_services_locator'])->public()->set('test.private_services_locator', ServiceLocator::class)->args([abstract_arg('callable collection')])->public();
};
