# magnetsdb-php

Quick installation 
============================

1. Install MariaDB, see installation instructions accord. distrubution

```
yaourt mariadb
sudo su
mysql_install_db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
systemctl start mariadb.service
mysql_secure_installation
mysql -u root -p
```

In database shell:

```
CREATE DATABASE `magnetsdb` CHARACTER SET utf8  COLLATE utf8_general_ci;
SHOW DATABASES;
USE magnetsdb;
```

2. Import sample of  the database 

Mirrors

https://dl.dropbox.com/s/hkms2r0ob1yo0vb/data.sql.gz

https://mega.nz/#!BBVWwLzS!HTt1MmHF-h5AUMOoAiyfuZ8xSSzHqdVVJr2pGSeChQo

https://dl.dropbox.com/s/a4ls2kvvqa2qvbl/magnetsdb.sql.gz

https://mega.nz/#!sclGTBRL!Sr21vgf6F_jmdkoHJVk_C0ApjzozLE0UKjpowx6s9QA

3. Install and configure Apache and PHP.

4. Edit configuration of the connection at `magnetsdb.php`

5. Launch server and open /magnetsdb. 
