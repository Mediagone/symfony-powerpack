<?php

declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Types\Primitives\Resolvers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Throwable;
use function is_a;


abstract class ValueParamResolver implements ValueResolverInterface
{
    private string $className;

    /** @var callable[] */
    private array $resolvers;

    private bool $convertResolverExceptionsToNull;

    /**
     * @param class-string $className
     * @param callable[] $resolvers
     */
    protected function __construct(string $className, array $resolvers, bool $convertResolverExceptionsToNull = false)
    {
        $this->className = $className;
        $this->resolvers = (static fn (callable ...$resolvers) => $resolvers)(...$resolvers);
        $this->convertResolverExceptionsToNull = $convertResolverExceptionsToNull;
    }

    /**
     * @return array|null[]
     */
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        if ($argument->isVariadic() || $argument->getType() === null) {
            return [];
        }

        if (! is_a($argument->getType(), $this->className, true)) {
            return [];
        }

        $value = null;
        $paramName = $argument->getName();

        foreach ($this->resolvers as $resolverSuffix => $resolver) {
            // Try to extract the parameter value from the request's bags attribute/query/request (PATH/GET/POST)...
            $requestParam = $request->get($paramName.$resolverSuffix);

            // ...or from json requests' body
            if ($requestParam === null && $request->getContentTypeFormat() === 'json') {
                $jsonParams = $request->toArray();
                $requestParam = $jsonParams[$paramName.$resolverSuffix] ?? null;
            }

            if ($requestParam !== null) {
                if ($this->convertResolverExceptionsToNull) {
                    try {
                        $value = $resolver($requestParam);
                    } catch (Throwable) {
                        $value = null;
                    }
                } else {
                    $value = $resolver($requestParam);
                }

                if ($value !== null) {
                    break;
                }
            }
        }

        if ($value === null && !$argument->isNullable()) {
            return [];
        }

        return [$value];
    }
}
