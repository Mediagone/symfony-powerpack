<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack\Unit;

use Mediagone\Symfony\PowerPack\Converters\Primitives\Services\StringParamConverter;
use Mediagone\Symfony\PowerPack\Converters\Primitives\StringParam;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Mediagone\Symfony\PowerPack\FooParam;


/**
 * @covers StringParamConverter
 */
final class StringParamConverterTest extends TestCase
{
    public function test_only_supports_StringParam(): void
    {
        $strParam = new ParamConverter([], StringParam::class);
        $fooParam = new ParamConverter([], FooParam::class);
        
        self::assertTrue((new StringParamConverter())->supports($strParam));
        self::assertFalse((new StringParamConverter())->supports($fooParam));
    }
    
    
    public function test_can_convert_from_GET(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [$paramName => 'A GET string'] // GET parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], StringParam::class);
        (new StringParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringParam::class, $convertedParam);
        self::assertSame('A GET string', $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_POST(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [$paramName => 'A POST string'] // POST parameters
        );
        
        $param = new ParamConverter(['name' => $paramName], StringParam::class);
        (new StringParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringParam::class, $convertedParam);
        self::assertSame('A POST string', $convertedParam->getValue());
    }
    
    
    public function test_can_convert_from_attribute(): void
    {
        $paramName = 'foo';
        $request = new Request(
            [], // GET parameters
            [], // POST parameters
            [$paramName => 'An attribute string'] // Attributes
        );
        
        $param = new ParamConverter(['name' => $paramName], StringParam::class);
        (new StringParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertInstanceOf(StringParam::class, $convertedParam);
        self::assertSame('An attribute string', $convertedParam->getValue());
    }
    
    
    public function test_returns_null_when_missing_parameter_is_optional(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], StringParam::class, [], true);
        (new StringParamConverter())->apply($request, $param);
        
        $convertedParam = $request->attributes->get($paramName);
        self::assertNull($convertedParam);
    }
    
    
    public function test_throws_when_missing_parameter_is_required(): void
    {
        $request = new Request();
        
        $paramName = 'foo';
        $param = new ParamConverter(['name' => $paramName], StringParam::class, []);
        
        $this->expectException(NotFoundHttpException::class);
        (new StringParamConverter())->apply($request, $param);
    }
    
    
    // public function test_returns_null_when_catching_exceptions(): void
    // {
    //     $paramName = 'foo';
    //     $request = new Request(
    //         [$paramName.'Error' => '']
    //     );
    //    
    //     $param = new ParamConverter(['name' => $paramName], StringParam::class, [], true);
    //     (new StringParamConverter())->apply($request, $param);
    //    
    //     $convertedParam = $request->attributes->get($paramName);
    //     self::assertNull($convertedParam);
    // }
    
    
    
}
