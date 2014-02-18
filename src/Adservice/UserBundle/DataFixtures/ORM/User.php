<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;

class Users extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 11; }
    
    public function load(ObjectManager $manager) {
        for($i=0;$i<5;$i++)
        {
            $users[] = array(   'username'        => 'user'.$i, 
                                'password'        => 'QeDeEveLRGfrw94I1iCOzs37BWE+xnmQFjkT5EeUTwDjXWRIjQsSYghkc2kAefhuFMTPvnIaplq7xbPOesN22Q==' ,
                                'salt'            => '84a2646fadd65616d73199e9f1fae1e1',
                                'name'            => 'Usuario',
                                'surname'         => $i,
                                'email_1'         => 'user'.$i.'@user.es',
                                'dni'             => $i.$i.$i.$i.$i.$i.$i.$i.'S',
                                'active'          => '1',
                                'region'          => 'Catalunya',
                                'province'        => 'Barcelona',
                                'created_at'      => new \DateTime(),
                                'modified_at'     => new \DateTime(),
                                'country'         => 'Spain',
                                'language'        => 'es_ES',
                                'partner'         => null,
                                'workshop'        => 'Taller AD Bcn',
                                'user_role'       => 'ROLE_USER',
                 );
        }
        
        $this->createUsers($manager, $users);
        
    }
    
    public static function createUsers($manager, $users)
    {
        foreach ($users as $user) {
            $entidad = new User();
            $entidad->setUsername   ($user['username']);
            $entidad->setPassword   ($user['password']);
            $entidad->setSalt       ($user['salt']);
            $entidad->setName       ($user['name']);
            $entidad->setSurname    ($user['surname']);
            $entidad->setEmail1     ($user['email_1']);
            $entidad->setDni        ($user['dni']);
            $entidad->setActive     ($user['active']);
            $entidad->setRegion     ($this->getReference($user['region']));
            $entidad->setProvince   ($this->getReference($user['province']));
            $entidad->setCreatedAt  ($user['created_at']);
            $entidad->setModifiedAt ($user['modified_at']);
            $entidad->setCountry    ($this->getReference($user['country']));
            $entidad->setLanguage   ($this->getReference($user['language']));
            if($this->getReference($user['partner']) != null){
                $entidad->setPartner($this->getReference($user['partner']));
            }
            if($this->getReference($user['workshop']) != null ) {
                $entidad->setWorkshop($this->getReference($user['workshop']));
            }
            $entidad->addRole       ($this->getReference($user['user_role']));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getUsername(), $entidad);
            
        }
        $manager->flush();
    }
}

?>