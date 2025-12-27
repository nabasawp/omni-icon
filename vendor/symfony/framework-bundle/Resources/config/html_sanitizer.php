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

use OmniIconDeps\Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use OmniIconDeps\Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use OmniIconDeps\Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;
return static function (ContainerConfigurator $container) {
    $container->services()->set('html_sanitizer.config.default', HtmlSanitizerConfig::class)->call('allowSafeElements', [], \true)->set('html_sanitizer.sanitizer.default', HtmlSanitizer::class)->args([service('html_sanitizer.config.default')])->tag('html_sanitizer', ['sanitizer' => 'default'])->alias('html_sanitizer', 'html_sanitizer.sanitizer.default')->alias(HtmlSanitizerInterface::class, 'html_sanitizer');
};
