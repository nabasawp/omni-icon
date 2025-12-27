<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpKernel\Event;

use OmniIconDeps\Symfony\Component\HttpFoundation\Request;
use OmniIconDeps\Symfony\Component\HttpFoundation\Response;
use OmniIconDeps\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to execute logic after a response was sent.
 *
 * Since it's only triggered on main requests, the `getRequestType()` method
 * will always return the value of `HttpKernelInterface::MAIN_REQUEST`.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
final class TerminateEvent extends KernelEvent
{
    public function __construct(HttpKernelInterface $kernel, Request $request, private Response $response)
    {
        parent::__construct($kernel, $request, HttpKernelInterface::MAIN_REQUEST);
    }
    public function getResponse(): Response
    {
        return $this->response;
    }
}
