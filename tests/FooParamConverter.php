<?php declare(strict_types=1);

namespace Tests\Mediagone\Symfony\PowerPack;

use InvalidArgumentException;
use Mediagone\Symfony\PowerPack\Converters\ValueParamConverter;


final class FooParamConverter extends ValueParamConverter
{
    public function __construct(bool $catchThrowable = true)
    {
        $resolvers = [
            'Id' => static function(string $value) {
                return new FooParam($value.'_byId');
            },
            'Name' => static function(string $value) {
                return new FooParam($value.'_byName');
            },
            'Error' => static function(string $value) {
                throw new InvalidArgumentException();
            }
        ];
        
        parent::__construct(FooParam::class, $resolvers, $catchThrowable);
    }
}
