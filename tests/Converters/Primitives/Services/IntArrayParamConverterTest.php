<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Converters\Primitives\Services;

use Mediagone\Symfony\PowerPack\Converters\Primitives\IntArrayParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\IntArrayParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers IntArrayParamConverter
 */
final class IntArrayParamConverterTest extends TestCase
{
    public function test_only_supports_IntArrayParam(): void
    {
        $intArrayParam = new ParamConverter([], IntArrayParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new IntArrayParamConverter())->supports($intArrayParam));
        self::assertFalse((new IntArrayParamConverter())->supports($fooParam));
    }
    
    
    public function validValuesProvider() : iterable
    {
        yield ['', []];
        yield [' ', []];
        yield ['1,2,3', [1, 2, 3]];
        yield ['1.234,2,3', [1, 2, 3]];
        yield ['1, 2 ,3', [1, 2, 3]];
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
        
        $param = new ParamConverter(['name' => $paramName], IntArrayParam::class);
        (new IntArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntArrayParam::class, $convertedParam);
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
        
        $param = new ParamConverter(['name' => $paramName], IntArrayParam::class);
        (new IntArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntArrayParam::class, $convertedParam);
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
        
        $param = new ParamConverter(['name' => $paramName], IntArrayParam::class);
        (new IntArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntArrayParam::class, $convertedParam);
        self::assertSame($converted, $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], IntArrayParam::class, [], true);
        (new IntArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
