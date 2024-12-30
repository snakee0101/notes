## About this project

This is a clone of google keep - note taking application.

<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></p>

## Frameworks

- Laravel 
- Vue

## Third-party services

- Meilisearch (PHP), Laravel Scout (PHP) - note search
- Pusher (PHP/Javascript), Laravel Echo (Javascript) - in-browser notifications
- Intervention Image (PHP) - creating thumbnails
- mpclarkson/icon-scraper (PHP) - gets favicon from site, where the link points to and displays it near the link 
- Tesseract OCR (PHP) - text recognition in images - allows searching by the text, contained in the image
- TailwindCSS - styling

## What services must be launched to make application work properly

- php artisan serve
- npm run watch
- php artisan queue:work
- meilisearch (must be previously installed on your system)
- php artisan schedule:work
- mysql