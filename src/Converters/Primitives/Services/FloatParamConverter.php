<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\FloatParam;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class FloatParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $resolvers = [
            '' => static function(string $value) {
                return FloatParam::fromFloat((float)$value);
            },
        ];
        
        parent::__construct(FloatParam::class, $resolvers);
    }
    
    
    
}
