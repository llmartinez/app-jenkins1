<?php
namespace Adservice\CarBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\PartnerBundle\Entity\Partner;
use Adservice\UtilBundle\DataFixtures\ORM\Data as Data;

class Partners extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 6; }
    
    public function load(ObjectManager $manager) {
        for($i=1;$i<5;$i++)
        {
            $entidad = new Partner(); 
            $entidad->setName('partner'.$i);
            $entidad->setPhoneNumber1($i.$i.$i.$i.$i.$i.$i.$i.$i);
            $entidad->setPhoneNumber2($i.$i.$i.$i.$i.$i.$i.$i.$i+1);
            $entidad->setFax($i.$i.$i.$i.$i.$i.$i.$i.$i+2);
            $entidad->setEmail1('partner'.$i.'@partner.es');
            $entidad->setEmail2('partner'.$i.'@partner.com');
            $entidad->setAddress(Data::getDireccion());
            $entidad->setPostalCode(Data::getCodigoPostal());
            $entidad->setActive('1');
            $entidad->setCreatedAt(new \DateTime());
            $entidad->setModifiedAt(new \DateTime());
            $entidad->setModifyBy($this->getReference('superadmin'));
            $entidad->setRegion($this->getReference(Data::getRegions()));
            $entidad->setProvince($this->getReference(Data::getProvinces()));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getName(), $entidad);
        }
        $sa = $this->getReference('superadmin');
        $sa->setPartner($this->getReference('partner1'));
        $manager->persist($sa);
        $manager->flush();
    }
}

?>
