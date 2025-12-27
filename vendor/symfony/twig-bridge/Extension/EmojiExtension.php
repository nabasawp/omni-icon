<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bridge\Twig\Extension;

use OmniIconDeps\Symfony\Component\Emoji\EmojiTransliterator;
use OmniIconDeps\Twig\Extension\AbstractExtension;
use OmniIconDeps\Twig\TwigFilter;
/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
final class EmojiExtension extends AbstractExtension
{
    private static array $transliterators = [];
    public function __construct(private readonly string $defaultCatalog = 'text')
    {
        if (!class_exists(EmojiTransliterator::class)) {
            throw new \LogicException('You cannot use the "emojify" filter as the "Emoji" component is not installed. Try running "composer require symfony/emoji".');
        }
    }
    public function getFilters(): array
    {
        return [new TwigFilter('emojify', $this->emojify(...))];
    }
    /**
     * Converts emoji short code (:wave:) to real emoji (👋).
     */
    public function emojify(string $string, ?string $catalog = null): string
    {
        $catalog ??= $this->defaultCatalog;
        try {
            $tr = self::$transliterators[$catalog] ??= EmojiTransliterator::create($catalog, EmojiTransliterator::REVERSE);
        } catch (\IntlException $e) {
            throw new \LogicException(\sprintf('The emoji catalog "%s" is not available.', $catalog), previous: $e);
        }
        return (string) $tr->transliterate($string);
    }
}
