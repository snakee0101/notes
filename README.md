## About this project

This is my sample project - a clone of google keep - note taking application.

<p align="center"><img src="https://github.com/snakee0101/notes/raw/refs/heads/master/application%20screenshot.png" style="width: 100%"></p>

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

## How to run the application

1. run this command from project directory: 
sudo docker compose up --build

2. after starting the application run these commands (get id of the laravel-app container through docker ps):
sudo docker exec -it container-id-of-laravel-app-container php artisan migrate:fresh --seed

3. Site is accessible at http://127.0.0.1:8000/

4. You can register a new user through page http://127.0.0.1:8000/register

4. You can login with email "testing@gmail.com" and password "password" (login page url: http://127.0.0.1:8000/login)


## Frameworks and technologies

- HTML, CSS, JavaScript, PHP, SQL
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

## Known issues

- Dropdown menu in DrawingDialogComponent (vue) doesn't work when drawing editor was called when editing a note
- Sudden darkening of background in a drawing after saving/reopening it

## Author
Lebediantsev Danylo, snakee0101@gmail.com