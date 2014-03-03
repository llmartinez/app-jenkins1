<?php

namespace Adservice\CarBundle\Tests\Entity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    public function testIsTrue(){
        $this->assertTrue(true);
        $this->assertFalse(false);
    }

}
