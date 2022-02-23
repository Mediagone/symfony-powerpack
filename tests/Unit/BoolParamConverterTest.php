<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\BoolParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\BoolParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => '1'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolParam::class);
        (new BoolParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolParam::class, $convertedParam);
        self::assertTrue($convertedParam->isTrue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '1'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolParam::class);
        (new BoolParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolParam::class, $convertedParam);
        self::assertTrue($convertedParam->isTrue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => '1'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolParam::class);
        (new BoolParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolParam::class, $convertedParam);
        self::assertTrue($convertedParam->isTrue());
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
