# zookeeper_poc
Zookeeper POC with PHP

# Prerequisite
- Install Java
```
vboxuser@zkU22VM:~$ java -version
openjdk version "11.0.21" 2023-10-17
OpenJDK Runtime Environment (build 11.0.21+9-post-Ubuntu-0ubuntu122.04)
OpenJDK 64-Bit Server VM (build 11.0.21+9-post-Ubuntu-0ubuntu122.04, mixed mode, sharing)
```
- Install PHP7.2
  - sudo apt install php7.2 php7.2-common php7.2-cli php7.2-fpm
  - sudo apt install php7.2-{mysql,curl,json,xsl,gd,xml,zip,xsl,soap,bcmath,mbstring,gettext,imagick}
  - sudo apt install php7.2-dev
```
vboxuser@zkU22VM:~$ php -version
PHP 7.2.34-43+ubuntu22.04.1+deb.sury.org+1 (cli) (built: Sep  2 2023 08:01:34) ( NTS )
```
**NOTE:** Initially tried with PHP version 8.2 but then later faced issue while running zookeeper server.

# Setup

- Download and untar [Apache Zookeeper C Binding](https://zookeeper.apache.org/releases.html) stable release

- Start Zookeeper Server [**Mode:** Standalone] 

Create conf/zoo.conf with below details:
```
tickTime=2000
dataDir=/home/vboxuser/zk_test/data
clientPort=2181
```
Running ZK Server:
```
$ bin/zkServer.sh start conf/zoo.cfg 
/usr/bin/java
ZooKeeper JMX enabled by default
Using config: conf/zoo.cfg
Starting zookeeper ... STARTED
```
Check Status: bin/zkServer.sh status

# Usage

> Usage: php zkService.php [connectionString] [operation] Valid operation: create, get, update and delete

1. Create
```
php zkService.php localhost:2181 create /person "Ankit G!"
```

2. Get
```
php zkService.php localhost:2181 get /person
```

3. Update
```
php zkService.php localhost:2181 update /person "New data..."
```

4. Delete
```
php zkService.php localhost:2181 delete /person
```

# References
- https://zookeeper.apache.org/doc/current/zookeeperStarted.html
- https://www.php.net/manual/en/book.zookeeper.php
- https://github.com/php-zookeeper/php-zookeeper
