<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\WorkshopBundle\Entity\Workshop;

class Workshops extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 9; }
    
    public function load(ObjectManager $manager) {
        
        $typologies = array('Autoservice AD', 'Garage AD', 'Carrosserie AD');
                
        for($i=1;$i<=3;$i++) 
        {
            $entidad = new Workshop();
            $entidad->setName('workshop'.$i);
            $entidad->setPhoneNumber1($i.$i.$i.$i.$i.$i.$i.$i.$i);
            $entidad->setMovilePhone1($i.$i.$i.$i.$i.$i.$i.$i.$i);
            $entidad->setEmail1('workshop'.$i.'@workshop.es');
            $entidad->setActive('1');
            $entidad->setUpdateAt(new \DateTime());
            $entidad->setLowdateAt(new \DateTime());
            $entidad->setCreatedAt(new \DateTime());
            $entidad->setModifiedAt(new \DateTime());
            $entidad->setRegion($this->getReference('Catalunya'));
            $entidad->setProvince($this->getReference('Barcelona'));
            $entidad->setModifyBy($this->getReference('superadmin'));
            $entidad->setPartner($this->getReference('partner'.$i));
            $entidad->setTypology($this->getReference($typologies[$i-1]));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
        }
        $manager->flush();
    }
}

?>
