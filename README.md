# Laravel Chat 🐈 for ChatGPT

[![Version](https://img.shields.io/packagist/v/ogrre/laravel-chatgpt.svg?style=flat-square)](https://packagist.org/packages/ogrre/laravel-chatgpt)
[![Licence](https://img.shields.io/github/license/ogrre/laravel-chatgpt.svg?style=flat-square)](https://github.com/ogrre/laravel-chatgpt/blob/master/LICENSE)
[![Tests](https://img.shields.io/github/workflow/status/votre-nom/librairie/Tests?label=tests&style=flat-square)](https://github.com/votre-nom/librairie/actions/workflows/tests.yml)
[![Couverture](https://img.shields.io/codecov/c/github/votre-nom/librairie?style=flat-square)](https://codecov.io/gh/votre-nom/librairie)

### Documentation

This libray need openai php client, so don't forget to add in your .env this variables:

```dotenv
# .env

OPENAI_API_KEY=
OPENAI_API_ORGANIZATION=
```

#### Installation:

To install the Laravel Chat for ChatGPT library, run the following command:

```shell
composer require ogrre/laravel-chatgpt
```

After the installation, publish the vendor files by executing the command:

```shell
php artisan vendor:publish --provider="Ogrre\ChatGPT\ChatServiceProvider"
```

By default, the service provider will be automatically registered in the `app.php` file. However, if needed, you can manually add the service provider in the `config/app.php` file:

```php
# config/app.php

'providers' => [
    // ...
    Ogrre\Media\MediaServiceProvider::class,
];
```

Finally, run the migration command to create the necessary database tables:

```shell
php artisan migrate
```

#### Usage instructions

To associate a chat with a model, the model must use the `hasChat` trait. For example, in the User model:

```php
class User extends Authenticatable
{
    use HasFactory, HasChat;
    // ...
}
```

Once the model is set up with the `hasChat` trait, you can create a new chat using the following command:

```php
$optional_title = "Translate this word";
$optional_role = "You're a helpful assistant";

$user_chat = $user->newChat($optional_title, $optional_role);
```

After creating a new chat or retrieving an existing one, you have two options to interact with it:

Using the chat object directly:
```php
$chat->gpt("say hello in French");
```

Using the model directly (in this example, the User model):
```php
$user->chatgpt("say hello in French", $chat);
```

The Chat model is a standard Laravel Model, so you can use all the usual functions, including delete and others.

Additionally, the library provides a ChatResource for displaying chats in an API, allowing for easy integration with API endpoints.
