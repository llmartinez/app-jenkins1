<?php
namespace Adservice\UtilBundle\DataFixtures\ORM\PROD;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Adservice\UtilBundle\Resources\excel\Spreadsheet_Excel_Reader;

use Adservice\CarBundle\Entity\Brand;
use Adservice\CarBundle\Entity\Model;
use Adservice\CarBundle\Entity\Version;

class LoadTecDoc extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $manager;
    private $container;

    public function getOrder(){ return 3; }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
## BRAND ##
        print "\tCreating brands...\n";

        //LEE EL .xls Y DEVUELVE EL OBJETO EXCEL_READER
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');

        $filePath = $this->container->get('kernel')->getRootDir().'/../data/xls/Marca_Vehiculo.xls';

        $data->read($filePath);

        //PARSEA EL EXCEL
        for($i=2;$i<=count($data->sheets[0]['cells']);$i++){

            $row = $data->sheets[0]['cells'][$i];

            $idTecDoc  = utf8_encode($row[1]);
            $brandName = utf8_encode($row[2]);

            $brand = new Brand();
            $brand->setIdTecDoc($idTecDoc);
            $brand->setName($brandName);

            $manager->persist($brand);
            $this->addReference('brand'.$brand->getIdTecDoc(), $brand);
        }

## MODEL ##
        print "\tCreating models...\n";

        //LEE EL .xls Y DEVUELVE EL OBJETO EXCEL_READER
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');

        $filePath = $this->container->get('kernel')->getRootDir().'/../data/xls/Modelo_Vehiculo.xls';

        $data->read($filePath);

        //PARSEA EL EXCEL
        for($i=2;$i<=count($data->sheets[0]['cells']);$i++){

            $row = $data->sheets[0]['cells'][$i];

            $idTecDoc  = utf8_encode($row[3]);
            $brandId   = utf8_encode($row[1]);
            $modelName = utf8_encode($row[2]);

            $model = new Model();
            $model->setIdTecDoc($idTecDoc);
            $model->setBrand($this->getReference('brand'.$brandId));
            $model->setName($modelName);

            $manager->persist($model);
            $this->addReference('model'.$model->getIdTecDoc(), $model);
        }


## VERSION ##
        print "\tCreating versions...\n";

        //LEE EL .xls Y DEVUELVE EL OBJETO EXCEL_READER
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');

        $filePath = $this->container->get('kernel')->getRootDir().'/../data/xls/Version_Vehiculo.xls';

        $data->read($filePath);

        //PARSEA EL EXCEL
        for($i=2;$i<=count($data->sheets[0]['cells']);$i++){

            $row = $data->sheets[0]['cells'][$i];

            $idTecDoc    = utf8_encode($row[4]);
            $modelId     = utf8_encode($row[2]);
            $versionName = utf8_encode($row[3]);

            $version = new Version();
            $version->setIdTecDoc($idTecDoc);
            $version->setModel($this->getReference('model'.$modelId));
            $version->setName($versionName);

            $manager->persist($version);
            $this->addReference('version'.$version->getIdTecDoc(), $version);
        }

        $manager->flush();
    }
}

?>