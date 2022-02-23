<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\IntParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\IntParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => '123'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], IntParam::class);
        (new IntParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntParam::class, $convertedParam);
        self::assertSame(123, $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '123'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], IntParam::class);
        (new IntParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntParam::class, $convertedParam);
        self::assertSame(123, $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => '123'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], IntParam::class);
        (new IntParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntParam::class, $convertedParam);
        self::assertSame(123, $convertedParam->getValue());
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
    
    
    public function test_throws_when_missing_parameter_is_required(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(
            ['name' => $paramName],
            IntParam::class,
            ['throwNotFoundOnMissingParam' => true]
        );
        
        $this->expectException(NotFoundHttpException::class);
        (new IntParamConverter())->apply($request, $param);
    }
    
    
    
}
