<p align="center">
    <img src=".github/logo.png" alt="Scavenger"><br>
  "The no nonsense pure PHP template."
</p>

A simple PHP backend project template for smaller scale projects that don't need anything extensive or complicated such
as [Laravel](https://github.com/laravel/laravel). You provide the clientside environment yourself.

## Features

As the goal of the project is to keep things _minimal_ it only contains bare minimum to get things going.

- [Twig](https://twig.symfony.com/) for easy templating.
- [Peece's simple-router](https://github.com/skipperbent/simple-php-router) for neat routing. (Seriously star it, It's
  underrated as hell!)
- [Pearls](#pearls) for file containerization.

### Pearls

Pearls are a convenient way to encapsulate and manage resources in the app. Whether it's external images, documents, or
any other files, Pearls provide a containerized approach to safeguard these assets.

They ensure that files are stored and served safely without the risk of tampering, unauthorized modifications, or
accidental execution during transport between the client and server.

#### Example:

Some script

```php
$pearl = new Pearl("test", "test");
$pearl->setData("string", DataType::Text, "a");
$pearl->setData("integer", DataType::Integer, 42);
$pearl->setData("image", DataType::Binary, file_get_contents(APP_ROOT . "/test.png"));
$pearl->flush();
```

Some other script

```php
$pearl = new Pearl("test", "test");
print("String: {$pearl->getData("string")}<br>");
print("Integer: {$pearl->getData("integer")}<br>");
print("Image: <img src=\"data:image/png;base64," . base64_encode($pearl->getData("image")) . "\" \>");
```

## Installation
1. Fork this repository then clone it to a local directory.
2. Execute `composer i` in the project directory.
3. Copy the config.ini file and modify the value(s) to your liking.
4. You've made a Scavenger project!

### Webserver configuration.
As Scavenger relies on a router script to function, you need to fall back to the index.php script in an event of the URL not being a valid path on the filesystem. e.g. `/user/1`.

Example with NGINX:
```nginx
try_files $uri $uri/ /index.php;
```

## Closing notes

The reason why I made Scavenger was because I was very displeased after seeing how complex Laravel is for the scope of
my projects, I don't need database ORMs or other fancy schmancy things.

Hopefully you'll find Scavenger useful for one of your projects.

_Rain World is a registered trademark of Videocult, LLC, all rights belong to them._  
_Lizard hiss~_ Oh sorry. _Meow..._