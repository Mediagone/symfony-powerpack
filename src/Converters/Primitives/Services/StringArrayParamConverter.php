<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\StringArrayParam;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class StringArrayParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $resolvers = [
            '' => static function($value) {
                return StringArrayParam::fromComaSeparatedStrings((string)$value);
            },
        ];
        
        parent::__construct(StringArrayParam::class, $resolvers);
    }
    
    
    
}
