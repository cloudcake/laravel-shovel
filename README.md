<h6 align="center">
    <img src="https://raw.githubusercontent.com/stephenlake/laravel-shovel/master/docs/assets/laravel-shovel-banner.png"/>
</h6>

<h6 align="center">
    A minimal package for shovelling data from an API to clients, for Laravel.
</h6>

<p align="center">
<a href="https://travis-ci.org/stephenlake/laravel-shovel"><img src="https://img.shields.io/travis/stephenlake/laravel-shovel/master.svg?style=flat-square" alt=""></a>
<a href="https://github.styleci.io/repos/166599210"><img src="https://github.styleci.io/repos/166599210/shield?branch=master&style=flat-square" alt=""></a>
<a href="https://scrutinizer-ci.com/g/stephenlake/laravel-shovel"><img src="https://img.shields.io/scrutinizer/g/stephenlake/laravel-shovel.svg?style=flat-square" alt=""></a>
<a href="https://packagist.org/packages/stephenlake/laravel-shovel">
<img src="https://img.shields.io/packagist/dt/stephenlake/laravel-shovel.svg?style=flat-square" alt="">
</a>
<a href="https://github.com/stephenlake/laravel-shovel"><img src="https://img.shields.io/github/release/stephenlake/laravel-shovel.svg?style=flat-square" alt=""></a>
<a href="https://github.com/stephenlake/laravel-shovel/LICENSE.md"><img src="https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square" alt=""></a>
</p>

<br><br>

# Laravel Shovel

**Laravel Shovel** is a minimalist package providing Laravel response macros to assist in rapid API development by transforming models, resources, collections, paginated objects and errors into a concise API response format. DRY.

Made with ❤️ by [Stephen Lake](http://stephenlake.github.io/)

## Getting Started

Install the package via composer.

    composer require stephenlake/laravel-shovel
    
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
By using `shovel(Post::paginate());` or `shovel(Resource::collection(Post::paginate())`. 

#### See [documentation](https://stephenlake.github.io/laravel-shovel/) for usage.

## License

This library is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
