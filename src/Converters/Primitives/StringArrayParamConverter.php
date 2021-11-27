<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class StringArrayParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $handlers = [
            '' => static function(string $value) {
                return StringArrayParam::fromComaSeparatedStrings($value);
            },
        ];
        
        parent::__construct(StringArrayParam::class, $handlers);
    }
    
    
    
}
