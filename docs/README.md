# Getting Started

## For version 1.*
See the [version 1 documentation here](https://github.com/stephenlake/laravel-shovel/blob/44940d2a884ff0c17084abeedde0e537bb8cf5f0/docs/README.md).

## Install the package via composer

```bash
composer require stephenlake/laravel-shovel
```

## Register the service provider

This package makes use of Laravel's auto-discovery of service providers. If you are an using earlier version of Laravel (&lt; 5.4) you will need to manually register the service provider.

Add `Shovel\ShovelServiceProvider::class` to the `providers` array in `config/app.php`.

## Publish middleware

`php artisan vendor:publish --provider="Shovel\ShovelServiceProvider" --tag="middleware"`

This will publish the middleware to app/Http/Middleware. Change this if you do not use the default namespace, remember to update the middleware class namespace as well.

# Usage

Shovel will automatically cast paginated objects, models, collections and resource object to their appropriate formats so you don't need to.

## Basic

### Regular Responses

Imagine your project contains a `Post` model.

```php
response()->shovel(Post::first());
```

Will result in the following structured result:

```json
{
  "meta": {
    "status": "success",
    "message": "OK",
    "code": 200
  },
  "data": {
    "title": "Sample Title #1",
    "body": "..."
  }
}
```

Or multiple models:
```php
response(Post::get());
```

Will result in the following structured result:

```json
{
  "meta": {
    "status": "success",
    "message": "OK",
    "code": 200
  },
  "data": [
    {
      "title": "Sample Title #1",
      "body": "..."
    },
    {
      "title": "Sample Title #2",
      "body": "..."
    }
  ]
}
```

### Copy/Paste Example

routes/web.php
```php
use Illuminate\Http\Resources\Json\Resource;
use App\User;

Route::get('/users', function(){
    return response(User::get());
});

Route::get('/users/first', function(){
    return response(User::first());
});

Route::get('/users/paginated', function(){
    return response(User::paginate());
});

Route::get('/users/resource', function(){
    return response(new Resource(User::first()));
});

Route::get('/users/resources', function(){
    return response(Resource::collection(User::get()));
});

Route::get('/users/resources/paginated', function(){
    return response(Resource::collection(User::paginate()));
});

Route::get('/error/default-code', function() {

    $message = request('message', 'This is an example error message');

    return response()->withError($message);
});

Route::get('/error/with-custom-code', function() {

    $message = request('message', 'This is an example error message');
    $code    = request('code', 422);

    return response()->withError($message, $code);
});

Route::get('/added-meta', function() {
    return response(['Foo' => 'Bar'])->withMeta('some.awesome.key', [
        'this' => 'is',
        'new'  => 'meta'
    ]);
});

Route::get('/added-messages', function() {
    return response(['Foo' => 'Bar'])->withMessage([
        'You are a message',
        'I am a message'
    ]);
});
```

### Errors

#### Setting error messages
You can easily provide error messages by appending the `->withError()` method to the shovel instance:

```php
response()->withError('This is my error', 500);
```

And will result in the following structured result:

```json
{
  "meta": {
    "status": "error",
    "message": "This is my error",
    "code": 500
  }
}
```

If you do not provide an error message, the default HTTP response message will be used for the associated HTTP status code.

```php
response()->withError(404);
```

And will result in the following structured result:

```json
{
  "meta": {
    "status": "error",
    "message": "Not found",
    "code": 404
  }
}
```

Of course you may also provide a custom error message without providing an error code which will fall back to the default '422':

```php
response()->withError('Some error was encountered');
```

And will result in the following structured result:

```json
{
  "meta": {
    "status": "error",
    "message": "Some error was encountered",
    "code": 422
  }
}
```

#### Setting multiple error messages
There may be situations where the single error message response does not suit your needs, you may define multiple message lines:

```php
response()->withMessage([
  'This is my first error',
  'This is my second error'
]);
```

## Pagination
When working with paginated models, collections or resources, shovel does the dirty work for you, and there's no additional code required, the output however has a few additional attributes:

```php
$paginatedPosts = Post::paginate();

response()->shovel($paginatedPosts);
```

Produces:
```json
{
  "meta": {
    "status": "success",
    "message": "OK",
    "code": 200,
    "pagination": {
      "records": 42312,
      "page": 1,
      "pages": 2821,
      "limit": 15
    }
  },
  "data": [
    {
      ...
    },
    {
      ...
    }
  ]
}
```

## JSON Resources
For resource objects, the same rule as pagination applies, the code doesn't change, but the output may depending on whether it's a paginated resource, collection or single object:

### Single JSON Resource
```php
use Illuminate\Http\Resources\Json\Resource;

$post = Post::first();

response(new Resource($post));
```

Produces:
```json
{
  "meta": {
    "status": "success",
    "message": "OK",
    "code": 200
  },
  "data": {
    "title": "Sample Title #1",
    "body": "..."
  }
}
```

### Collection JSON Resources
```php
use Illuminate\Http\Resources\Json\Resource;

$posts = Post::get();

response(Resource::collection($posts));
```

Produces:
```json
{
  "meta": {
    "status": "success",
    "message": "OK",
    "code": 200
  },
  "data": [
    {
      ...
    },
    {
      ...
    }
  ]
}
```

### Paginated JSON Resources
```php
use Illuminate\Http\Resources\Json\Resource;

$paginatedPosts = Post::paginate();

response(Resource::collection($paginatedPosts));
```

Produces:
```json
{
  "meta": {
    "status": "success",
    "message": "OK",
    "code": 200,
    "pagination": {
      "records": 42312,
      "page": 1,
      "pages": 2821,
      "limit": 15
    }
  },
  "data": [
    {
      ...
    },
    {
      ...
    }
  ]
}
```

## Extra Meta Data
There may be situations where you need to append additional attributes to the meta data block which can be done in two ways:

## Single Field Meta
```php
response('Some Data')->withMeta('key', 'value');
```

Produces:
```json
{
  "meta": {
    "status": "success",
    "message": "OK",
    "code": 200,
    "key": "value"
  },
  "data": "Some Data"
}
```

## Dot Notation Field Meta
```php
response('Some Data')->withMeta('my.nested.key', 'value');
```

Produces:
```json
{
  "meta": {
    "status": "success",
    "message": "OK",
    "code": 200,
    "my": {
      "nested": {
        "key": "value"
      }
    }
  },
  "data": "Some Data"
}
```

## Supported HTTP Status Codes
For a full list of support HTTP codes and their descriptions, see the [HTTP.php](https://github.com/stephenlake/laravel-shovel/blob/master/src/HTTP.php) file.
