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

use OmniIconDeps\Symfony\Component\Translation\Bridge\Crowdin\CrowdinProviderFactory;
use OmniIconDeps\Symfony\Component\Translation\Bridge\Loco\LocoProviderFactory;
use OmniIconDeps\Symfony\Component\Translation\Bridge\Lokalise\LokaliseProviderFactory;
use OmniIconDeps\Symfony\Component\Translation\Bridge\Phrase\PhraseProviderFactory;
use OmniIconDeps\Symfony\Component\Translation\Provider\NullProviderFactory;
use OmniIconDeps\Symfony\Component\Translation\Provider\TranslationProviderCollection;
use OmniIconDeps\Symfony\Component\Translation\Provider\TranslationProviderCollectionFactory;
return static function (ContainerConfigurator $container) {
    $container->services()->set('translation.provider_collection', TranslationProviderCollection::class)->factory([service('translation.provider_collection_factory'), 'fromConfig'])->args([[]])->set('translation.provider_collection_factory', TranslationProviderCollectionFactory::class)->args([tagged_iterator('translation.provider_factory'), []])->set('translation.provider_factory.null', NullProviderFactory::class)->tag('translation.provider_factory')->set('translation.provider_factory.crowdin', CrowdinProviderFactory::class)->args([service('http_client'), service('logger'), param('kernel.default_locale'), service('translation.loader.xliff'), service('translation.dumper.xliff')])->tag('translation.provider_factory')->set('translation.provider_factory.loco', LocoProviderFactory::class)->args([service('http_client'), service('logger'), param('kernel.default_locale'), service('translation.loader.xliff'), service('translator')])->tag('translation.provider_factory')->set('translation.provider_factory.lokalise', LokaliseProviderFactory::class)->args([service('http_client'), service('logger'), param('kernel.default_locale'), service('translation.loader.xliff')])->tag('translation.provider_factory')->set('translation.provider_factory.phrase', PhraseProviderFactory::class)->args([service('http_client'), service('logger'), service('translation.loader.xliff'), service('translation.dumper.xliff'), service('cache.app'), param('kernel.default_locale')])->tag('translation.provider_factory');
};
