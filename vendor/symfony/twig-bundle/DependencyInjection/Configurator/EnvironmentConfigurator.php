<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bundle\TwigBundle\DependencyInjection\Configurator;

use OmniIconDeps\Symfony\Bridge\Twig\UndefinedCallableHandler;
use OmniIconDeps\Twig\Environment;
use OmniIconDeps\Twig\Extension\CoreExtension;
/**
 * Twig environment configurator.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class EnvironmentConfigurator
{
    public function __construct(private string $dateFormat, private string $intervalFormat, private ?string $timezone, private int $decimals, private string $decimalPoint, private string $thousandsSeparator)
    {
    }
    public function configure(Environment $environment): void
    {
        $environment->getExtension(CoreExtension::class)->setDateFormat($this->dateFormat, $this->intervalFormat);
        if (null !== $this->timezone) {
            $environment->getExtension(CoreExtension::class)->setTimezone($this->timezone);
        }
        $environment->getExtension(CoreExtension::class)->setNumberFormat($this->decimals, $this->decimalPoint, $this->thousandsSeparator);
        // wrap UndefinedCallableHandler in closures for lazy-autoloading
        $environment->registerUndefinedFilterCallback(fn($name) => UndefinedCallableHandler::onUndefinedFilter($name));
        $environment->registerUndefinedFunctionCallback(fn($name) => UndefinedCallableHandler::onUndefinedFunction($name));
    }
}
