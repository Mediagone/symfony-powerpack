<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\JsonParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


final class JsonParamConverter implements ParamConverterInterface
{
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function supports(ParamConverter $configuration) : bool
    {
        return $configuration->getClass() === JsonParam::class;
    }
    
    
    public function apply(Request $request, ParamConverter $configuration)
    {
        $paramName = $configuration->getName();
        
        if ($request->get($paramName)) {
            $param = JsonParam::fromString((string)$request->get($paramName));
        }
        else {
            $param = null;
        }
        
        if ($param === null && $configuration->isOptional() === false) {
            throw new NotFoundHttpException('JsonParam parameter is mandatory.');
        }
        
        $request->attributes->set($configuration->getName(), $param);
    }
    
    
    
}
