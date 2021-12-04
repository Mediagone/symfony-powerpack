<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\BoolArrayParam;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class BoolArrayParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $resolvers = [
            '' => static function(string $value) {
                return BoolArrayParam::fromComaSeparatedBooleans($value);
            },
        ];
        
        parent::__construct(BoolArrayParam::class, $resolvers);
    }
    
    
    
}
