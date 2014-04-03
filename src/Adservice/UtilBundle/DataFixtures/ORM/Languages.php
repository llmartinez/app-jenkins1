<?php
namespace Adservice\UtilBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\Entity\Language;

class Languages extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 1; }

    public function load(ObjectManager $manager) {
        $languages = array(
            array('language' => 'spanish' , 'short_name' => 'es_ES'  ),
            array('language' => 'english' , 'short_name' => 'en_EN'  ),
            array('language' => 'french'  , 'short_name' => 'fr_FR'  ),
        );
        foreach ($languages as $language) {
            $entidad = new Language();
            $entidad->setLanguage($language['language']);
            $entidad->setShortName($language['short_name']);
            $manager->persist($entidad);

            $this->addReference($entidad->getShortName(), $entidad);
        }
        $manager->flush();
    }
}

?>