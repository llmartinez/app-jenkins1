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

            if(utf8_encode($row[4]) != 0) $start = substr(utf8_encode($row[4]), 2, 2); else $start = '';
            if(utf8_encode($row[5]) != 0) $end   = substr(utf8_encode($row[5]), 2, 2); else $end   = '';

            if($start != '')              $modelName = utf8_encode($row[2]).' ('.$start.'-'.$end.')';
            else                          $modelName = utf8_encode($row[2]);

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

            if(isset($row[5])) $motor   = utf8_encode($row[5]); else $motor   = '';
            $idTecDoc     = utf8_encode($row[4]);
            $modelId      = utf8_encode($row[2]);
            $versionName  = utf8_encode($row[3]).' ('.utf8_encode($row[9]).') ('.$motor.')';

            $fechaI = substr(utf8_encode($row[6]), 0, 4);
            $fechaF = substr(utf8_encode($row[7]), 0, 4);

            $year         = $fechaI.' - '.$fechaF;
            $kw           = utf8_encode($row[8]);
            $displacement = utf8_encode($row[9]);

            $version = new Version();
            $version->setIdTecDoc($idTecDoc);
            $version->setModel($this->getReference('model'.$modelId));
            $version->setName($versionName);
            $version->setYear($year);
            $version->setMotor($motor);
            $version->setKw($kw);
            $version->setDisplacement($displacement);

            $manager->persist($version);
            $this->addReference('version'.$version->getIdTecDoc(), $version);
        }

        $manager->flush();
    }
}

?>