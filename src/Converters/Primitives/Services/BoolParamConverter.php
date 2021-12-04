<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\BoolParam;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class BoolParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $resolvers = [
            '' => static function(string $value) {
                return BoolParam::fromInteger((int)$value);
            },
        ];
        
        parent::__construct(BoolParam::class, $resolvers);
    }
    
    
    
}
