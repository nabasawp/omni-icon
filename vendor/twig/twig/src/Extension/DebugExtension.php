<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Twig\Extension;

use OmniIconDeps\Twig\Environment;
use OmniIconDeps\Twig\Template;
use OmniIconDeps\Twig\TemplateWrapper;
use OmniIconDeps\Twig\TwigFunction;
final class DebugExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        // dump is safe if var_dump is overridden by xdebug
        $isDumpOutputHtmlSafe = \extension_loaded('xdebug') && str_contains(\ini_get('xdebug.mode'), 'develop') && (\false === \ini_get('html_errors') || \ini_get('html_errors')) || 'cli' === \PHP_SAPI;
        return [new TwigFunction('dump', [self::class, 'dump'], ['is_safe' => $isDumpOutputHtmlSafe ? ['html'] : [], 'needs_context' => \true, 'needs_environment' => \true, 'is_variadic' => \true])];
    }
    /**
     * @internal
     */
    public static function dump(Environment $env, $context, ...$vars)
    {
        if (!$env->isDebug()) {
            return;
        }
        ob_start();
        if (!$vars) {
            $vars = [];
            foreach ($context as $key => $value) {
                if (!$value instanceof Template && !$value instanceof TemplateWrapper) {
                    $vars[$key] = $value;
                }
            }
            var_dump($vars);
        } else {
            var_dump(...$vars);
        }
        return ob_get_clean();
    }
}
