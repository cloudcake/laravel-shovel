<h6 align="center">
    <img src="https://github.com/stephenlake/laravel-shovel/blob/master/docs/assets/laravel-shovel.png" width="450"/>
</h6>

<h6 align="center">
    A minimal package for shovelling data from an API to clients, for Laravel.
</h6>

# Getting Started

## Install the package via composer.

```bash
composer require stephenlake/laravel-shovel
```

## Register the service provider.

This package makes use of Laravel's auto-discovery of service providers. If you are an using earlier version of Laravel (&lt; 5.4) you will need to manually register the service provider.

Add `Shovel\ShovelServiceProvider::class` to the `providers` array in `config/app.php`.

That's it. See the usage section for examples.

# Usage

Shovel will automatically cast paginated objects, models, collections and resource object to their appropriate formats so you don't need to.

## Basic Examples

## Pagination Examples

## JSON Resource Examples

## Custom Error Messages

### Single Error Messages

### Multiple Error Messages

## Extra Meta Data

### Single Key Data 

### Nested Keys
