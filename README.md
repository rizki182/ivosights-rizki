## Deploy menggunakan docker
**Clone repository**

```sh
git clone https://github.com/rizki182/ivosights-rizki.git
```

**Masuk ke folder docker**
```sh
cd ivosights-rizki/docker
```

**Deploy**
```sh
docker compose up -d
```

Setelah proses deploy selesai akses url berikut melalui browser. Jika muncul pesan **Bad Gateway** berarti dependency belum selesai diinstall, tunggu beberapa saat lalu refresh halaman beberapa kali sampai aplikasi bisa diakses
```sh
http://localhost:8080
```

Jika sudah tidak ada pesan bad gateway jalankan perintah berikut untuk mengisi database dengan data awal:
```sh
docker exec -it ivosights-rizki-php php artisan db:seed
```

Untuk melihat database, akses url berikut melalui browser :
```
http://localhost:8081
```

Lalu isi form login dengan data berikut
```
Server : mariadb
Username : ivosights
Password : ivosights
Database : db_project_rizki
```


## Deploy menggunakan php

**Requirement**
```
- php >= 8.1
- php-cli
- php-mysql
- php-xml
- php-curl
- composer
- mariadb
```

**Clone repository**
```sh
git clone https://github.com/rizki182/ivosights-rizki.git
```

**Masuk ke folder source code**
```sh
cd ivosights-rizki/code
```
Edit file **.env**, dan sesuaikan variabel database dengan konfigurasi databse anda
```
DB_HOST= {mariadb host address}
DB_PORT= {mariadb port}
DB_DATABASE= {database name (database harus sudah ada dengan data kosong )}
DB_USERNAME= {database username}
DB_PASSWORD= {database password}
```

Jalankan perintah berikut untuk install laravel dependency
```sh
composer install
```

Jalankan perintah berikut untuk import skema database
```sh
php artisan migrate
```

 Jalankan perintah berikut untuk mengisi database dengan data awal
 ```sh
php artisan db:seed
```

**Deploy**
```sh
php artisan serve
```

Akses aplikasi di
```
http://localhost:8000
```
## Login API dengan JWT
```
(POST) {base_url}/api/login
Form-data:
- email   : test@test.test
- password: password
```


## Troubleshoot
Jika saat deploy terjadi timeout tambahkan atau edit file **/etc/docker/daemon.json**
```sh
{
  "dns": ["8.8.8.8", "8.8.4.4"]
}
```
Lalu restart service docker
