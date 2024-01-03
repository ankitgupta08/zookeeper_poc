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

### 1. Download and untar [Apache Zookeeper C Binding](https://zookeeper.apache.org/releases.html) stable release

- Compile ZooKeeper C Binding

  From top directory:
  ```
  mvn clean install -DskipTests
  ```
  **NOTE:** Before running above mvn command, set the JAVA_HOME environment variable to the path where the JDK is installed i.e.
  ```
  export JAVA_HOME=/path/to/your/jdk
  export PATH=$JAVA_HOME/bin:$PATH
  ```
  Next, move to `zookeeper-client/zookeeper-client-c/`
  ```
  ./configure
  make
  make check
  sudo make install
  ```

- Start Zookeeper Server [**Mode:** Standalone] 

  Create conf/zoo.cfg with below details:
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

- Replicated Zookeeper (Multi-Server on a single VM) [**Mode:** Leader and Follower] 

  Create conf/zoo1.cfg, conf/zoo2.cfg and conf/zoo3.cfg
  ```
  $ cat conf/zoo1.cfg 
  tickTime=2000
  initLimit=5
  syncLimit=2
  dataDir=/home/vboxuser/zk_test/data1
  clientPort=2181
  server.1=localhost:2888:3888
  server.2=localhost:2889:3889
  server.3=localhost:2890:3890

  $ cat conf/zoo2.cfg 
  tickTime=2000
  dataDir=/home/vboxuser/zk_test/data2
  clientPort=2182
  initLimit=5
  syncLimit=2
  server.1=localhost:2888:3888
  server.2=localhost:2889:3889
  server.3=localhost:2890:3890

  $ cat conf/zoo3.cfg 
  tickTime=2000
  dataDir=/home/vboxuser/zk_test/data3
  clientPort=2183
  initLimit=5
  syncLimit=2
  server.1=localhost:2888:3888
  server.2=localhost:2889:3889
  server.3=localhost:2890:3890
  ```
  Also, create myid file in each dataDir path and add one line to it:
  For e.g:
  ```
  ~/zk_test/data2$ cat myid
  2
  ```
  The myid file consists of a single line containing only the text of that machine's id. So, myid of server 1 would contain the text "1" and nothing     else. Similarly for server 2 and 3.

  Now, Run 3 ZK Servers from 3 terminals (via bin/zkServer.sh start-foreground conf/zoo1.cfg). Same with zoo2.cfg amd zoo3.cfg

### 2. Download and untar [PHP Zookeeper Extension](https://pecl.php.net/package/zookeeper) stable release

- Compile PHP Zookeeper Extension
  ```
  $ phpize7.2
  $ ./configure --with-php-config=/usr/bin/php-config7.2 --with-libzookeeper-dir=/path/to/zookeeper-c-binding
  $ make
  $ sudo make install
  ```
  `/path/to/zookeeper-c-binding` is the install prefix of ZooKeeper C Binding, which must contain the include/zookeeper/zookeeper.h

  Next, update php.ini file to include zookeeper extension. Add below line, for eg:
  ```
  extension=/usr/lib/php/20170718/zookeeper.so
  ```

Now, You can run PHP script to connect with ZK APIs (Refer `Usage` section)

### 3. Verify Zookeeper database and logs

- Verify using [ZK CLI](https://zookeeper.apache.org/doc/current/zookeeperCLI.html) that data is replicated to all servers.
  E.g:
  ```
  $ bin/zkCli.sh -server 127.0.0.1:2182
  ```
  Change the port to connect with different server.

- Verify Transaction Logs (created in dataDir path):
  ```
  $ bin/zkTxnLogToolkit.sh ../../data2/version-2/log.400000001
  ```

# Usage

> Usage: php zkService.php [connectionString] [operation] Valid operation: create, get, update and delete

### 1. Create

  Usage: php zkStart.php [connectionString] create [nodePath] [nodeData]
  ```
  php zkStart.php localhost:2181 create /person "Ankit Gupta!"
  ```

#### 2. Get

  Usage: php zkStart.php [connectionString] get [nodePath]
  ```
  php zkStart.php localhost:2181 get /person
  ```

#### 3. Update

  Usage: php zkStart.php [connectionString] update [nodePath] [newData]
  ```
  php zkStart.php localhost:2181 update /person "Some new data..."
  ```

#### 4. Delete

  Usage: php zkStart.php [connectionString] delete [nodePath]
  ```
  php zkStart.php localhost:2181 delete /person
  ```

# References
- [Medium Page - What is ZooKeeper](https://medium.com/@gavindya/what-is-zookeeper-db8dfc30fc9b)
- [ZooKeeper Getting Started](https://zookeeper.apache.org/doc/current/zookeeperStarted.html)
- [PHP ZooKeeper Manual](https://www.php.net/manual/en/book.zookeeper.php)
- [PECL Page - ZooKeeper package](https://pecl.php.net/package/zookeeper)
- [GH - PHP ZooKeeper extension](https://github.com/php-zookeeper/php-zookeeper)
