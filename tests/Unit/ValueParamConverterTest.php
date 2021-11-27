<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use InvalidArgumentException;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Mediagone\Symfony\PowerPack\BarParam;
use Tests\Mediagone\Symfony\PowerPack\FooParam;
use Tests\Mediagone\Symfony\PowerPack\FooParamConverter;


/**
 * @covers ValueParamConverter
 */
final class ValueParamConverterTest extends TestCase
{
    public function test_can_tell_if_class_is_supported(): void
    {
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new FooParamConverter())->supports($fooParam));
    }
    
    
    public function test_can_tell_if_class_is_not_supported(): void
    {
        $param = new ParamConverter([], BarParam::class);
        
        self::assertFalse((new FooParamConverter())->supports($param));
    }
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName.'Id' => 'found'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FooParam::class);
        (new FooParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FooParam::class, $convertedParam);
        self::assertSame('found_byId', $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName.'Id' => 'found'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FooParam::class);
        (new FooParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FooParam::class, $convertedParam);
        self::assertSame('found_byId', $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName.'Id' => 'found'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], FooParam::class);
        (new FooParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FooParam::class, $convertedParam);
        self::assertSame('found_byId', $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_secondary_handler(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName.'Name' => 'found'], // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], FooParam::class);
        (new FooParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(FooParam::class, $convertedParam);
        self::assertSame('found_byName', $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], FooParam::class, [], true);
        (new FooParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    public function test_throws_when_missing_parameter_is_required(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], FooParam::class, []);
        
        $this->expectException(NotFoundHttpException::class);
        (new FooParamConverter())->apply($request, $param);
    }
    
    
    public function test_is_not_catching_exceptions_by_default(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName.'Error' => '']
        );
        
        $param = new ParamConverter(['name' => $paramName], FooParam::class, [], true);
        
        $this->expectException(InvalidArgumentException::class);
        (new FooParamConverter(false))->apply($request, $param);
    }
    
    
    public function test_returns_null_when_catching_exceptions(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName.'Error' => '']
        );
        
        $param = new ParamConverter(['name' => $paramName], FooParam::class, [], true);
        (new FooParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
