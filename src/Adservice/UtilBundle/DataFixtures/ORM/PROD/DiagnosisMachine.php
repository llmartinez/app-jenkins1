<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\WorkshopBundle\Entity\DiagnosisMachine;

class DiagnosisMachines extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 15; }

    public function load(ObjectManager $manager) {
        $machines = array(
            array( 'name' => '...'     , 'country' => 'spain'   , 'ref' => 'none_es'),
            array( 'name' => '...'     , 'country' => 'andorra' , 'ref' => 'none_an'),
            array( 'name' => '...'     , 'country' => 'france'  , 'ref' => 'none_fr'),
            array( 'name' => '...'     , 'country' => 'portugal', 'ref' => 'none_pt'),

            array( 'name' => 'Autel'   , 'country' => 'spain'   , 'ref' => 'Autel_es'),
            array( 'name' => 'Autel'   , 'country' => 'andorra' , 'ref' => 'Autel_an'),
            array( 'name' => 'Autel'   , 'country' => 'france'  , 'ref' => 'Autel_fr'),
            array( 'name' => 'Autel'   , 'country' => 'portugal', 'ref' => 'Autel_pt'),

            array( 'name' => 'Autocom' , 'country' => 'spain'   , 'ref' => 'Autocom_es'),
            array( 'name' => 'Autocom' , 'country' => 'andorra' , 'ref' => 'Autocom_an'),
            array( 'name' => 'Autocom' , 'country' => 'france'  , 'ref' => 'Autocom_fr'),
            array( 'name' => 'Autocom' , 'country' => 'portugal', 'ref' => 'Autocom_pt'),

            array( 'name' => 'Autosnap', 'country' => 'spain'   , 'ref' => 'Autosnap_es'),
            array( 'name' => 'Autosnap', 'country' => 'andorra' , 'ref' => 'Autosnap_an'),
            array( 'name' => 'Autosnap', 'country' => 'france'  , 'ref' => 'Autosnap_fr'),
            array( 'name' => 'Autosnap', 'country' => 'portugal', 'ref' => 'Autosnap_pt'),

            array( 'name' => 'Berton'  , 'country' => 'spain'   , 'ref' => 'Berton_es'),
            array( 'name' => 'Berton'  , 'country' => 'andorra' , 'ref' => 'Berton_an'),
            array( 'name' => 'Berton'  , 'country' => 'france'  , 'ref' => 'Berton_fr'),
            array( 'name' => 'Berton'  , 'country' => 'portugal', 'ref' => 'Berton_pt'),

            array( 'name' => 'Bosch'   , 'country' => 'spain'   , 'ref' => 'Bosch_es'),
            array( 'name' => 'Bosch'   , 'country' => 'andorra' , 'ref' => 'Bosch_an'),
            array( 'name' => 'Bosch'   , 'country' => 'france'  , 'ref' => 'Bosch_fr'),
            array( 'name' => 'Bosch'   , 'country' => 'portugal', 'ref' => 'Bosch_pt'),

            array( 'name' => 'Delphi'  , 'country' => 'spain'   , 'ref' => 'Delphi_es'),
            array( 'name' => 'Delphi'  , 'country' => 'andorra' , 'ref' => 'Delphi_an'),
            array( 'name' => 'Delphi'  , 'country' => 'france'  , 'ref' => 'Delphi_fr'),
            array( 'name' => 'Delphi'  , 'country' => 'portugal', 'ref' => 'Delphi_pt'),

            array( 'name' => 'OTC'     , 'country' => 'spain'   , 'ref' => 'OTC_es'),
            array( 'name' => 'OTC'     , 'country' => 'andorra' , 'ref' => 'OTC_an'),
            array( 'name' => 'OTC'     , 'country' => 'france'  , 'ref' => 'OTC_fr'),
            array( 'name' => 'OTC'     , 'country' => 'portugal', 'ref' => 'OTC_pt'),

            array( 'name' => 'Reflex'  , 'country' => 'spain'   , 'ref' => 'Reflex_es'),
            array( 'name' => 'Reflex'  , 'country' => 'andorra' , 'ref' => 'Reflex_an'),
            array( 'name' => 'Reflex'  , 'country' => 'france'  , 'ref' => 'Reflex_fr'),
            array( 'name' => 'Reflex'  , 'country' => 'portugal', 'ref' => 'Reflex_pt'),

            array( 'name' => 'SPX'     , 'country' => 'spain'   , 'ref' => 'SPX_es'),
            array( 'name' => 'SPX'     , 'country' => 'andorra' , 'ref' => 'SPX_an'),
            array( 'name' => 'SPX'     , 'country' => 'france'  , 'ref' => 'SPX_fr'),
            array( 'name' => 'SPX'     , 'country' => 'portugal', 'ref' => 'SPX_pt'),

            array( 'name' => 'Texa'    , 'country' => 'spain'   , 'ref' => 'Texa_es'),
            array( 'name' => 'Texa'    , 'country' => 'andorra' , 'ref' => 'Texa_an'),
            array( 'name' => 'Texa'    , 'country' => 'france'  , 'ref' => 'Texa_fr'),
            array( 'name' => 'Texa'    , 'country' => 'portugal', 'ref' => 'Texa_pt'),
        );
        foreach ($machines as $machine) {
            $entidad = new DiagnosisMachine();
            $entidad->setName($machine['name']);
            $entidad->setCountry($this->getReference($machine['country']));
            $entidad->setActive(1);
            $manager->persist($entidad);

            $ref = $machine['ref'];
            if ($ref == '') $ref = $entidad->getName();

            $this->addReference($ref, $entidad);
        }
        $manager->flush();
    }
}

?>
