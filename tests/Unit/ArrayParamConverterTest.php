<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\ArrayParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\ArrayParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers ArrayParamConverter
 */
final class ArrayParamConverterTest extends TestCase
{
    public function test_only_supports_ArrayParam(): void
    {
        $ArrayParam = new ParamConverter([], ArrayParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new ArrayParamConverter())->supports($ArrayParam));
        self::assertFalse((new ArrayParamConverter())->supports($fooParam));
    }
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => '1,2,3'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], ArrayParam::class);
        (new ArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(ArrayParam::class, $convertedParam);
        self::assertSame(['1','2','3'], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '1,2,3'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], ArrayParam::class);
        (new ArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(ArrayParam::class, $convertedParam);
        self::assertSame(['1','2','3'], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => '1,2,3'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], ArrayParam::class);
        (new ArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(ArrayParam::class, $convertedParam);
        self::assertSame(['1','2','3'], $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], ArrayParam::class, [], true);
        (new ArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    public function test_throws_when_missing_parameter_is_required(): void
    {
        $request = new Request();
       
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], ArrayParam::class, []);
       
        $this->expectException(NotFoundHttpException::class);
        (new ArrayParamConverter())->apply($request, $param);
    }
    
    
    
}
