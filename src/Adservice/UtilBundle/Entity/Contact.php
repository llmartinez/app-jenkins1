<?php

namespace Adservice\UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\UtilBundle\Entity\Contact
 */

// @Embeddable
class Contact
{
//     /**
//      * @var string $country
//      *
//      * @ORM\ManyToOne(targetEntity="Adservice\UtilBundle\Entity\Country")
//      */
//     private $country;

//     /**
//      * @var string $region
//      *
//      * @ORM\Column(name="region", type="string")
//      */
//     private $region;

//     /**
//      * @var string $city
//      *
//      * @ORM\Column(name="city", type="string")
//      */
//     private $city;

//     /**
//      * @var string $address
//      *
//      * @ORM\Column(name="address", type="string", length=255, nullable=true)
//      */
//     private $address;

//     /**
//      * @var string $postal_code
//      *
//      * @ORM\Column(name="postal_code", type="string",nullable=true)
//      */
//     private $postal_code;

//     /**
//      * @var integer $phone_number_1
//      *
//      * @ORM\Column(name="phone_number_1", type="integer")
//      */
//     private $phone_number_1;

//     /**
//      * @var integer $phone_number_2
//      *
//      * @ORM\Column(name="phone_number_2", type="integer", nullable=true)
//      */
//     private $phone_number_2;

//     /**
//      * @var integer $mobile_number_1
//      *
//      * @ORM\Column(name="mobile_number_1", type="integer", nullable=true)
//      */
//     private $mobile_number_1;

//     /**
//      * @var integer $mobile_number_2
//      *
//      * @ORM\Column(name="mobile_number_2", type="integer", nullable=true)
//      */
//     private $mobile_number_2;

//     /**
//      * @var integer $fax
//      *
//      * @ORM\Column(name="fax", type="integer", nullable=true)
//      */
//     private $fax;

//     /**
//      * @var string $email_1
//      *
//      * @ORM\Column(name="email_1", type="string", length=255, nullable=true)
//      */
//     private $email_1;

//     /**
//      * @var string $email_2
//      *
//      * @ORM\Column(name="email_2", type="string", length=255, nullable=true)
//      */
//     private $email_2;

// //  ___________________________________________________________________
// // |___________________________________________________________________|

//     /**
//      * Set country
//      *
//      * @param string $country
//      */
//     public function setCountry(\Adservice\UtilBundle\Entity\Country $country) {
//         $this->country = $country;
//     }

//     /**
//      * Get country
//      *
//      * @return string
//      */
//     public function getCountry() {
//         return $this->country;
//     }

//     /**
//      * Set region
//      *
//      * @param string $region
//      */
//     public function setRegion($region) {
//         $this->region = $region;
//     }

//     /**
//      * Get region
//      *
//      * @return string
//      */
//     public function getRegion() {
//         return $this->region;
//     }

//     /**
//      * Set city
//      *
//      * @param string $city
//      */
//     public function setCity($city) {
//         $this->city = $city;
//     }

//     /**
//      * Get city
//      *
//      * @return string
//      */
//     public function getCity() {
//         return $this->city;
//     }

//     /**
//      * Set address
//      *
//      * @param string $address
//      */
//     public function setAddress($address) {
//         $this->address = $address;
//     }

//     /**
//      * Get address
//      *
//      * @return string
//      */
//     public function getAddress() {
//         return $this->address;
//     }

//     /**
//      * Set postal_code
//      *
//      * @param string $postal_code
//      */
//     public function setPostalCode($postal_code) {
//         $this->postal_code = $postal_code;
//     }

//     /**
//      * Get postal_code
//      *
//      * @return string
//      */
//     public function getPostalCode() {
//         return $this->postal_code;
//     }

//     /**
//      * Set phone_number_1
//      *
//      * @param integer $phoneNumber1
//      */
//     public function setPhoneNumber1($phoneNumber1) {
//         $this->phone_number_1 = $phoneNumber1;
//     }

//     /**
//      * Get phone_number_1
//      *
//      * @return integer
//      */
//     public function getPhoneNumber1() {
//         return $this->phone_number_1;
//     }

//     /**
//      * Set phone_number_2
//      *
//      * @param integer $phoneNumber2
//      */
//     public function setPhoneNumber2($phoneNumber2) {
//         $this->phone_number_2 = $phoneNumber2;
//     }

//     /**
//      * Get phone_number_2
//      *
//      * @return integer
//      */
//     public function getPhoneNumber2() {
//         return $this->phone_number_2;
//     }

//     /**
//      * Set mobile_number_1
//      *
//      * @param integer $mobileNumber1
//      */
//     public function setMobileNumber1($mobileNumber1) {
//         $this->mobile_number_1 = $mobileNumber1;
//     }

//     /**
//      * Get mobile_number_1
//      *
//      * @return integer
//      */
//     public function getMobileNumber1() {
//         return $this->mobile_number_1;
//     }

//     /**
//      * Set mobile_number_2
//      *
//      * @param integer $mobileNumber2
//      */
//     public function setMobileNumber2($mobileNumber2) {
//         $this->mobile_number_2 = $mobileNumber2;
//     }

//     /**
//      * Get mobile_number_2
//      *
//      * @return integer
//      */
//     public function getMobileNumber2() {
//         return $this->mobile_number_2;
//     }

//     /**
//      * Set fax
//      *
//      * @param integer $fax
//      */
//     public function setFax($fax) {
//         $this->fax = $fax;
//     }

//     /**
//      * Get fax
//      *
//      * @return integer
//      */
//     public function getFax() {
//         return $this->fax;
//     }

//     /**
//      * Set email_1
//      *
//      * @param string $email1
//      */
//     public function setEmail1($email1) {
//         $this->email_1 = $email1;
//     }

//     /**
//      * Get email_1
//      *
//      * @return string
//      */
//     public function getEmail1() {
//         return $this->email_1;
//     }

//     /**
//      * Set email_2
//      *
//      * @param string $email2
//      */
//     public function setEmail2($email2) {
//         $this->email_2 = $email2;
//     }

//     /**
//      * Get email_2
//      *
//      * @return string
//      */
//     public function getEmail2() {
//         return $this->email_2;
//     }
}