<?php

namespace AppBundle\Tests\Utils;

use AppBundle\Utils\Slugger as Slugger;

class SluggerTest extends \PHPUnit_Framework_TestCase
{    
    public function testSlugify()
    {
        $string = 'Hola Mundo';
        $string = Slugger::slugify($string);

        $this->assertEquals('hola-mundo', $string);
    }

    public function testnoSpaces()
    {
        $string = '    
                    Hello  World!  ';
        $string = Slugger::noSpaces($string);
        
        $this->assertEquals('HelloWorld!', $string);
    }
}

?>