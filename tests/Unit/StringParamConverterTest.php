<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\StringParamConverter;
use Mediagone\Symfony\PowerPack\Converters\Primitives\StringParam;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers StringParamConverter
 */
final class StringParamConverterTest extends TestCase
{
    public function test_only_supports_StringParam(): void
    {
        $strParam = new ParamConverter([], StringParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new StringParamConverter())->supports($strParam));
        self::assertFalse((new StringParamConverter())->supports($fooParam));
    }
    
    
    public function validValuesProvider() : iterable
    {
        yield ['', ''];
        yield [1, '1'];
        yield [true, '1'];
        yield [1.2345, '1.2345'];
        yield ['Hello ', 'Hello '];
        yield ['Hello :)', 'Hello :)'];
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_GET($value, string $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => $value] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], StringParam::class);
        (new StringParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_POST($value, string $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => $value] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], StringParam::class);
        (new StringParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_attribute($value, string $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => $value] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], StringParam::class);
        (new StringParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], StringParam::class, [], true);
        (new StringParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
