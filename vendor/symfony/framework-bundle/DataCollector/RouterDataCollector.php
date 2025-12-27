<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Bundle\FrameworkBundle\DataCollector;

use OmniIconDeps\Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use OmniIconDeps\Symfony\Component\HttpFoundation\Request;
use OmniIconDeps\Symfony\Component\HttpKernel\DataCollector\RouterDataCollector as BaseRouterDataCollector;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class RouterDataCollector extends BaseRouterDataCollector
{
    public function guessRoute(Request $request, mixed $controller): string
    {
        if (\is_array($controller)) {
            $controller = $controller[0];
        }
        if ($controller instanceof RedirectController && $request->attributes->has('_route')) {
            return $request->attributes->get('_route');
        }
        return parent::guessRoute($request, $controller);
    }
}
