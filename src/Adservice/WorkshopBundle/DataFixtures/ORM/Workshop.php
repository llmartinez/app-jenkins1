<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\WorkshopBundle\Entity\Workshop;

class Workshops extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 9; }
    
    public function load(ObjectManager $manager) {
        $workshops = array(
            array(  'name'            => 'Taller AD Bcn' , 
                    'phone_number_1'  => '111111111',
                    'movile_number_1' => '111111111',
                    'email_1'         => 'ad@workshop.es',
                    'adservice_plus'  => '0',
                    'active'          => '1',
                    'update_at'       => new \DateTime(),
                    'lowdate_at'      => new \DateTime(), 
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'region'          => 'Catalunya',
                    'province'        => 'Barcelona',
                    'modify_by'       => 'SuperAdmin',
                    'partner'         => 'AD Barcelona',
                    'typology'        => 'Autoservice AD',
                 ),
            array(  'name'            => 'Garage AD Bcn' , 
                    'phone_number_1'  => '111111111',
                    'movile_number_1' => '111111111',
                    'email_1'         => 'ad@workshop.es',
                    'adservice_plus'  => '0',
                    'active'          => '1',
                    'update_at'       => new \DateTime(),
                    'lowdate_at'      => new \DateTime(), 
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'region'          => 'Catalunya',
                    'province'        => 'Barcelona',
                    'modify_by'       => 'SuperAdmin',
                    'partner'         => 'AD Barcelona',
                    'typology'        => 'Garage AD',
                 ),
            array(  'name'            => 'Taller AD Madrid' ,
                    'phone_number_1'  => '111111111',
                    'movile_number_1' => '111111111',
                    'email_1'         => 'ad@workshop.es',
                    'adservice_plus'  => '0',
                    'active'          => '1',
                    'update_at'       => new \DateTime(),
                    'lowdate_at'      => new \DateTime(), 
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'region'          => 'Madrid',
                    'province'        => 'Madrid ',
                    'modify_by'       => 'SuperAdmin',
                    'partner'         => 'AD Madrid',
                    'typology'        => 'Autoservice AD',
                 ),
            array(  'name'            => 'Garage AD Madrid'  ,
                    'phone_number_1'  => '111111111',
                    'movile_number_1' => '111111111',
                    'email_1'         => 'ad@workshop.es',
                    'adservice_plus'  => '0',
                    'active'          => '1',
                    'update_at'       => new \DateTime(),
                    'lowdate_at'      => new \DateTime(), 
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'region'          => 'Madrid',
                    'province'        => 'Madrid ',
                    'modify_by'       => 'SuperAdmin',
                    'partner'         => 'AD Madrid',
                    'typology'        => 'Garage AD',
                 ),
            array(  'name'            => 'Taller AD Canarias'  ,
                    'phone_number_1'  => '111111111',
                    'movile_number_1' => '111111111', 
                    'email_1'         => 'ad@workshop.es',
                    'adservice_plus'  => '0',
                    'active'          => '1',
                    'update_at'       => new \DateTime(),
                    'lowdate_at'      => new \DateTime(), 
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'region'          => 'Canarias',
                    'province'        => 'Sta. Cruz de Tenerife',
                    'modify_by'       => 'SuperAdmin',
                    'partner'         => 'AD Canarias',
                    'typology'        => 'Carrosserie AD',
                 ),
        );
        foreach ($workshops as $workshop) {
            $entidad = new Workshop();
            $entidad->setName($workshop['name']);
            $entidad->setPhoneNumber1($workshop['phone_number_1']);
            $entidad->setMovilePhone1($workshop['movile_number_1']);
            $entidad->setEmail1($workshop['email_1']);
            $entidad->setActive($workshop['active']);
            $entidad->setUpdateAt($workshop['update_at']);
            $entidad->setLowdateAt($workshop['lowdate_at']);
            $entidad->setCreatedAt($workshop['created_at']);
            $entidad->setModifiedAt($workshop['modified_at']);
            $entidad->setRegion($this->getReference($workshop['region']));
            $entidad->setProvince($this->getReference($workshop['province']));
            $entidad->setModifyBy($this->getReference($workshop['modify_by']));
            $entidad->setPartner($this->getReference($workshop['partner']));
            $entidad->setTypology($this->getReference($workshop['typology']));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
