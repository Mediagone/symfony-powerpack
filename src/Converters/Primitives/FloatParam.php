<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use JsonSerializable;


final class FloatParam implements JsonSerializable
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private float $value;
    
    public function getValue() : float
    {
        return $this->value;
    }
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    private function __construct(float $value)
    {
        $this->value = $value;
    }
    
    
    public static function fromFloat(float $value) : self
    {
        return new self($value);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function jsonSerialize() : float
    {
        return $this->value;
    }
    
    public function __toString() : string
    {
        return (string)$this->value;
    }
    
    
    
}
