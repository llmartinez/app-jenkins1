<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;

class Users extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 11; }
    
    public function load(ObjectManager $manager) {
        $users = array(
            array(  'username'        => 'user1', 
                    'password'        => 'QeDeEveLRGfrw94I1iCOzs37BWE+xnmQFjkT5EeUTwDjXWRIjQsSYghkc2kAefhuFMTPvnIaplq7xbPOesN22Q==' ,
                    'salt'            => '84a2646fadd65616d73199e9f1fae1e1',
                    'name'            => 'Usuario',
                    'surname'         => '1',
                    'email_1'         => 'user1@user.es',
                    'dni'             => '11111111S',
                    'active'          => '1',
                    'region'          => 'Catalunya',
                    'province'        => 'Barcelona',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => 'Taller AD Bcn',
                    'country'         => 'Spain',
                    'language'        => 'es_ES',
                    'partner'         => null,
                    'user_role'       => 'ROLE_USER',
                 ),
            array(  'username'        => 'user2', 
                    'password'        => 'QeDeEveLRGfrw94I1iCOzs37BWE+xnmQFjkT5EeUTwDjXWRIjQsSYghkc2kAefhuFMTPvnIaplq7xbPOesN22Q==' ,
                    'salt'            => '84a2646fadd65616d73199e9f1fae1e1',
                    'name'            => 'Utilisateur',
                    'surname'         => '2',
                    'email_1'         => 'user2@user.es',
                    'dni'             => '22222222S',
                    'active'          => '1',
                    'region'          => 'Catalunya',
                    'province'        => 'Barcelona',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => 'Garage AD Bcn',
                    'country'         => 'France',
                    'language'        => 'fr_FR',
                    'partner'         => null,
                    'user_role'       => 'ROLE_USER',
                 ),
            array(  'username'        => 'user3', 
                    'password'        => 'QeDeEveLRGfrw94I1iCOzs37BWE+xnmQFjkT5EeUTwDjXWRIjQsSYghkc2kAefhuFMTPvnIaplq7xbPOesN22Q==' ,
                    'salt'            => '84a2646fadd65616d73199e9f1fae1e1',
                    'name'            => 'Usuario',
                    'surname'         => '3',
                    'email_1'         => 'user3@user.es',
                    'dni'             => '33333333S',
                    'active'          => '1',
                    'region'          => 'Madrid',
                    'province'        => 'Madrid ',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => 'Taller AD Madrid',
                    'country'         => 'Spain',
                    'language'        => 'es_ES',
                    'partner'         => null,
                    'user_role'       => 'ROLE_USER',
                 ),
            array(  'username'        => 'user4', 
                    'password'        => 'QeDeEveLRGfrw94I1iCOzs37BWE+xnmQFjkT5EeUTwDjXWRIjQsSYghkc2kAefhuFMTPvnIaplq7xbPOesN22Q==' ,
                    'salt'            => '84a2646fadd65616d73199e9f1fae1e1',
                    'name'            => 'User',
                    'surname'         => '4',
                    'email_1'         => 'user4@user.es',
                    'dni'             => '44444444S',
                    'active'          => '1',
                    'region'          => 'Canarias',
                    'province'        => 'Sta. Cruz de Tenerife',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => 'Taller AD Canarias',
                    'country'         => 'England',
                    'language'        => 'en_EN',
                    'partner'         => null,
                    'user_role'       => 'ROLE_USER',
                 ),
        );
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
            $entidad->setWorkshop   ($this->getReference($user['workshop']));
            $entidad->addRole       ($this->getReference($user['user_role']));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getUsername(), $entidad);
            
        }
        $manager->flush();
    }
}

?>