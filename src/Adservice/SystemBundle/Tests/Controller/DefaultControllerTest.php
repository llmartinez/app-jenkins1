<?php

namespace Adservice\SystemBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    public function testIsTrue(){
        $this->assertTrue(true);
        $this->assertFalse(false);
    }

}
