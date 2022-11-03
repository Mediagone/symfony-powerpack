<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\FloatArrayParam;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class FloatArrayParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $resolvers = [
            '' => static function($value) {
                return FloatArrayParam::fromComaSeparatedFloats((string)$value);
            },
        ];
        
        parent::__construct(FloatArrayParam::class, $resolvers);
    }
    
    
    
}
