<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpClient\Exception;

use OmniIconDeps\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class TransportException extends \RuntimeException implements TransportExceptionInterface
{
}
