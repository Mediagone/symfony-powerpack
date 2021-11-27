<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack;


final class FooParam
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private string $value;
    
    public function getValue() : string
    {
        return $this->value;
    }
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    
}
