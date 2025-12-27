<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpKernel\DataCollector;

use OmniIconDeps\Symfony\Component\HttpFoundation\Request;
use OmniIconDeps\Symfony\Component\HttpFoundation\Response;
use OmniIconDeps\Symfony\Contracts\Service\ResetInterface;
/**
 * DataCollectorInterface.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface DataCollectorInterface extends ResetInterface
{
    /**
     * Collects data for the given Request and Response.
     *
     * @return void
     */
    public function collect(Request $request, Response $response, ?\Throwable $exception = null);
    /**
     * Returns the name of the collector.
     *
     * @return string
     */
    public function getName();
}
