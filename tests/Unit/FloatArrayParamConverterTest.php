<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\FloatArrayParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\FloatArrayParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers FloatArrayParamConverter
 */
final class FloatArrayParamConverterTest extends TestCase
{
    public function test_only_supports_FloatArrayParam(): void
    {
        $floatArrayParam = new ParamConverter([], FloatArrayParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new FloatArrayParamConverter())->supports($floatArrayParam));
        self::assertFalse((new FloatArrayParamConverter())->supports($fooParam));
    }
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => '1.2,3.4,5.6'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatArrayParam::class);
        (new FloatArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatArrayParam::class, $convertedParam);
        self::assertSame([1.2,3.4,5.6], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '1.2,3.4,5.6'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatArrayParam::class);
        (new FloatArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatArrayParam::class, $convertedParam);
        self::assertSame([1.2,3.4,5.6], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => '1.2,3.4,5.6'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], FloatArrayParam::class);
        (new FloatArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FloatArrayParam::class, $convertedParam);
        self::assertSame([1.2,3.4,5.6], $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], FloatArrayParam::class, [], true);
        (new FloatArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    public function test_throws_when_missing_parameter_is_required(): void
    {
        $request = new Request();
      
        $paramName = 'foo';
        $param = new ParamConverter(
            ['name' => $paramName],
            FloatArrayParam::class,
            ['throwNotFoundOnMissingParam' => true]
        );
      
        $this->expectException(NotFoundHttpException::class);
        (new FloatArrayParamConverter())->apply($request, $param);
    }
    
    
    
}
