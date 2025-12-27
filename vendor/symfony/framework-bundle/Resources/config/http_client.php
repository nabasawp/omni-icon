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

use OmniIconDeps\Http\Client\HttpAsyncClient;
use OmniIconDeps\Psr\Http\Client\ClientInterface;
use OmniIconDeps\Psr\Http\Message\ResponseFactoryInterface;
use OmniIconDeps\Psr\Http\Message\StreamFactoryInterface;
use OmniIconDeps\Symfony\Component\Cache\Adapter\TagAwareAdapter;
use OmniIconDeps\Symfony\Component\HttpClient\HttpClient;
use OmniIconDeps\Symfony\Component\HttpClient\HttplugClient;
use OmniIconDeps\Symfony\Component\HttpClient\Messenger\PingWebhookMessageHandler;
use OmniIconDeps\Symfony\Component\HttpClient\Psr18Client;
use OmniIconDeps\Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use OmniIconDeps\Symfony\Component\HttpClient\UriTemplateHttpClient;
use OmniIconDeps\Symfony\Contracts\HttpClient\HttpClientInterface;
return static function (ContainerConfigurator $container) {
    $container->services()->set('cache.http_client.pool')->parent('cache.app')->tag('cache.pool')->set('cache.http_client', TagAwareAdapter::class)->args([service('cache.http_client.pool')])->tag('cache.taggable', ['pool' => 'cache.http_client.pool'])->set('http_client.transport', HttpClientInterface::class)->factory([HttpClient::class, 'create'])->args([
        [],
        // default options
        abstract_arg('max host connections'),
    ])->call('setLogger', [service('logger')->ignoreOnInvalid()])->tag('monolog.logger', ['channel' => 'http_client'])->tag('kernel.reset', ['method' => 'reset', 'on_invalid' => 'ignore'])->set('http_client', HttpClientInterface::class)->factory('current')->args([[service('http_client.transport')]])->tag('http_client.client')->tag('kernel.reset', ['method' => 'reset', 'on_invalid' => 'ignore'])->alias(HttpClientInterface::class, 'http_client')->set('psr18.http_client', Psr18Client::class)->args([service('http_client'), service(ResponseFactoryInterface::class)->ignoreOnInvalid(), service(StreamFactoryInterface::class)->ignoreOnInvalid()])->alias(ClientInterface::class, 'psr18.http_client')->set('httplug.http_client', HttplugClient::class)->args([service('http_client'), service(ResponseFactoryInterface::class)->ignoreOnInvalid(), service(StreamFactoryInterface::class)->ignoreOnInvalid()])->alias(HttpAsyncClient::class, 'httplug.http_client')->set('http_client.abstract_retry_strategy', GenericRetryStrategy::class)->abstract()->args([abstract_arg('http codes'), abstract_arg('delay ms'), abstract_arg('multiplier'), abstract_arg('max delay ms'), abstract_arg('jitter')])->set('http_client.uri_template', UriTemplateHttpClient::class)->decorate('http_client', null, 7)->args([service('.inner'), service('http_client.uri_template_expander')->nullOnInvalid(), abstract_arg('default vars')])->set('http_client.uri_template_expander.guzzle', \Closure::class)->factory([\Closure::class, 'fromCallable'])->args([[\OmniIconDeps\GuzzleHttp\UriTemplate\UriTemplate::class, 'expand']])->set('http_client.uri_template_expander.rize', \Closure::class)->factory([\Closure::class, 'fromCallable'])->args([[inline_service(\OmniIconDeps\Rize\UriTemplate::class), 'expand']])->set('http_client.messenger.ping_webhook_handler', PingWebhookMessageHandler::class)->args([service('http_client')])->tag('messenger.message_handler');
};
