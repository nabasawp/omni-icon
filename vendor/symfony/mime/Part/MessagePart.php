<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\Mime\Part;

use OmniIconDeps\Symfony\Component\Mime\Message;
use OmniIconDeps\Symfony\Component\Mime\RawMessage;
/**
 * @final
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MessagePart extends DataPart
{
    public function __construct(private RawMessage $message)
    {
        if ($message instanceof Message) {
            $name = $message->getHeaders()->getHeaderBody('Subject') . '.eml';
        } else {
            $name = 'email.eml';
        }
        parent::__construct('', $name);
    }
    public function getMediaType(): string
    {
        return 'message';
    }
    public function getMediaSubtype(): string
    {
        return 'rfc822';
    }
    public function getBody(): string
    {
        return $this->message->toString();
    }
    public function bodyToString(): string
    {
        return $this->getBody();
    }
    public function bodyToIterable(): iterable
    {
        return $this->message->toIterable();
    }
    public function __serialize(): array
    {
        return ['message' => $this->message];
    }
    public function __unserialize(array $data): void
    {
        $this->message = $data['message'] ?? $data["\x00" . self::class . "\x00message"];
        $this->__construct($this->message);
    }
}
