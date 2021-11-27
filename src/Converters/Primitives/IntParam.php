<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use JsonSerializable;


final class IntParam implements JsonSerializable
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private int $value;
    
    public function getValue() : int
    {
        return $this->value;
    }
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    private function __construct(int $value)
    {
        $this->value = $value;
    }
    
    
    public static function fromInteger(int $value) : self
    {
        return new self($value);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function jsonSerialize() : int
    {
        return $this->value;
    }
    
    public function __toString() : string
    {
        return (string)$this->value;
    }
    
    
    
}
