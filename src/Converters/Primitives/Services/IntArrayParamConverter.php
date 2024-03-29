<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\IntArrayParam;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class IntArrayParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $resolvers = [
            '' => static function($value) {
                return IntArrayParam::fromComaSeparatedInts((string)$value);
            },
        ];
        
        parent::__construct(IntArrayParam::class, $resolvers);
    }
    
    
    
}
