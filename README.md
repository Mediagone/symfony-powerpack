# Symfony Powerpack
⚠️ This project is in experimental phase, it might be subject to changes.

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)

This package provides efficiency and code-quality helpers for Symfony:
1. [Generic param converters](#paramConverters)
2. [Primitive types parameters](#primitiveParameters)


## Installation
This package requires **PHP 7.4+**

Add it as Composer dependency:
```sh
composer require mediagone/symfony-powerpack
```

In order to use primitive type parameters in your controllers, you must register the converters in your `services.yaml` by adding the following service declaration:
```yaml
services:
    
    Mediagone\Symfony\PowerPack\Converters\Primitives\Services\:
        resource: '../vendor/mediagone/symfony-powerpack/src/Converters/Primitives/Services/'
```


## <a name="paramConverters"></a>1) Generic param converter
Param Converters are the best way to convert URL or route parameters into entity or Value Object instances. They allow to extract retrieval or conversion logic, preventing code duplication and keeping your controllers clean.

*For more details, see [Symfony's documentation](https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html).*

Custom converters are very powerful, but doing Clean Code implies writing a lot of these converters. This package provides a base class that handles boilerplate code for you: you only have to define resolvers that will convert the request's parameter into the desired value.  


### Value Object converter

Let's take this very basic ValueObject:

```php
final class LowercaseString
{
    private string $value;
    
    public function getValue() : string
    {
        return $this->value;
    }
    
    public function __construct(string $value)
    {
        $this->value = strtolower($value);
    }
}
```

You can use it in your controller by typehinting an argument:

```php
use Mediagone\Symfony\PowerPack\Converters\Primitives\StringParam;
use Symfony\Component\Routing\Annotation\Route;

final class SearchController
{
    /**
     * @Route("/search", name="app_search")
     */
    public function __invoke(LowercaseString $searched): Response
    {
        // Return search results...
    }
}
```

The associated param converter only needs a single resolver to transform the value:

```php
final class LowercaseStringParamConverter extends ValueParamConverter
{
    public function __construct()
    {
        $resolvers = [
            '' => static function(string $value) {
                return new LowercaseString($value);
            },
        ];
        
        parent::__construct(LowercaseString::class, $resolvers);
    }
    
}
```
The array key acts as suffix for controller's argument name, thus an empty string means that the converter will look for a request parameter with the exact same name than the controller's argument ("searched").

**Note:** *the converters is using `$request->get()` internally, so it will look successively in all request data available (Route attributes, GET and POST parameters).* 


### Entity converter

Entity converters work the exact same way, but generally imply more complexity in data retrieval.
For example, you can define multiple way of getting back an User, by registering multiple resolvers in the converter:

```php
use App\Entity\User;

final class StringParamConverter extends ValueParamConverter
{
    public function __construct(UserRepository $userRepository)
    {
        $resolvers = [
           'Id' => static function(string $value) use($userRepository) : ?User {
               return $userRepository->findById($value);
           },
           'Name' => static function(string $value) use($userRepository) : ?User {
               return $userRepository->findByName($value);
           },
        ];
        
        parent::__construct(User::class, $resolvers);
    }
    
}
```
This way, the converter will be able to fetch the user by Id if an `userId` parameter is supplied to the controller, or by its Name if the given parameter is `userName`. In other words, the request parameter name is the concatenation of the *controller's argument name* and the *resolver's array key*.

Again, it works the same for GET, POST or route's attributes.

```php
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;

final class ShowUserController
{
    /**
     * @Route("/users/{userId}", name="app_user_show")
     */
    public function __invoke(User $user): Response
    {
        // Return response...
    }
}
```


### Optional parameters

If you need to allow a nullable argument, just make the argument nullable and handle it in your code (eg. to return a custom response):

```php
public function __invoke(?User $user): Response
{
    if ($user === null) {
        // do something...
    }
}
```


### Exception handling

Exceptions can be thrown in your resolvers, for example if the supplied value is not valid. In some cases, you don't need to handle those errors and you can just consider them as missing values.

You can either:
- Catch exceptions directly in the resolver, to customize the return value by yourself.
- Enable the `convertResolverExceptionsToNull` option on your controller's action, to automatically handle errors and convert the parameter value to `null`.

Example:
```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route('/search/{name}')
 * @ParamConverter("name", options={"convertResolverExceptionsToNull": true}) 
 */
public function __invoke(?LowercaseString $name): Response
{
    if ($name === null) {
        throw new InvalidArgumentException('Invalid or missing value for `$name` parameter.');
    }
    
    ...
}
```



## <a name="primitiveParameters"></a>2) Primitive types parameters
The only drawback of ParamConverters is they only work with classes but not with primitive PHP types (int, string, float...) so this package also provides a set of classes that can be used to enforce type-safety for primitive types.

| Class name | Parameter value example | Converted PHP value |
|:---|---|---|
| BoolParam | `1` or `0` | `true` or `false` |
| FloatParam |`3.14159` | `3.14159` |
| IntParam | `42` | `42` |
| StringParam | `hello` | `'hello'` |
| JsonParam | `["1","2","3"]` | `['1', '2', '3']` |

It also provides parameters to extract serialized arrays from the query, built from comma-separated values string :

| Class name | Parameter value example | Converted PHP value |
|:---|---|---|
| BoolArrayParam | `1,0,1`  | `[true, false, true]` |
| FloatArrayParam | `1.1,2.2,3.3` | `[1.1, 2.2, 3.3]` |
| IntArrayParam | `1,2,3` | `[1, 2, 3]` |
| StringArrayParam | `one,two,three` | `['one', 'two', 'three']` |

Again, you only have to typehint arguments in your controller to get the request's values:
```php
// Request URL:  /do-something?data=23&options=1,1,0

public function __invoke(IntParam $data, BoolArrayParam $options): Response
{
    $data->getValue(); // 23
    foreach ($options->getValue() as $option) {
        // ...
    }
}
```


## License

_Symfony Powerpack_ is licensed under MIT license. See LICENSE file.



[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg
[ico-version]: https://img.shields.io/packagist/v/mediagone/symfony-powerpack.svg
[ico-downloads]: https://img.shields.io/packagist/dt/mediagone/symfony-powerpack.svg

[link-packagist]: https://packagist.org/packages/mediagone/symfony-powerpack
[link-downloads]: https://packagist.org/packages/mediagone/symfony-powerpack
