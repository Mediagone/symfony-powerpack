<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use InvalidArgumentException;
use JsonSerializable;
use function array_map;
use function explode;
use function in_array;
use function trim;


final class FloatArrayParam implements JsonSerializable
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
    
    
    public static function fromComaSeparatedFloats(string $value) : self
    {
        $value = trim($value);
        if ($value === '') {
            return new self([]);
        }
    
        $values = array_map(static fn(string $s) => trim($s), explode(',', $value));
        foreach ($values as $val) {
            if ($val === '') {
                throw new InvalidArgumentException("Invalid coma separated floats string ($value)");
            }
        }
    
        return new self(
            array_map(static fn(string $value) => (float)$value, $values)
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
