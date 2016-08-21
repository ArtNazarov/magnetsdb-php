# magnetsdb-php

Quick installation 
============================

Install MariaDB, see installation instructions accord. distrubution

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
SOURCE path/to/data.sql
```

Import sample of  the database 

Mirrors

https://dl.dropbox.com/s/mvpltkb9slh2b0w/data.sql.zip

https://mega.nz/#!ZI0BHR4L!LgSSt2ln32wEG2VTZIufaL8USOLZNDU3ODspqdUoGcs

https://dl.dropbox.com/s/a4ls2kvvqa2qvbl/magnetsdb.sql.gz

https://mega.nz/#!sclGTBRL!Sr21vgf6F_jmdkoHJVk_C0ApjzozLE0UKjpowx6s9QA

Install and configure Apache and PHP.

Edit configuration of the connection at `magnetsdb.php`

Launch server and open /magnetsdb. 
