<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;


abstract class ValueParamConverter implements ParamConverterInterface
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private string $className;
    
    private array $resolvers;
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    protected function __construct(string $className, array $resolvers)
    {
        $this->className = $className;
        $this->resolvers = (static fn(callable ...$resolvers) => $resolvers)(...$resolvers);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function supports(ParamConverter $configuration) : bool
    {
        return $configuration->getClass() === $this->className;
    }
    
    
    public function apply(Request $request, ParamConverter $configuration)
    {
        $param = null;
        $paramName = $configuration->getName();
        
        $options = $configuration->getOptions();
        
        foreach ($this->resolvers as $resolverKey => $resolver) {
            $requestParam = $request->get($paramName.$resolverKey);
            if ($requestParam === null && $request->getContentType() === 'json') {
                $params = $request->toArray();
                $requestParam = $params[$paramName.$resolverKey] ?? null;
            }
            
            if ($requestParam !== null) {
                $convertResolverExceptionsToNull = $options['convertResolverExceptionsToNull'] ?? false;
                if ($convertResolverExceptionsToNull) {
                    try {
                        $param = $resolver($requestParam);
                    } catch (Throwable $ex) {
                        $param = null;
                    }
                }
                else {
                    $param = $resolver($requestParam);
                }
                
                if ($param !== null) {
                    break;
                }
            }
        }
        
        if ($param === null && $configuration->isOptional() === false) {
            return false;
        }
        
        $request->attributes->set($configuration->getName(), $param);
        
        return true;
    }
    
    
    
}
