<?php
namespace Adservice\WorkshopBundle\Tests\Entity;

use Adservice\WorkshopBundle\Entity\DiagnosisMachine;

class DiagnosisMachineTest extends \PHPUnit_Framework_TestCase
{
   protected $diagnosis_machine;

   public function testSettersDiagnosisMachine()
   {
       $diagnosis_machine = new DiagnosisMachine();
       $diagnosis_machine->setName('DiagnosisMachineTest');

       $this->diagnosis_machine = $diagnosis_machine;
   }

   public function testGettersDiagnosisMachine()
   {
       $diagnosis_machine = $this->diagnosis_machine;
       return $diagnosis_machine;
   }

   public static function GetDiagnosisMachine()
   {
       $diagnosis_machine = new DiagnosisMachine();
       $diagnosis_machine->setName('DiagnosisMachineTest');

       return $diagnosis_machine;
   }
}