<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\IntParam;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class IntParamConverter extends ValueParamConverter
{
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct()
    {
        $resolvers = [
            '' => static function($value) {
                return IntParam::fromInteger((int)$value);
            },
        ];
        
        parent::__construct(IntParam::class, $resolvers);
    }
    
    
    
}
