<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\FloatParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\FloatParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => '123.456'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatParam::class);
        (new FloatParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatParam::class, $convertedParam);
        self::assertSame(123.456, $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '123.456'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatParam::class);
        (new FloatParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatParam::class, $convertedParam);
        self::assertSame(123.456, $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => '123.456'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatParam::class);
        (new FloatParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatParam::class, $convertedParam);
        self::assertSame(123.456, $convertedParam->getValue());
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
    
    
    public function test_throws_when_missing_parameter_is_required(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(
            ['name' => $paramName],
            FloatParam::class,
            ['throwNotFoundOnMissingParam' => true]);
        
        $this->expectException(NotFoundHttpException::class);
        (new FloatParamConverter())->apply($request, $param);
    }
    
    
    
}
