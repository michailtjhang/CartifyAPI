# About CartifyAPI
 CartifyAPI adalah 

## Requirements
<a href="https://laravel.com/docs/11.x/releases"><img src="https://img.shields.io/badge/laravel-v11-blue" alt="version laravel"></a>
<a href="https://www.php.net/releases/8.3.6/en.php"><img src="https://img.shields.io/badge/PHP-v8.3.6-blue" alt="version php"></a>

## Instalasi
- download zip <a href="https://github.com/michailtjhang/BizCycle/archive/refs/heads/master.zip">Klik disini</a> 
- atau clone di terminal :
    ```bash
    git clone https://github.com/michailtjhang/BizCycle.git
    ```

## Setup
- buka direktori project di terminal anda.
- ketikan command di terminal :
  ```bash
  copy .env.example .env
  ```
  untuk Linuk, ketikan command :
  ```bash
  cp .env.example .env
  ```
- instal package-package di laravel, ketikan command :
  ```bash
  composer install
  ```
- Generate app key, ketikan command :
  ```bash
  php artisan key:generate
  ```
- Generate jwt token, ketikan command :
  ```bash
  php artisan jwt:secret
  ```
### Command Run Website
- menjalanlan Laravel di website, ketikan command :
  ```bash
  php artisan serve
  ```
### Command Database
- buatlah nama database baru. Lalu sesuaikan nama database, username, dan password database di file `.env`, ketikan command :
  ```bash
  php artisan migrate
  ```
- memasukkan data table ke database, ketikan command :
  ```bash
  php artisan db:seed
  ```

## Akun Login
akun admin : email = admin@gmail.com, pw = 12345678

## Fitur

## Author
- **[Michail](https://github.com/michailtjhang)**

## Credits
- 

## License
MIT License