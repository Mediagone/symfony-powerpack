<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\BoolArrayParam;
use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\BoolArrayParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers BoolArrayParamConverter
 */
final class BoolArrayParamConverterTest extends TestCase
{
    public function test_only_supports_BoolArrayParam(): void
    {
        $boolArrayParam = new ParamConverter([], BoolArrayParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new BoolArrayParamConverter())->supports($boolArrayParam));
        self::assertFalse((new BoolArrayParamConverter())->supports($fooParam));
    }
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => '1,0,1'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class);
        (new BoolArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolArrayParam::class, $convertedParam);
        self::assertSame([true,false,true], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => '1,0,1'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class);
        (new BoolArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolArrayParam::class, $convertedParam);
        self::assertSame([true,false,true], $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => '1,0,1'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class);
        (new BoolArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(BoolArrayParam::class, $convertedParam);
        self::assertSame([true,false,true], $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], BoolArrayParam::class, [], true);
        (new BoolArrayParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    
}
