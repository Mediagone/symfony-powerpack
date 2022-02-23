<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\StringArrayParamConverter;
use Mediagone\Symfony\PowerPack\Converters\Primitives\StringArrayParam;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers StringArrayParamConverter
 */
final class StringArrayParamConverterTest extends TestCase
{
    public function test_only_supports_StringArrayParam(): void
    {
        $strArrayParam = new ParamConverter([], StringArrayParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new StringArrayParamConverter())->supports($strArrayParam));
        self::assertFalse((new StringArrayParamConverter())->supports($fooParam));
    }
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => '1,2,3'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], StringArrayParam::class);
        (new StringArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringArrayParam::class, $convertedParam);
        self::assertSame(['1','2','3'], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '1,2,3'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], StringArrayParam::class);
        (new StringArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringArrayParam::class, $convertedParam);
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
        
        $param = new ParamConverter(['name' => $paramName], StringArrayParam::class);
        (new StringArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringArrayParam::class, $convertedParam);
        self::assertSame(['1','2','3'], $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], StringArrayParam::class, [], true);
        (new StringArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
