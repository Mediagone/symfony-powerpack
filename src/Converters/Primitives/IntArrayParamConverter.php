<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class IntArrayParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $handlers = [
            '' => static function(string $value) {
                return IntArrayParam::fromComaSeparatedInts($value);
            },
        ];
        
        parent::__construct(IntArrayParam::class, $handlers);
    }
    
    
    
}
