<?php
namespace Adservice\UtilBundle\Tests\Entity;

use Adservice\UtilBundle\Entity\Country;

class CountryTest extends \PHPUnit_Framework_TestCase
{   
    protected $country;
    
    public function testSettersCountry()
    {
        $country = new Country();
        $country->setCountry('CountryTest'); 
        
        $this->country = $country;
    }
    
    public function testGettersCountry()
    {
        $country = $this->country;
        return $country;
    }
    
    public static function GetCountry()
    {
        $country = new Country();
        $country->setCountry('CountryTest'); 
        
        return $country;
    }
}