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

            array( 'name' => 'Actia (ES)'   , 'country' => 'spain'   , 'ref' => 'Actia_es'),
            array( 'name' => 'Actia (AN)'   , 'country' => 'andorra' , 'ref' => 'Actia_an'),
            array( 'name' => 'Actia (FR)'   , 'country' => 'france'  , 'ref' => 'Actia_fr'),
            array( 'name' => 'Actia (PT)'   , 'country' => 'portugal', 'ref' => 'Actia_pt'),

            array( 'name' => 'Autel (ES)'   , 'country' => 'spain'   , 'ref' => 'Autel_es'),
            array( 'name' => 'Autel (AN)'   , 'country' => 'andorra' , 'ref' => 'Autel_an'),
            array( 'name' => 'Autel (FR)'   , 'country' => 'france'  , 'ref' => 'Autel_fr'),
            array( 'name' => 'Autel (PT)'   , 'country' => 'portugal', 'ref' => 'Autel_pt'),

            array( 'name' => 'Autocom (ES)' , 'country' => 'spain'   , 'ref' => 'Autocom_es'),
            array( 'name' => 'Autocom (AN)' , 'country' => 'andorra' , 'ref' => 'Autocom_an'),
            array( 'name' => 'Autocom (FR)' , 'country' => 'france'  , 'ref' => 'Autocom_fr'),
            array( 'name' => 'Autocom (PT)' , 'country' => 'portugal', 'ref' => 'Autocom_pt'),

            array( 'name' => 'Autosnap (ES)', 'country' => 'spain'   , 'ref' => 'Autosnap_es'),
            array( 'name' => 'Autosnap (AN)', 'country' => 'andorra' , 'ref' => 'Autosnap_an'),
            array( 'name' => 'Autosnap (FR)', 'country' => 'france'  , 'ref' => 'Autosnap_fr'),
            array( 'name' => 'Autosnap (PT)', 'country' => 'portugal', 'ref' => 'Autosnap_pt'),

            array( 'name' => 'Berton (ES)'  , 'country' => 'spain'   , 'ref' => 'Berton_es'),
            array( 'name' => 'Berton (AN)'  , 'country' => 'andorra' , 'ref' => 'Berton_an'),
            array( 'name' => 'Berton (FR)'  , 'country' => 'france'  , 'ref' => 'Berton_fr'),
            array( 'name' => 'Berton (PT)'  , 'country' => 'portugal', 'ref' => 'Berton_pt'),

            array( 'name' => 'Bosch (ES)'   , 'country' => 'spain'   , 'ref' => 'Bosch_es'),
            array( 'name' => 'Bosch (AN)'   , 'country' => 'andorra' , 'ref' => 'Bosch_an'),
            array( 'name' => 'Bosch (FR)'   , 'country' => 'france'  , 'ref' => 'Bosch_fr'),
            array( 'name' => 'Bosch (PT)'   , 'country' => 'portugal', 'ref' => 'Bosch_pt'),

            array( 'name' => 'Delphi (ES)'  , 'country' => 'spain'   , 'ref' => 'Delphi_es'),
            array( 'name' => 'Delphi (AN)'  , 'country' => 'andorra' , 'ref' => 'Delphi_an'),
            array( 'name' => 'Delphi (FR)'  , 'country' => 'france'  , 'ref' => 'Delphi_fr'),
            array( 'name' => 'Delphi (PT)'  , 'country' => 'portugal', 'ref' => 'Delphi_pt'),

            array( 'name' => 'OTC (ES)'     , 'country' => 'spain'   , 'ref' => 'OTC_es'),
            array( 'name' => 'OTC (AN)'     , 'country' => 'andorra' , 'ref' => 'OTC_an'),
            array( 'name' => 'OTC (FR)'     , 'country' => 'france'  , 'ref' => 'OTC_fr'),
            array( 'name' => 'OTC (PT)'     , 'country' => 'portugal', 'ref' => 'OTC_pt'),

            array( 'name' => 'Reflex (ES)'  , 'country' => 'spain'   , 'ref' => 'Reflex_es'),
            array( 'name' => 'Reflex (AN)'  , 'country' => 'andorra' , 'ref' => 'Reflex_an'),
            array( 'name' => 'Reflex (FR)'  , 'country' => 'france'  , 'ref' => 'Reflex_fr'),
            array( 'name' => 'Reflex (PT)'  , 'country' => 'portugal', 'ref' => 'Reflex_pt'),

            array( 'name' => 'SPX (ES)'     , 'country' => 'spain'   , 'ref' => 'SPX_es'),
            array( 'name' => 'SPX (AN)'     , 'country' => 'andorra' , 'ref' => 'SPX_an'),
            array( 'name' => 'SPX (FR)'     , 'country' => 'france'  , 'ref' => 'SPX_fr'),
            array( 'name' => 'SPX (PT)'     , 'country' => 'portugal', 'ref' => 'SPX_pt'),

            array( 'name' => 'Texa (ES)'    , 'country' => 'spain'   , 'ref' => 'Texa_es'),
            array( 'name' => 'Texa (AN)'    , 'country' => 'andorra' , 'ref' => 'Texa_an'),
            array( 'name' => 'Texa (FR)'    , 'country' => 'france'  , 'ref' => 'Texa_fr'),
            array( 'name' => 'Texa (PT)'    , 'country' => 'portugal', 'ref' => 'Texa_pt'),
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
