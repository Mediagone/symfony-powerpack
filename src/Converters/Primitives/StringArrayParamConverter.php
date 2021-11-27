<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


final class StringArrayParamConverter implements ParamConverterInterface
{
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function supports(ParamConverter $configuration) : bool
    {
        return $configuration->getClass() === StringArrayParam::class;
    }
    
    
    public function apply(Request $request, ParamConverter $configuration)
    {
        $paramName = $configuration->getName();
        $param = null;
        
        if ($request->get($paramName) !== null) {
            $param = StringArrayParam::fromComaSeparatedStrings($request->get($paramName));
        }
        
        if ($param === null && $configuration->isOptional() === false) {
            throw new NotFoundHttpException('StringArrayParam parameter is mandatory.');
        }
        
        $request->attributes->set($configuration->getName(), $param);
    }
    
    
    
}