<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\StringArrayParamConverter;
use Mediagone\Symfony\PowerPack\Converters\Primitives\StringArrayParam;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers StringArrayParamConverter
 */
final class StringArrayParamConverterTest extends TestCase
{
    public function test_only_supports_StringArrayParam(): void
    {
        $strArrayParam = new ParamConverter([], StringArrayParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new StringArrayParamConverter())->supports($strArrayParam));
        self::assertFalse((new StringArrayParamConverter())->supports($fooParam));
    }
    
    
    public function validValuesProvider() : iterable
    {
        yield ['', []];
        yield [' ', [' ']];
        yield [',a,b', ['', 'a', 'b']];
        yield ['a,,b', ['a', '', 'b']];
        yield ['a,b,', ['a', 'b', '']];
        yield ['1,2,3', ['1', '2', '3']];
        yield ['a,b,c', ['a', 'b', 'c']];
        yield [' a , b , c ', [' a ', ' b ', ' c ']];
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
        
        $param = new ParamConverter(['name' => $paramName], StringArrayParam::class);
        (new StringArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringArrayParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_POST($value, array $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => $value] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], StringArrayParam::class);
        (new StringArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringArrayParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_attribute($value, array $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => $value] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], StringArrayParam::class);
        (new StringArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringArrayParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], StringArrayParam::class, [], true);
        (new StringArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
