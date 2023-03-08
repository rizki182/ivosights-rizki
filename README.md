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
