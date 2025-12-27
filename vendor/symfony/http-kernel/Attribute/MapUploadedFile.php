<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\HttpKernel\Attribute;

use OmniIconDeps\Symfony\Component\HttpFoundation\Response;
use OmniIconDeps\Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestPayloadValueResolver;
use OmniIconDeps\Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use OmniIconDeps\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PARAMETER)]
class MapUploadedFile extends ValueResolver
{
    public ArgumentMetadata $metadata;
    public function __construct(
        /** @var Constraint|array<Constraint>|null */
        public Constraint|array|null $constraints = null,
        public ?string $name = null,
        string $resolver = RequestPayloadValueResolver::class,
        public readonly int $validationFailedStatusCode = Response::HTTP_UNPROCESSABLE_ENTITY
    )
    {
        parent::__construct($resolver);
    }
}
