## About this project

This is a clone of google keep - note taking application.

<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></p>

## Features

- Basic **note editing** (with HTML markup - trix editor)
- Note **pinning/unpinning**
- **Reminders** (user sets reminder at a given date-time (can be repeated at a specified interval) and then scheduler sends in-browser notification)
- Note **collaboration** (you can see and edit notes that are shared with you) 
- You can change **color** of the note
- You can attach **images** and **drawings** (that are created with canvas editor) to the note
- You can create and attach multiple **tags** to the note and then **filter notes by tag**
- You can **take a photo** using laptop/phone camera
- **Checklist** inside a note (with ability to check, uncheck and delete tasks)
- Note **search** (by type, color, tag and content (including text, recognized from images))
- **Theme** switcher (dark/light theme)
- Note **archiving**
- Note can be **temporary deleted** (notes are automatically deleted after 7 days)
- Note **duplication**
- **Links** (which are in the note) are shown in separate panel with their favicons 

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
- Trix editor (Javascript) - WYSIWYG editor for notes content

## What services must be launched to make application work properly

- php artisan serve
- npm run watch
- php artisan queue:work
- meilisearch (must be previously installed on your system)
- php artisan schedule:work
- mysql


## Known issues

- Dropdown menu in DrawingDialogComponent (vue) doesn't work when drawing editor was called when editing a note
- Sudden darkening of background in a drawing after saving/reopening it