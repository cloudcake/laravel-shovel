# Laravel Shovel

![downloads](https://img.shields.io/packagist/dt/stephenlake/laravel-shovel.svg?style=flat-square)
![license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)

**Laravel Shovel** is a minimalist package providing Laravel response macros to assist in rapid API development by transforming models, resources, collections, paginated objects and errors into a concise API response format. DRY.

## Getting Started

Install the package via composer.

    composer require cloudcake/laravel-shovel
    
Transform `Post::paginate();` into 
```
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
  "data": [{...},{...},{...}]
}
```
Using regular methods, `response(Post::paginate());` or `response(Resource::collection(Post::paginate())`. 

See [documentation](https://cloudcake.github.io/laravel-shovel/#/) for more information.

## License

This library is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
