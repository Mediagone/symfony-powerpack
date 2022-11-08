<?php declare(strict_types=1);

namespace Mediagone\Symfony\PowerPack\Converters\Primitives;

use JsonSerializable;
use LogicException;
use Mediagone\Symfony\PowerPack\NotImplementedException;
use function json_decode;
use function json_encode;


final class JsonParam implements JsonSerializable
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private $value;
    
    public function getValue()
    {
        return $this->value;
    }
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    private function __construct($value)
    {
        $this->value = $value;
    }
    
    
    public static function fromString(string $value) : self
    {
        return new self(json_decode($value, false, 512, JSON_THROW_ON_ERROR));
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
        return json_encode($this->value, JSON_THROW_ON_ERROR);
    }
    
    public function __isset(string $key) : bool
    {
        return isset($this->value->$key);
    }
    
    public function __get(string $key)
    {
        if (! isset($this->value->$key)) {
            throw new LogicException("The JSON data does not contain a '$key' key.");
        }
        
        return $this->value->$key;
    }
    
    public function __set(string $key, $value)
    {
        throw new NotImplementedException('JsonParam class is immutable, call to __set() method is not supported.');
    }
    
    
    
}
