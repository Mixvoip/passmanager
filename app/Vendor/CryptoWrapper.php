<?php

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 11.04.16
 * Time: 09:51
 * My personal Crypto Wrapper
 */
class CryptoWrapper
{
    private $cryptEngine = null;
    private $loaded = false;

    public function __construct()
    {

    }

    public function load()
    {
        $this->loaded = true;
    }
    
    public function __destruct()
    {
        //Reset key just to be sure
        if (isset($this->cryptEngine)) {
            $this->setKey("");
        }
        $this->cryptEngine = null;
    }

    /**
     * Set the Key for the encryption / decryption
     * @param $key
     */
    public function setKey($key)
    {
        $this->initCrypto();
        $this->cryptEngine->setKey($key);
    }

    /**
     * Encrypt the $message with the key
     * @param $message String The Plaintext to be encrypted
     * @return String The Encrypted message
     */
    public function encrypt($message)
    {
        if (isset($this->cryptEngine)) {
            return base64_encode($this->cryptEngine->encrypt(base64_encode($message)));
        } else {
            throw new FatalErrorException("The Crypto engine is not initialised. Please Call setKey() to initialise the Crypto Engine");
        }
    }

    /**
     * Decrypt the Message with the key
     * @param $message String The Encrypted message to be decrypted
     * @return String The Plaintext
     */
    public function decrypt($message)
    {
        if (isset($this->cryptEngine)) {
            return base64_decode($this->cryptEngine->decrypt(base64_decode($message)));
        } else {
            throw new FatalErrorException("The Crypto engine is not initialised. Please Call setKey() to initialise the Crypto Engine");
        }
    }

    /**
     * Encrypt the $message with the key for the recovery
     * @param $message String The Plaintext to be encrypted
     * @return String The Encrypted message
     */
    public function encryptRecovery($message)
    {
        return $this->encrypt($message);
    }

    /**
     * Decrypt the Message with the key for the recovery
     * @param $message String The Encrypted message to be decrypted
     * @return String The Plaintext
     */
    public function decryptRecovery($message)
    {
        return $this->decrypt($message);
    }

    /**
     * Salts and hashes the password using hash_hmac with sha512
     * @param $password String Password to be hashed
     * @param $salt String Salt to use.
     * @return string salted and hashed password
     */
    public function hashPassword($password, $salt)
    {
        $saltyPassword = hash_hmac('sha512', $password, $salt);
        return $saltyPassword;
    }

    /**
     * Generate a random salt based on the multiplication of microtime as int and a random integer from mt_rand().
     * After that it hashes the random value with sha512
     * @return string sha521 random salt string
     */
    public function genSalt()
    {
        $rngInt = mt_rand();
        $saltInitValue = (int)microtime(true) * $rngInt;
        return hash('sha512', $saltInitValue);
    }

    /**
     * Generate a random Passwords with a length
     * @param $length int Length of the Password. Default 32
     * @return String The generated Password
     */
    public function genPassword($length = 32)
    {
        $this->loadphpSecLib();
        $rng = \phpseclib\Crypt\Random::string($length);
        return base64_encode($rng);
    }

    /**
     * Initialise the Crypto. Call before setKey
     */
    private function initCrypto()
    {
        $this->loadphpSecLib();
        //Define here the Crypto Engine
        $this->cryptEngine = new phpseclib\Crypt\Twofish();
        //Additional Options:
    }

    /**
     * Load the Phpsec Lib
     */
    private function loadphpSecLib()
    {
        if (!$this->loaded) {
            $this->loaded = true;
            //Set the Library path for phpseclib
            set_include_path(get_include_path() . PATH_SEPARATOR . APP . 'Vendor/phpseclib');

            //Require all the phpseclib classes
            require_once('Crypt/Base.php');
            require_once('Crypt/Twofish.php');
            require_once('Crypt/Random.php');
        }

    }

}