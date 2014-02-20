<?php

namespace Adservice\CarBundle\Tests\Entity;

use Symfony\Component\Validator\ValidatorFactory;
use Adservice\CarBundle\Entity\Brand;

class DefaultControllerTest extends \PHPUnit_Framework_TestCase
{        
//    private $validator;
        
//    protected function setUp() {
//        parent::setUp();
//        $this->validator = ValidatorFactory::buildDefault()->getValidator();
//    }
    
    public function testIsTrue(){
        $this->assertTrue(true);
        $this->assertFalse(false);
        
    }
    
//    private function validar(Brand $brand){
//        $errores = $this->validator->validate($brand);
//        $error = $errores[0];
//        return array($errores, $error);
//    }
    
//    public function testValidarName() {
//        $brand = new Brand();
//        $brand->setName('Brand1');
//        $name = $brand->getName();
//        $this->assertEquals('Brand1', $name, 'Se asigna name a $brand');
//    }
}
