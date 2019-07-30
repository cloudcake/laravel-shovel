# Getting Started

## For version 1.\*

See the [version 1 documentation here](https://github.com/stephenlake/laravel-shovel/blob/44940d2a884ff0c17084abeedde0e537bb8cf5f0/docs/README.md).

## Install the package via composer

```bash
composer require stephenlake/laravel-shovel
```

## Register the service provider

This package makes use of Laravel's auto-discovery of service providers. If you are an using earlier version of Laravel (&lt; 5.4) you will need to manually register the service provider.

Add `Shovel\ShovelServiceProvider::class` to the `providers` array in `config/app.php`.

## Register middleware

### Request Middleware

The [Request Middleware](https://github.com/stephenlake/laravel-shovel/blob/master/src/Http/Middleware/ApiRequest.php) is stand-by middleware that allows you to mutate input request data and is aliased as `ApiRequest`. This is useful when your API input data casing does not match your database casing.

To use the middleware on a route or route group, use the alias as follows:

**Through route middleware:**

```php
Route::middleware(['ApiRequest'])->get('/some/api/route', function() {
  return response(['some' => 'data']);
});
```

**Through route group middleware:**

```php
Route::group(['middleware' => ['ApiRequest']], function() {

  Route::get('/some/api/route/1', function() {
    return response(['some' => 'data']);
  });

  Route::get('/some/api/route/2', function() {
    return response(['some' => 'data']);
  });

});
```

### Response Middleware

The [Response Middleware](https://github.com/stephenlake/laravel-shovel/blob/master/src/Http/Middleware/ApiResponse.php) is the class responsible for building the output response data and is aliased as `ApiResponse`.

**Through route middleware:**

```php
Route::middleware(['ApiResponse', 'ApiRequest'])->get('/some/api/route', function() {
  return response(['some' => 'data']);
});
```

**Through route group middleware:**

```php
Route::group(['middleware' => ['ApiResponse', 'ApiRequest']], function() {

  Route::get('/some/api/route/1', function() {
    return response(['some' => 'data']);
  });

  Route::get('/some/api/route/2', function() {
    return response(['some' => 'data']);
  });

});
```

# Usage

Shovel will automatically cast paginated objects, models, collections and resource object to their appropriate formats so you don't need to.

## Basic

### Regular Responses

Imagine your project contains a `Post` model.

```php
response(Post::first());
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
```

## Messages

### Customizing messages

You can easily override messages using the `->withMeta()` method:

```php
response()
    ->withMeta('status', 'error')
    ->withMeta('code', 500)
    ->withMeta('message', 'This is my error message');
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

### Multiple messages

There may be situations where the single error message response does not suit your needs, you may define multiple message lines:

```php
response()->withMeta('messages', [
  'This is my first error',
  'This is my second error'
]);
```

## Pagination

When working with paginated models, collections or resources, shovel does the dirty work for you, and there's no additional code required, the output however has a few additional attributes:

```php
response(Post::paginate());
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

For a full list of support HTTP codes and their descriptions, see the [HTTP.php](https://github.com/stephenlake/laravel-shovel/blob/master/src/Http.php) file.
