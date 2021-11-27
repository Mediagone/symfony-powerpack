<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use JsonSerializable;


final class BoolParam implements JsonSerializable
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private bool $value;
    
    public function isTrue() : bool
    {
        return $this->value;
    }
    
    public function isFalse() : bool
    {
        return $this->value === false;
    }
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    private function __construct(bool $value)
    {
        $this->value = $value;
    }
    
    
    public static function fromInteger(int $value) : self
    {
        return new self((bool)$value);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function jsonSerialize() : bool
    {
        return $this->value;
    }
    
    public function __toString() : string
    {
        return $this->value ? '1' : '0';
    }
    
    
    
}
