<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters;

use ReflectionClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;


abstract class ValueParamConverter implements ParamConverterInterface
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private string $className;
    
    private array $resolvers;
    
    private bool $catchExceptions;
    
    private bool $throwOnMissingParam;
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    protected function __construct(string $className, array $resolvers, bool $catchThrowable = true, bool $throwOnMissingParam = false)
    {
        $this->className = $className;
        $this->resolvers = (static fn(callable ...$resolvers) => $resolvers)(...$resolvers);
        $this->catchExceptions = $catchThrowable;
        $this->throwOnMissingParam = $throwOnMissingParam;
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
        $paramName = $configuration->getName();
        $param = null;
        
        foreach ($this->resolvers as $resolverKey => $resolver) {
            $requestParam = $request->get($paramName.$resolverKey);
            
            if ($requestParam !== null) {
                if ($this->catchExceptions) {
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
            $shortClassName = (new ReflectionClass($this->className))->getShortName();
            
            if ($this->throwOnMissingParam) {
                throw new NotFoundHttpException("$shortClassName not found (invalid or missing '$$paramName' parameter).");
            }
            
            return false;
        }
        
        $request->attributes->set($configuration->getName(), $param);
        
        return true;
    }
    
    
    
}
