![MIXvoip Logo](app/webroot/img/mixvoip.png?raw=true)
# Passmanager
The Passmanager 2.0 is a tool to manage password via folders and tag's. There are 2 types of folders: The Private and the Shared Folder. Additionally to the folders there are the tag's. Tag's are used to control the user's access to the passwords in a folder.

The Passmanager is a Web-application and runs with PHP using the CakePHP Framework

All Passwords are encrypted using a Twofish encryption provided by the phpseclib. 

## How to Install
### Requirements
 * A HTTP Server with URL Rewriting (Apache 2 with mod_rewrite is fine)
 * PHP version 5.3.0 at least but not above PHP 7
 * sqlite3 module for PHP
 * sqlite3 for your operating system (to create the database) *(tipp: under Debian/Ubuntu you can use `apt-get install sqlite3` )*.
         
### Setup
1. Make sure your Webserver installation can run CakePHP ([cakephp install instructions](http://book.cakephp.org/2.0/en/installation.html))
2. Clone the sources from github `git clone https://github.com/mixvoip/passmanager.git`
3. Go to the `app/webroot/db` Directory and execute the `./createDatabase.sh` Script to create the database
4. To create the *admin* user with the default password *q1w2e3!*  and the root tag run in the `app/webroot/db` the command `sqlite3 Passmanager.db < firstUser.sql`. We recommend to change the password of the *admin* user as soon as possible

 *We highly encourage you to use connections encrypted with SSL  such as HTTPS to further enhance the security. We also recommend to strictly regulate the access to the `app/webroot/db` folder so that only the web server has access to it.*

## Contacts
* To contact MIXvoip S.a. please visit our [Homepage](https://www.mixvoip.com/contact/)
* The Devloper can be reached under [claures@mixvoip.com](mailto:claures@mixvoip.com)
* [Facebook](https://www.facebook.com/mixvoip)
* [Twitter](https://twitter.com/mixvoip)

##Technical Stuff
###Configuration
The main configuration file for the App is the `app/Config/AppSettings.php` File

###The Cryptology
For the Cryptology the Passmanager uses the Twofish algorithm from the phpseclib. All cryptographic functions are implemented in the `app/Vendor/CryptoWrapper.php` for simple replacement.

## License
The Project is under the [MIT License](https://opensource.org/licenses/mit-license.php).
