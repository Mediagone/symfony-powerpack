<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\FloatParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\FloatParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers FloatParamConverter
 */
final class FloatParamConverterTest extends TestCase
{
    public function test_only_supports_FloatParam(): void
    {
        $floatParam = new ParamConverter([], FloatParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new FloatParamConverter())->supports($floatParam));
        self::assertFalse((new FloatParamConverter())->supports($fooParam));
    }
    
    
    public function validValuesProvider() : iterable
    {
        yield ['', 0.];
        yield ['1', 1.];
        yield ['12.34', 12.34];
        yield [true, 1.];
        yield [false, 0.];
    }
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_GET($value, float $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => $value] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatParam::class);
        (new FloatParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_POST($value, float $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => $value] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatParam::class);
        (new FloatParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_attribute($value, float $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => $value] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatParam::class);
        (new FloatParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], FloatParam::class, [], true);
        (new FloatParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
