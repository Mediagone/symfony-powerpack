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
    
    private array $handlers;
    
    private bool $catchExceptions;
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    protected function __construct(string $className, array $handlers, bool $catchThrowable = true)
    {
        $this->className = $className;
        $this->handlers = (static fn(callable ...$handlers) => $handlers)(...$handlers);
        $this->catchExceptions = $catchThrowable;
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
        
        foreach ($this->handlers as $handlerKey => $handler) {
            $requestParam = $request->get($paramName.$handlerKey);
            
            if ($requestParam !== null) {
                if ($this->catchExceptions) {
                    try {
                        $param = $handler($requestParam);
                    } catch (Throwable $ex) {
                        $param = null;
                    }
                }
                else {
                    $param = $handler($requestParam);
                }
                
                if ($param !== null) {
                    break;
                }
            }
        }
        
        if ($param === null && $configuration->isOptional() === false) {
            $shortClassName = (new ReflectionClass($this->className))->getShortName();
            
            throw new NotFoundHttpException("$shortClassName not found (invalid or missing '$$paramName' parameter).");
        }
        
        $request->attributes->set($configuration->getName(), $param);
        
        return true;
    }
    
    
    
}
