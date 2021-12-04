<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\StringParam;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class StringParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $resolvers = [
            '' => static function(string $value) {
                return StringParam::fromString($value);
            },
        ];
        
        parent::__construct(StringParam::class, $resolvers);
    }
    
    
    
}