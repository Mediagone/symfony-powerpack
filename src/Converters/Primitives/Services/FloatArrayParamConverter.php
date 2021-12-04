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
        $handlers = [
            '' => static function(string $value) {
                return FloatArrayParam::fromComaSeparatedFloats($value);
            },
        ];
        
        parent::__construct(FloatArrayParam::class, $handlers);
    }
    
    
    
}
