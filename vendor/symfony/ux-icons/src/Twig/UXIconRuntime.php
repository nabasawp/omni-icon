<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\UX\Icons\Twig;

use OmniIconDeps\Psr\Log\LoggerInterface;
use OmniIconDeps\Symfony\UX\Icons\Exception\IconNotFoundException;
use OmniIconDeps\Symfony\UX\Icons\IconRendererInterface;
use OmniIconDeps\Twig\Extension\RuntimeExtensionInterface;
/**
 * @author Simon André <smn.andre@gmail.com>
 *
 * @internal
 */
final class UXIconRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly IconRendererInterface $iconRenderer, private readonly bool $ignoreNotFound = \false, private readonly ?LoggerInterface $logger = null)
    {
    }
    /**
     * @param array<string, bool|string> $attributes
     */
    public function renderIcon(string $name, array $attributes = []): string
    {
        try {
            return $this->iconRenderer->renderIcon($name, $attributes);
        } catch (IconNotFoundException $e) {
            if ($this->ignoreNotFound) {
                $this->logger?->warning($e->getMessage());
                return '';
            }
            throw $e;
        }
    }
    public function render(array $args = []): string
    {
        $name = $args['name'];
        unset($args['name']);
        return $this->renderIcon($name, $args);
    }
}
