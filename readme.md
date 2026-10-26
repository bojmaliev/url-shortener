![Build Status](https://github.com/milon/url-shortener/actions/workflows/ci.yml/badge.svg)

# URL Shortener

A simple URL Shortener application with Laravel and [Filament](https://filamentphp.com).

## Installation

It's a Laravel `13` application with a [Filament 5](https://filamentphp.com/docs) panel as its UI, so there is no frontend build step. It needs PHP `8.3` or newer with the `bcmath` and `intl` extensions. Here are the commands you need to run one by one-

```
git clone git@github.com:milon/url-shortener.git url
cd url
composer install
cp .env.example .env
php artisan key:generate
```

Then you need to put your database credentials in the .env file. I used MySQL in this project, but any [Eloquent](https://laravel.com/docs/13.x/eloquent) supported relational database can be used. After that run these-

```
php artisan migrate
```

Then to run the development server, run this-

```
php artisan serve
```

Then you can visit the url shortener in this url- `http://127.0.0.1:8000`.

**You should also give write permission to the `storage` folder**

## Uses

You need to log in to shorten URLs and see statistics. Shortened links (`/{hash}`) are public, except private links, which only the allowed emails can open after logging in.

This project has following features-

- Login and password reset (registration is disabled)
- Proper validation for each form
- Option for private url
- Basic statistics
- Filament admin panel (all links and users for admins)

**[screenshots](screenshots.md)** (taken from the old Bootstrap UI)

## Demo

Demo application can be found on [heroku](https://milon-url-shortener.herokuapp.com/).

## Admin User

There is no way to create an admin user from the UI. You need change the value of `is_admin` in the users table directly from database to gain admin access.

You can also run `php artisan db:seed` to create an admin user with the following credentials-

| Name | Value |
|------|-----------|
| Name | Admin User |
| Email | admin@url.com |
| Password | password |

## CLI Command

There is also two cli command for ease of use. One for shortening the URL and another for resolve a shortened URL. You can get the usage form this command-

```
php artisan url:short --help
php artisan url:resolve --help
```

## API

This project comes with 2 basic API endpoints to create and retrieve shorten links. Standard token based authentication is used here. Token can be generated and obtained from the profile page. A [postman collection](UrlShortener.postman_collection.json) can be downloaded for the demonstration.

#### `GET /api/links/{hash}`

**Request**

```
curl -i -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer vzjxb8palszAMKXDtZg4rn5lapZe4m" localhost:8000/api/links/AKKJ5g
```

**Response**
```
{
    "error": false,
    "link": {
        "created_at": "07-11-2019 09:03",
        "hash": "AKKJ5g",
        "is_private": false,
        "url": "https://laravel.com/docs/6.x/eloquent-mutators#attribute-casting",
        "user": null
    }
}
```

#### `POST /api/links`

**Request**

```
curl -X POST -d '{"url":"https://laravel.com/docs/6.x/validation","is_private":0,"allowed_email":"contact@milon.im, test@gmail.de"}' -H "Accept: application/json" -H "Content-Type: application/json" -H "Authorization: Bearer vzjxb8palszAMKXDtZg4rn5lapZe4m" localhost:8000/api/links
```

**Response**

```
{
    "error": false,
    "link": {
        "url": "https://laravel.com/docs/6.x/validation",
        "hash": "0HPOTS",
        "is_private": true,
        "allowed_email": [
            "contact@milon.im",
            "test@gmail.de"
        ],
        "created_at": "08-11-2019 13:12",
        "user": {
            "name": "Milon",
            "email": "contact@milon.im",
            "created_at": "07-11-2019 09:04"
        }
    }
}
```

## Testing

**Unit Test**

Tests run against an in-memory `sqlite` database (see `phpunit.xml`). Run the following command to run test-

```
php artisan test
```

**Browser Test**

I used [Laravel Dusk](https://laravel.com/docs/13.x/dusk) for browser test. To install it's dependencies run the following command-

```
php artisan dusk:install
```

Make sure you start your server and add the homepage URL in `APP_URL` config in `.env.testing` file. Then run the following command-

```
php artisan dusk
```

## Developer

Nuruzzaman Milon<br>
contact@milon.im<br>
<https://milon.im>
