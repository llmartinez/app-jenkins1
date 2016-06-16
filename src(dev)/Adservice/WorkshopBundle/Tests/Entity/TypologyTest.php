<?php
namespace Adservice\WorkshopBundle\Tests\Entity;

use Adservice\WorkshopBundle\Entity\Typology;

class TypologyTest extends \PHPUnit_Framework_TestCase
{
   protected $typology;

   public function testSettersTypology()
   {
       $typology = new Typology();
       $typology->setName('TypologyTest');

       $this->typology = $typology;
   }

   public function testGettersTypology()
   {
       $typology = $this->typology;
       return $typology;
   }

   public static function GetTypology()
   {
       $typology = new Typology();
       $typology->setName('TypologyTest');

       return $typology;
   }
}