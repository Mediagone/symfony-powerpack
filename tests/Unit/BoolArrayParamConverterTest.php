<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use InvalidArgumentException;
use Mediagone\Symfony\PowerPack\Converters\Primitives\BoolArrayParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\BoolArrayParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers BoolArrayParamConverter
 */
final class BoolArrayParamConverterTest extends TestCase
{
    public function test_only_supports_BoolArrayParam(): void
    {
        $boolArrayParam = new ParamConverter([], BoolArrayParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new BoolArrayParamConverter())->supports($boolArrayParam));
        self::assertFalse((new BoolArrayParamConverter())->supports($fooParam));
    }
    
    
    public function validValuesProvider() : iterable
    {
        yield ['', []];
        yield ['1', [true]];
        yield ['1,0', [true, false]];
        yield ['1,0,1', [true, false, true]];
        yield [' 1 , 0 , 1 ', [true, false, true]];
        yield ['true,false', [true, true]];
        yield ['true,1.234', [true, true]];
        yield ['true,0', [true, false]];
    }
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_GET($value, array $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => $value] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class);
        (new BoolArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolArrayParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '1,0,1'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class);
        (new BoolArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolArrayParam::class, $convertedParam);
        self::assertSame([true,false,true], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => '1,0,1'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class);
        (new BoolArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolArrayParam::class, $convertedParam);
        self::assertSame([true,false,true], $convertedParam->getValue());
    }
    
    
    public function invalidValuesProvider() : iterable
    {
        yield [','];
        yield ['1,'];
        yield [',1'];
        yield ['1,,1'];
        yield ['1, ,1'];
        yield ['1,2,'];
        yield [',1,2'];
        yield ['1,2, '];
        yield [' ,1,2'];
    }
    
    /**
     * @dataProvider invalidValuesProvider
     */
    public function test_throws_on_invalid_string($value): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $paramName = 'foo';
        $request = new Request([$paramName => $value]);
        
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class);
        (new BoolArrayParamConverter())->apply($request, $param);
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class, [], true);
        (new BoolArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
