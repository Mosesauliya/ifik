# Panduan Menjalankan CodeIgniter 3 dengan Docker

Lingkungan Docker untuk projek CodeIgniter 3 ini sudah siap digunakan.

## Cara Menjalankan

1. **Jalankan Container:**
   ```bash
   docker-compose up -d --build
   ```

2. **Akses Aplikasi & Tools:**
   - **Aplikasi Web (CI3):** `http://localhost:8080`
   - **phpMyAdmin:** `http://localhost:8081`

3. **Konfigurasi Database CodeIgniter 3 (`application/config/database.php`):**
   Ubah file `application/config/database.php` agar terhubung ke container MySQL:

   ```php
   $db['default'] = array(
       'dsn'      => '',
       'hostname' => 'db',            // Gunakan nama service docker 'db'
       'username' => 'ci3_user',      // User MySQL dari docker-compose
       'password' => 'ci3_password',  // Password MySQL
       'database' => 'db_ifik',       // Nama Database
       'dbdriver' => 'mysqli',
       'dbprefix' => '',
       'pconnect' => FALSE,
       'db_debug' => (ENVIRONMENT !== 'production'),
       'cache_on' => FALSE,
       'cachedir' => '',
       'char_set' => 'utf8',
       'dbcollat' => 'utf8_general_ci',
       'swap_pre' => '',
       'encrypt'  => FALSE,
       'compress' => FALSE,
       'stricton' => FALSE,
       'failover' => array(),
       'save_queries' => TRUE
   );
   ```

4. **Perintah Docker Berguna:**
   - Melihat log container web: `docker-compose logs -f web`
   - Berhenti container: `docker-compose down`
   - Masuk ke terminal container web: `docker-compose exec web bash`
