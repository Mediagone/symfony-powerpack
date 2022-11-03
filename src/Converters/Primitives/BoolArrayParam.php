<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use InvalidArgumentException;
use JsonSerializable;
use function array_map;
use function explode;
use function in_array;


final class BoolArrayParam implements JsonSerializable
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
    
    
    public static function fromComaSeparatedBooleans(string $value) : self
    {
        $value = trim($value);
        if ($value === '') {
            return new self([]);
        }
        
        $values = array_map(static fn(string $s) => trim($s), explode(',', $value));
        foreach ($values as $val) {
            if ($val === '') {
                throw new InvalidArgumentException("Invalid coma separated booleans string ($value)");
            }
        }
        
        return new self(
            array_map(static fn(string $value) => (bool)$value, $values)
        );
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
