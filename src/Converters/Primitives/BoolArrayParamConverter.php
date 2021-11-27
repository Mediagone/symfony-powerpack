<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


final class BoolArrayParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $handlers = [
            '' => static function(string $value) {
                return BoolArrayParam::fromComaSeparatedBooleans($value);
            },
        ];
        
        parent::__construct(BoolArrayParam::class, $handlers);
    }
    
    
    
}
