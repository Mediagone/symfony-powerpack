<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use JsonSerializable;
use function explode;
use function in_array;


final class ArrayParam implements JsonSerializable
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private array $value;
    
    public function getValue() : array
    {
        return $this->value;
    }
    
    public function contains($value) : bool
    {
        return in_array($value, $this->value, true) ;
    }
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    private function __construct(array $value)
    {
        $this->value = $value;
    }
    
    
    public static function fromArray(array $value) : self
    {
        return new self($value);
    }
    
    
    public static function fromComaSeparatedStrings(string $value) : self
    {
        return new self(explode(',', $value));
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function jsonSerialize() : array
    {
        return $this->value;
    }
    
    public function __toString() : string
    {
        return implode(', ', $this->value);
    }
    
    
}
