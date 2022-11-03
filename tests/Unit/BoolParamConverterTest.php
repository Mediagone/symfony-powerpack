<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\BoolParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\BoolParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers BoolParamConverter
 */
final class BoolParamConverterTest extends TestCase
{
    public function test_only_supports_BoolParam(): void
    {
        $boolParam = new ParamConverter([], BoolParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new BoolParamConverter())->supports($boolParam));
        self::assertFalse((new BoolParamConverter())->supports($fooParam));
    }
    
    
    public function validValuesProvider() : iterable
    {
        yield [true, true];
        yield [1, true];
        yield [0, false];
        yield [1.234, true];
        yield [0.0, false];
        yield ['1', true];
        yield ['', false];
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_GET($value, bool $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => $value] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolParam::class);
        (new BoolParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolParam::class, $convertedParam);
        if ($converted) {
            self::assertTrue($convertedParam->isTrue());
            self::assertFalse($convertedParam->isFalse());
        }
        else {
            self::assertFalse($convertedParam->isTrue());
            self::assertTrue($convertedParam->isFalse());
        }
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_POST($value, bool $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => $value] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolParam::class);
        (new BoolParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolParam::class, $convertedParam);
        if ($converted) {
            self::assertTrue($convertedParam->isTrue());
            self::assertFalse($convertedParam->isFalse());
        }
        else {
            self::assertFalse($convertedParam->isTrue());
            self::assertTrue($convertedParam->isFalse());
        }
    }
    
    
    /**
     * @dataProvider validValuesProvider
     */
    public function test_can_convert_from_attribute($value, bool $converted): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => $value] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolParam::class);
        (new BoolParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolParam::class, $convertedParam);
        if ($converted) {
            self::assertTrue($convertedParam->isTrue());
            self::assertFalse($convertedParam->isFalse());
        }
        else {
            self::assertFalse($convertedParam->isTrue());
            self::assertTrue($convertedParam->isFalse());
        }
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], BoolParam::class, [], true);
        (new BoolParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
