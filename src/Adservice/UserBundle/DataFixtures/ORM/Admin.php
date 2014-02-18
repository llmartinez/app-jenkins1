<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;

class Admins extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 7; }
    
    public function load(ObjectManager $manager) {
        $users = array(
            array(  'username'        => 'Admin', 
                    'password'        => 'mcobJtS8ExG2JAUrEg3VzeLlgyxyyviEr5/uO+vel5stbf3TeHTJxZoHUZqfVHOqON1QNKcm5PXemcz9rW4BZg==' ,
                    'salt'            => '79bc6981377363689c90b9c7d6962da9',
                    'name'            => 'Admin',
                    'surname'         => '1',
                    'fax'             => '147258369',
                    'email_1'         => 'admin@admin.es',
                    'dni'             => '00000000A',
                    'active'          => '1',
                    'region'          => 'Catalunya',
                    'province'        => 'Barcelona',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => null,
                    'country'         => 'Spain',
                    'language'        => 'es_ES',
                    'partner'         => 'AD Barcelona',
                    'user_role'       => 'ROLE_ADMIN',
                 )
        );
        foreach ($users as $user) {
            $entidad = new User();
            $entidad->setUsername($user['username']);
            $entidad->setPassword($user['password']);
            $entidad->setSalt($user['salt']);
            $entidad->setName($user['name']);
            $entidad->setSurname($user['surname']);
            $entidad->setCity($user['city']);
            $entidad->setPhoneNumber1($user['phone_number_1']);
            $entidad->setPhoneNumber2($user['phone_number_2']);
            $entidad->setMovileNumber1($user['movile_number_1']);
            $entidad->setMovileNumber2($user['movile_number_2']);
            $entidad->setFax($user['fax']);
            $entidad->setEmail1($user['email_1']);
            $entidad->setEmail2($user['email_2']);
            $entidad->setDni($user['dni']);
            $entidad->setActive($user['active']);
            $entidad->setRegion($this->getReference($user['region']));
            $entidad->setProvince($this->getReference($user['province']));
            $entidad->setCreatedAt($user['created_at']);
            $entidad->setModifiedAt($user['modified_at']);
            $entidad->setCountry($this->getReference($user['country']));
            $entidad->setLanguage($this->getReference($user['language']));
            if($this->getReference($user['partner']) != null){
                $entidad->setPartner($this->getReference($user['partner']));
            }
            if($this->getReference($user['workshop']) != null ) {
                $entidad->setWorkshop($this->getReference($user['workshop']));
            }
            $entidad->addRole($this->getReference($user['user_role']));
            $manager->persist($entidad);
            
            $this->addReference('SuperAdmin', $entidad);
            
        }
        $manager->flush();
    }
}

?>