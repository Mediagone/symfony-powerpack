<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\IntArrayParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\IntArrayParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => '1,2,3'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], IntArrayParam::class);
        (new IntArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntArrayParam::class, $convertedParam);
        self::assertSame([1,2,3], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '1,2,3'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], IntArrayParam::class);
        (new IntArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntArrayParam::class, $convertedParam);
        self::assertSame([1,2,3], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => '1,2,3'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], IntArrayParam::class);
        (new IntArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(IntArrayParam::class, $convertedParam);
        self::assertSame([1,2,3], $convertedParam->getValue());
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
    
    
    public function test_throws_when_missing_parameter_is_required(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(
            ['name' => $paramName],
            IntArrayParam::class,
            ['throwNotFoundOnMissingParam' => true]
        );
        
        $this->expectException(NotFoundHttpException::class);
        (new IntArrayParamConverter())->apply($request, $param);
    }
    
    
    
}
