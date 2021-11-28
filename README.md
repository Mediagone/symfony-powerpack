# Symfony Powerpack
⚠️ This project is in experimental phase, it might be subject to changes.

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE)

This package provides code-quality features to Symfony:
1. [Generic param converters](#paramConverters)
2. [Primitive types parameters](#primitiveParameters)


## Installation
This package requires **PHP 7.4+**

Add it as Composer dependency:
```sh
$ composer require mediagone/symfony-powerpack
```


## <a name="paramConverters"></a>Generic param converter
Param Converters are the best way to convert URL or route parameters into entity or Value Object instances. They allow to extract retrieval or conversion logic, preventing code duplication and keeping your controllers clean.

*For more details, see [Symfony's documentation](https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html).*

Custom converters are very powerful, but doing Clean Code implies writing a lot of these converters. This package provides a base class that handles boilerplate code for you: you only have to define handlers that will convert the request's parameter into the desired value.  


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

The associated param converter only needs a single handler to transform the value:

```php
final class LowercaseStringParamConverter extends ValueParamConverter
{
    public function __construct()
    {
        $handlers = [
            '' => static function(string $value) {
                return new LowercaseString($value);
            },
        ];
        
        parent::__construct(LowercaseString::class, $handlers);
    }
    
}
```
The array key acts as suffix for controller's argument name, thus an empty string means that the converter will look for a request parameter with the exact same name than the controller's argument ("searched").

**Note:** *the converters is using `$request->get()` internally, so it will look successively in all request data available (Route attributes, GET and POST parameters).* 


### Entity converter

Entity converters work the exact same way, but generally imply more complexity in data retrieval.
For example, you can define multiple way of getting back an User, by registering multiple handlers in the converter:

```php
use App\Entity\User;

final class StringParamConverter extends ValueParamConverter
{
    public function __construct(UserRepository $userRepository)
    {
        $handlers = [
           'Id' => static function(string $value) use($userRepository) : ?User {
               return $userRepository->findById($value);
           },
           'Name' => static function(string $value) use($userRepository) : ?User {
               return $userRepository->findByName($value);
           },
        ];
        
        parent::__construct(User::class, $handlers);
    }
    
}
```
This way, the converter will be able to fetch the user by Id if an `userId` parameter is supplied to the controller, or by its Name if the given parameter is `userName`. It also work the same for GET, POST or route's attributes.

In other words, the request parameter name is the concatenation of the *controller's argument name* and the *handler's array key*.
```php
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;

final class ShowUserController
{
    /**
     * @Route("/users/{userId}", name="app_users")
     */
    public function __invoke(User $user): Response
    {
        // Return response...
    }
}
```


### Optional parameters

In the previous example, the converter will throw a `NotFoundHttpException` exception if no User is retrieved. \
You can disable this behavior by making the argument nullable and handle it by yourself:

```php
public function __invoke(?User $user): Response
{
    if ($user === null) {
        // ...
    }
}
```

### Exception handling

By default, all converters catch and return `null` if an exception is thrown by a handler, but you can disable this automatic catching by passing `false` as third argument in ValueParamConverter's constructor.

```php
use Mediagone\Symfony\PowerPack\Converters\Primitives\StringParam;
use Symfony\Component\Routing\Annotation\Route;

final class StringParamConverter extends ValueParamConverter
{
    public function __construct()
    {
        // ...
        
        parent::__construct(LowercaseString::class, $handlers, false); // disable exception handling
    }
    
}
```
You can also catch exceptions directly in the handler if you need to customize the return value.


## <a name="primitiveParameters"></a>Primitive types parameters
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

Again, you only have to typehint argument in your controller to get the request's values:
```php
// request URL: /do-something?id=1&options=1,1,0

public function __invoke(IntParam $id, BoolArrayParam $options): Response
{
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
