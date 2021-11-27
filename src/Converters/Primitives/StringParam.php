<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use JsonSerializable;


final class StringParam implements JsonSerializable
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
    
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    
    
    public static function fromString(string $value) : self
    {
        return new self($value);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function jsonSerialize() : string
    {
        return $this->value;
    }
    
    public function __toString() : string
    {
        return $this->value;
    }
    
    
    
}
