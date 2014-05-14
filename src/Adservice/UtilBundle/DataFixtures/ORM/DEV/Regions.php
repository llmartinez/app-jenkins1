<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UtilBundle\Entity\Region;

class Regions extends AbstractFixture implements OrderedFixtureInterface {

    public function getOrder(){ return 3; }

    public function load(ObjectManager $manager) {

        $regions = array(
            array('region' => "Álava"                   , 'country' => $this->getReference('Spain')),
            array('region' => "Albacete"                , 'country' => $this->getReference('Spain')),
            array('region' => "Alicante"                , 'country' => $this->getReference('Spain')),
            array('region' => "Almería"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Asturias"                , 'country' => $this->getReference('Spain')),
            array('region' => "Ávila"                   , 'country' => $this->getReference('Spain')),
            array('region' => "Badajoz"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Baleares"                , 'country' => $this->getReference('Spain')),
            array('region' => "Barcelona"               , 'country' => $this->getReference('Spain')),
            array('region' => "Burgos"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Cáceres"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Cádiz"                   , 'country' => $this->getReference('Spain')),
            array('region' => "Cantabria"               , 'country' => $this->getReference('Spain')),
            array('region' => "Castellón"               , 'country' => $this->getReference('Spain')),
            array('region' => "Cantabria"               , 'country' => $this->getReference('Spain')),
            array('region' => "Ceuta"                   , 'country' => $this->getReference('Spain')),
            array('region' => "Ciudad Real"             , 'country' => $this->getReference('Spain')),
            array('region' => "Ceuta"                   , 'country' => $this->getReference('Spain')),
            array('region' => "Córdoba"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Coruña (A)"              , 'country' => $this->getReference('Spain')),
            array('region' => "Cuenca"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Girona"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Granada"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Guadalajara"             , 'country' => $this->getReference('Spain')),
            array('region' => "Guipúzcoa"               , 'country' => $this->getReference('Spain')),
            array('region' => "Huelva"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Huesca"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Jaén"                    , 'country' => $this->getReference('Spain')),
            array('region' => "León"                    , 'country' => $this->getReference('Spain')),
            array('region' => "Lugo"                    , 'country' => $this->getReference('Spain')),
            array('region' => "Lleida"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Madrid"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Málaga"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Melilla"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Murcia"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Navarra"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Ourense"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Palencia"                , 'country' => $this->getReference('Spain')),
            array('region' => "Palmas (Las)"            , 'country' => $this->getReference('Spain')),
            array('region' => "Pontevedra"              , 'country' => $this->getReference('Spain')),
            array('region' => "Rioja (La)"              , 'country' => $this->getReference('Spain')),
            array('region' => "Salamanca"               , 'country' => $this->getReference('Spain')),
            array('region' => "Santa Cruz de Tenerife"  , 'country' => $this->getReference('Spain')),
            array('region' => "Segovia"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Sevilla"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Soria"                   , 'country' => $this->getReference('Spain')),
            array('region' => "Tarragona"               , 'country' => $this->getReference('Spain')),
            array('region' => "Teruel"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Toledo"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Valencia"                , 'country' => $this->getReference('Spain')),
            array('region' => "Valladolid"              , 'country' => $this->getReference('Spain')),
            array('region' => "Vizcaya"                 , 'country' => $this->getReference('Spain')),
            array('region' => "Zamora"                  , 'country' => $this->getReference('Spain')),
            array('region' => "Zaragoza"                , 'country' => $this->getReference('Spain')),

        /* Liste des régions de France métropolitaine */
            array('region' => "Alsace"                      , 'country' => $this->getReference('France')),
            array('region' => "Aquitaine"                   , 'country' => $this->getReference('France')),
            array('region' => "Auvergne"                    , 'country' => $this->getReference('France')),
            array('region' => "Basse-Normandie"             , 'country' => $this->getReference('France')),
            array('region' => "Bourgogne"                   , 'country' => $this->getReference('France')),
            array('region' => "Bretagne"                    , 'country' => $this->getReference('France')),
            array('region' => "Centre"                      , 'country' => $this->getReference('France')),
            array('region' => "Champagne-Ardenne"           , 'country' => $this->getReference('France')),
            array('region' => "Corse"                       , 'country' => $this->getReference('France')),
            array('region' => "Franche-Comté"               , 'country' => $this->getReference('France')),
            array('region' => "Haute-Normandie"             , 'country' => $this->getReference('France')),
            array('region' => "Île-de-France"               , 'country' => $this->getReference('France')),
            array('region' => "Languedoc-Roussillon"        , 'country' => $this->getReference('France')),
            array('region' => "Limousin"                    , 'country' => $this->getReference('France')),
            array('region' => "Lorraine"                    , 'country' => $this->getReference('France')),
            array('region' => "Midi-Pyrénées"               , 'country' => $this->getReference('France')),
            array('region' => "Nord-Pas-de-Calais"          , 'country' => $this->getReference('France')),
            array('region' => "Pays de la Loire"            , 'country' => $this->getReference('France')),
            array('region' => "Picardie"                    , 'country' => $this->getReference('France')),
            array('region' => "Poitou-Charentes"            , 'country' => $this->getReference('France')),
            array('region' => "Provence-Alpes-Côte d'Azur"  , 'country' => $this->getReference('France')),
            array('region' => "Rhône-Alpes"                 , 'country' => $this->getReference('France')),

        /* Liste des régions de France d'outre-mer (DROM) */
            array('region' => "Guadeloupe"                  , 'country' => $this->getReference('France')),
            array('region' => "Guyane"                      , 'country' => $this->getReference('France')),
            array('region' => "La Réunion"                  , 'country' => $this->getReference('France')),
            array('region' => "Martinique"                  , 'country' => $this->getReference('France')),
            array('region' => "Mayotte"                     , 'country' => $this->getReference('France')),
        );
        foreach ($regions as $region) {
            $entidad = new Region();
            $entidad->setRegion ($region['region']);
            $entidad->setCountry($region['country']);
            $manager->persist($entidad);

            $this->addReference($entidad->getRegion(), $entidad);
        }
        $manager->flush();
    }
}

?>