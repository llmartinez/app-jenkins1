<?php

namespace Adservice\UtilBundle\Services;

class Security
{
    protected $secret_iv;

    function __construct($secret_iv)
    {
        $this->secret_iv = $secret_iv;
    }
    /**
     * Method to encrypt a plain text string
     * initialization vector(IV) has to be the same when encrypting and decrypting
     * PHP 5.4.9
     *
     * https://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/
     *
     * @param string $string: string to encrypt or decrypt (the password)
     * @return string
     */
    public function encryptADS($string){

        $output = false;

        //encryptiom method name
        $encrypt_method = "AES-256-CBC";

        //the current day "YYYYmmdd"
        //$secret_key = date_format(date_create('now'), 'Ymd');          //today
        $keys[0] = hash('sha256', date_format(date_create('now'), 'Ymd'));          //today
        $keys[1] = hash('sha256', date_format(date_create('now -1day'), 'Ymd'));    //yesterday
        $keys[2] = hash('sha256', date_format(date_create('now +1day'), 'Ymd'));    //tomorrow

        // hash
        //$key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $this->secret_iv), 0, 16);

        foreach ($keys as $key) {
            //encrypt with openssl
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);

            //encode with base64
            $encoded[] = base64_encode($output);
        }

        return $encoded;
    }

    public function decryptADS($string){

        $output = false;

        $encrypt_method = "AES-256-CBC";

        // valid keys...
        $keys[0] = hash('sha256', date_format(date_create('now'), 'Ymd'));          //today
        $keys[1] = hash('sha256', date_format(date_create('now -1day'), 'Ymd'));    //yesterday
        $keys[2] = hash('sha256', date_format(date_create('now +1day'), 'Ymd'));    //tomorrow

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $this->secret_iv), 0, 16);

        $output = base64_decode($string);
        foreach ($keys as $key) {
            $valid_hashes[] = openssl_decrypt($output, $encrypt_method, $key, 0, $iv);
        }

        return $valid_hashes;
    }
}
