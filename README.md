![MIXvoip Logo](app/webroot/img/mixvoip.png?raw=true)
# Passmanager
Passmanager is a tool to manage passwords via folders and tags. There are 2 types of folders: the Private and the Shared Folder. Additionally to the folders there are the tags. Tags are used to control the user's access to the passwords in a folder.

The Passmanager is a Web-application and runs with PHP using the CakePHP framework

All passwords are encrypted using a Twofish encryption provided by phpseclib. 

## How to Install
### Requirements
 * A webserver with URL rewriting (Apache 2 with mod_rewrite is fine)
 * PHP >= 5.3.0 (excluding PHP 7)
 * sqlite3 module for PHP
 * sqlite3 for your operating system (to create the database) *(under Debian/Ubuntu you can use `apt-get install sqlite3`)*.
         
### Setup
1. Make sure your Webserver installation can run CakePHP ([CakePHP install instructions](http://book.cakephp.org/2.0/en/installation.html)).
2. Clone the sources from github `git clone https://github.com/mixvoip/passmanager.git`.
3. Go to the `app/webroot/db` Directory and execute the `./createDatabase.sh` Script to create the database.
4. To create the *admin* user with the default password *q1w2e3!* and the root tag run the command `sqlite3 Passmanager.db < firstUser.sql` in `app/webroot/db`. We recommend to change the password of the *admin* user as soon as possible.

 *We highly encourage you to use connections encrypted with SSL such as HTTPS to further enhance the security. We also recommend to strictly regulate the access to the `app/webroot/db` folder so that only the web server has access to it.*

## Contacts
* To contact MIXvoip S.a. please visit our [homepage](https://www.mixvoip.com/contact/)
* For questions contact us at [tech@mixvoip.com](mailto:tech@mixvoip.com)
* [Facebook](https://www.facebook.com/mixvoip)
* [Twitter](https://twitter.com/mixvoip)

##Technical Stuff
###Configuration
The main configuration file for the App is the `app/Config/AppSettings.php` file

###The Cryptology
Passmanager uses the Twofish algorithm from the phpseclib. All cryptographic functions are implemented in the `app/Vendor/CryptoWrapper.php` for simple replacement.

## License
The Project is under the [MIT License](https://opensource.org/licenses/mit-license.php).
