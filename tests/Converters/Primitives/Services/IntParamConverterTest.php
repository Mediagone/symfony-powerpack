<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\IntParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\IntParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers IntParamConverter
 */
final class IntParamConverterTest extends TestCase
{
    public function test_only_supports_IntParam(): void
    {
        $intParam = new ParamConverter([], IntParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new IntParamConverter())->supports($intParam));
        self::assertFalse((new IntParamConverter())->supports($fooParam));
    }
    
    
    public function validValuesProvider() : iterable
    {
        yield ['', 0];
        yield ['123', 123];
        yield [' 123', 123];
        yield ['123 ', 123];
        yield ['12.34', 12];
        yield [true, 1];
        yield [false, 0];
    }
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_GET($value, int $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => $value] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], IntParam::class);
        (new IntParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_POST($value, int $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => $value] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], IntParam::class);
        (new IntParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_attribute($value, int $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => $value] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], IntParam::class);
        (new IntParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], IntParam::class, [], true);
        (new IntParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
