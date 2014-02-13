<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\PartnerBundle\Entity\Partner;

class Partners extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 7; }
    
    public function load(ObjectManager $manager) {
        $partners = array(
            array(  'name'            => 'AD Barcelona'  , 
                    'phone_number_1'  => '111111111',
                    'phone_number_2'  => '111111112',
                    'fax'             => '111111113',
                    'email_1'         => 'adbcn@partner.es',
                    'email_2'         => 'adbcn@partner.com',
                    'address'         => 'Sede AD Bcn',
                    'postal_code'     => '08080',
                    'active'          => '1',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'region'          => 'Catalunya',
                    'province'        => 'Barcelona',
                    'modify_by'       => 'SuperAdmin',
                 ),
            array(  'name'            => 'AD Madrid'  , 
                    'phone_number_1'  => '222222221',
                    'phone_number_2'  => '222222222',
                    'fax'             => '222222223',
                    'email_1'         => 'admadrid@partner.es',
                    'email_2'         => 'admadrid@partner.com',
                    'address'         => 'Sede AD Madrid',
                    'postal_code'     => '07070',
                    'active'          => '1',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'region'          => 'Madrid',
                    'province'        => 'Madrid ',
                    'modify_by'       => 'SuperAdmin',
                 ),
            array(  'name'            => 'AD Canarias'  , 
                    'phone_number_1'  => '333333331',
                    'phone_number_2'  => '333333332',
                    'fax'             => '333333333',
                    'email_1'         => 'adcanarias@partner.es',
                    'email_2'         => 'adcanarias@partner.com',
                    'address'         => 'Sede AD Canarias',
                    'postal_code'     => '09090',
                    'active'          => '1',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'region'          => 'Canarias',
                    'province'        => 'Sta. Cruz de Tenerife',
                    'modify_by'       => 'SuperAdmin',
                 ),

        );
        foreach ($partners as $partner) {
            $entidad = new Partner();
            $entidad->setName($partner['name']);
            $entidad->setPhoneNumber1($partner['phone_number_1']);
            $entidad->setPhoneNumber2($partner['phone_number_2']);
            $entidad->setFax($partner['fax']);
            $entidad->setEmail1($partner['email_1']);
            $entidad->setEmail2($partner['email_2']);
            $entidad->setAddress($partner['address']);
            $entidad->setPostalCode($partner['postal_code']);
            $entidad->setActive($partner['active']);
            $entidad->setCreatedAt($partner['created_at']);
            $entidad->setModifiedAt($partner['modified_at']);
            $entidad->setRegion($this->getReference($partner['region']));
            $entidad->setProvince($this->getReference($partner['province']));
            $entidad->setModifyBy($this->getReference($partner['modify_by']));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
