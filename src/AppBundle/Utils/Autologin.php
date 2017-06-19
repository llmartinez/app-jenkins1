<?php
namespace AppBundle\Utils;

class Autologin
{
    private $secret_iv;

    public function __construct($secret_iv) {
        $this->secret_iv = $secret_iv;
    }

    /**
     * Method to encrypt a plain text string
     * PHP 5.4.9
     *
     * https://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/
     *
     * @param string $string: string to encrypt or decrypt (the password)
     * @param string $secret_iv: initialization vector (has to be the same when encrypting and decrypting)
     * @return string
     */
    public function encrypt($string)
    {
        $output = false;
        //encryptiom method name
        $encrypt_method = "AES-256-CBC";
        //the current day "YYYYmmdd"
        $secret_key = date_format(date_create('now'), 'Ymd');

        // hash
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $this->secret_iv), 0, 16);

        //encrypt with openssl
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);

        //encode with base64
        $output = base64_encode($output);

        return $output;
    }


    /**
     * Method to decrypt an encrypted string
     * PHP 5.4.9
     *
     * https://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/
     *
     * @param string $string: string to decrypt (the password)
     * @param string $secret_iv: initialization vector (has to be the same when encrypting and decrypting)
     * @return string
     */
    public function decrypt($string)
    {
        //$output = false;
        $output = base64_decode($string);
        $encrypt_method = "AES-256-CBC";
        $hash = null;

        // valid keys...
        $keys[0] = hash('sha256', date_format(date_create('now'), 'Ymd'));          //today
        $keys[1] = hash('sha256', date_format(date_create('now -1day'), 'Ymd'));    //yesterday
        $keys[2] = hash('sha256', date_format(date_create('now +1day'), 'Ymd'));    //tomorrow

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $this->secret_iv), 0, 16);

        // get an array of possible valid hashes
        foreach ($keys as $key) {
            $valid_hashes[] = openssl_decrypt($output, $encrypt_method, $key, 0, $iv);
        }

        // check if there is a valid hash in the array
        foreach ($valid_hashes as $valid_hash) {
            if ($valid_hash != "") $hash = $valid_hash;
        }

        // $hash must contain only english letters & digits
        if (!preg_match('/[^A-Za-z0-9]/', $hash))
             return $hash;

        else return null;
    }
}


