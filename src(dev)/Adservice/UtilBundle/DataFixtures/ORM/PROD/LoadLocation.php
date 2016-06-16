<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Adservice\UtilBundle\Resources\excel\Spreadsheet_Excel_Reader;

use Adservice\UtilBundle\Entity\Country;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\City;

class LoadLocation extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $manager;
    private $container;

    public function getOrder(){ return 2; }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        print "\tCreating locations...\n";

## SPAIN ##
        //LEE EL .xls Y DEVUELVE EL OBJETO EXCEL_READER
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');

        $filePath = $this->container->get('kernel')->getRootDir().'/../data/xls/spain.xls';

        $data->read($filePath);

        $country = new Country();
        $country->setCountry('spain');
        $country->setLang('spanish');
        $country->setShortName('ES');

        //PARSEA EL EXCEL
        for($i=1;$i<=count($data->sheets[0]['cells']);$i++){

            $row = $data->sheets[0]['cells'][$i];

            $provinceCode = $row[1];
            $province = utf8_encode($row[2]);

            if($i == 1)
            {
                $region = new Region();
                $region->setRegion($province);
                $region->setCountry($country);

            } elseif($province !== $region->getRegion()){

                $manager->persist($region);
                $region = new Region();
                $region->setRegion($province);
                $region->setCountry($country);

                $this->addReference($region->getRegion(), $region);
            }

            $locationCode = $row[3];
            //Contendrá un array con las poblaciones si hay más de una, sino un array de un elemento
            $locations = explode('/', utf8_encode($row[4]));

            foreach($locations as $location){

                $city = new City();
                $city->setCity($location);
                $city->setRegion($region);
                $manager->persist($city);
            }
        }

        //Persisto la última provincia
        $manager->persist($region);
        $manager->persist($country);
        $this->addReference($country->getCountry(), $country);

## ANDORRA ##
        //LEE EL .xls Y DEVUELVE EL OBJETO EXCEL_READER
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');

        $filePath = $this->container->get('kernel')->getRootDir().'/../data/xls/andorra.xls';

        $data->read($filePath);

        $country = new Country();
        $country->setCountry('andorra');
        $country->setLang('spanish');
        $country->setShortName('AN');

        //PARSEA EL EXCEL
        for($i=1;$i<=count($data->sheets[0]['cells']);$i++){

            $row = $data->sheets[0]['cells'][$i];

            $provinceCode = $row[1];
            $province = utf8_encode($row[2]);

            if($i == 1)
            {
                $region = new Region();
                $region->setRegion($province);
                $region->setCountry($country);

            } elseif($province !== $region->getRegion()){

                $manager->persist($region);

                $region = new Region();
                $region->setRegion($province);
                $region->setCountry($country);

                $this->addReference($region->getRegion(), $region);
            }

            $locationCode = $row[3];
            //Contendrá un array con las poblaciones si hay más de una, sino un array de un elemento
            $locations = explode('/', utf8_encode($row[4]));

            foreach($locations as $location){

                $city = new City();
                $city->setCity($location);
                $city->setRegion($region);
                $manager->persist($city);
            }
        }
        //Persisto la última provincia
        $manager->persist($region);
        $manager->persist($country);

        $this->addReference($country->getCountry(), $country);

## FRANCE ##
        //LEE EL .xls Y DEVUELVE EL OBJETO EXCEL_READER
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');

        $filePath = $this->container->get('kernel')->getRootDir().'/../data/xls/france.xls';

        $data->read($filePath);

        $country = new Country();
        $country->setCountry('france');
        $country->setLang('french');
        $country->setShortName('FR');

        $rows = $data->sheets[0]['cells'];

        //PARSEA EL EXCEL
        for($i=1;$i<=count($rows);$i++){

            $row = $rows[$i];

            $province = utf8_encode($row[2]);

            if($i == 1)
            {
                $region = new Region();
                $region->setRegion($province);
                $region->setCountry($country);

            } elseif($province !== $region->getRegion()){

                $manager->persist($region);

                $region = new Region();
                $region->setRegion($province);
                $region->setCountry($country);

                $this->addReference($region->getRegion(), $region);
            }

            //Contendrá un array con las poblaciones si hay más de una, sino un array de un elemento
            $location = utf8_encode($row[5]);

                $city = new City();
                $city->setCity($location);
                $city->setRegion($region);
                $manager->persist($city);
        }

        //Persisto la última provincia
        $manager->persist($region);
        $manager->persist($country);

        $this->addReference($country->getCountry(), $country);

## PORTUGAL ##
        //LEE EL .xls Y DEVUELVE EL OBJETO EXCEL_READER
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');

        $filePath = $this->container->get('kernel')->getRootDir().'/../data/xls/portugal.xls';

        $data->read($filePath);

        $country = new Country();
        $country->setCountry('portugal');
        $country->setLang('portuguese');
        $country->setShortName('PT');

        //PARSEA EL EXCEL
        for($i=1;$i<=count($data->sheets[0]['cells']);$i++){

            $row = $data->sheets[0]['cells'][$i];

            $province = utf8_encode($row[2]);

            if($i == 1)
            {
                $region = new Region();
                $region->setRegion($province);
                $region->setCountry($country);

            } elseif($province !== $region->getRegion()){

                $manager->persist($region);

                $region = new Region();
                $region->setRegion($province);
                $region->setCountry($country);

                $this->addReference($region->getRegion(), $region);
            }

            //Contendrá un array con las poblaciones si hay más de una, sino un array de un elemento
            $location = utf8_encode($row[3]);

                $city = new City();
                $city->setCity($location);
                $city->setRegion($region);
                $manager->persist($city);
        }
        //Persisto la última provincia
        $manager->persist($region);
        $manager->persist($country);

        $this->addReference($country->getCountry(), $country);

## UK ##
        //LEE EL .xls Y DEVUELVE EL OBJETO EXCEL_READER
        // $data = new Spreadsheet_Excel_Reader();
        // $data->setOutputEncoding('CP1251');

        // $filePath = $this->container->get('kernel')->getRootDir().'/../data/xls/spain.xls';

        // $data->read($filePath);

        $country = new Country();
        $country->setCountry('uk');
        $country->setLang('english');
        $country->setShortName('UK');

        //Persisto la última provincia
        $manager->persist($country);

        $this->addReference($country->getCountry(), $country);


        $manager->flush();
    }
}

?>