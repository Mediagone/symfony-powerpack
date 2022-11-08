<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Converters\Primitives;

use LogicException;
use Mediagone\Symfony\PowerPack\Converters\Primitives\JsonParam;
use Mediagone\Symfony\PowerPack\NotImplementedException;
use PHPUnit\Framework\TestCase;
use function json_encode;


/**
 * @covers JsonParam
 */
final class JsonParamTest extends TestCase
{
    public function test_can_be_decoded_to_json(): void
    {
        $object = (object)['message' => 'This is a sentence.'];
        $param = JsonParam::fromString(json_encode($object));
        
        self::assertEquals($object, $param->getValue());
    }
    
    
    public function test_can_access_inner_json_data(): void
    {
        $object = (object)['message' => 'This is a sentence.'];
        $param = JsonParam::fromString(json_encode($object));
        
        self::assertSame($object->message, $param->message);
    }
    
    
    public function test_can_tell_if_json_contains_data(): void
    {
        $object = (object)['message' => 'This is a sentence.'];
        $param = JsonParam::fromString(json_encode($object));
        
        self::assertTrue(isset($param->message));
        self::assertFalse(isset($param->code));
    }
    
    
    public function test_throws_error_on_inexistent_inner_json_data(): void
    {
        $this->expectException(LogicException::class);
        
        $param = JsonParam::fromString(json_encode((object)[]));
        $param->message; // "message" key does not exists
    }
    
    
    public function test_throws_error_when_modifying_inner_json_data(): void
    {
        $this->expectException(NotImplementedException::class);
        
        $param = JsonParam::fromString(json_encode((object)[]));
        $param->message = 'This object is readonly!';
    }
    
    
    
}
