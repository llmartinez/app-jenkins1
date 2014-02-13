<?php
namespace Adservice\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Adservice\UserBundle\Entity\User;

class Assessors extends AbstractFixture implements OrderedFixtureInterface {
    
    public function getOrder(){ return 10; }
    
    public function load(ObjectManager $manager) {
        $users = array(
            array(  'username'        => 'assessor1', 
                    'password'        => '6LQcTkIP+7AYn2UQfafPanykBGZtgXgw0JWS3RdS1x5rPy2B9nYmXbPqpwHHMtxOAHuQ18TmUq72C32bXIZT3Q==' ,
                    'salt'            => '50b62d727a358f509f12c1cdec7dcd9a',
                    'name'            => 'Asesor',
                    'surname'         => '1',
                    'email_1'         => 'assessor1@assessor.es',
                    'dni'             => '11111111S',
                    'active'          => '1',
                    'region'          => 'Catalunya',
                    'province'        => 'Barcelona',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => null,
                    'country'         => 'Spain',
                    'language'        => 'es_ES',
                    'partner'         => 'AD Barcelona',
                    'user_role'       => 'ROLE_ASSESSOR',
                 ),
            array(  'username'        => 'assessor2', 
                    'password'        => '6LQcTkIP+7AYn2UQfafPanykBGZtgXgw0JWS3RdS1x5rPy2B9nYmXbPqpwHHMtxOAHuQ18TmUq72C32bXIZT3Q==' ,
                    'salt'            => '50b62d727a358f509f12c1cdec7dcd9a',
                    'name'            => 'Conseiller',
                    'surname'         => '2',
                    'email_1'         => 'assessor2@assessor.es',
                    'dni'             => '22222222S',
                    'active'          => '1',
                    'region'          => 'Catalunya',
                    'province'        => 'Barcelona',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => null,
                    'country'         => 'France',
                    'language'        => 'fr_FR',
                    'partner'         => 'AD Barcelona',
                    'user_role'       => 'ROLE_ASSESSOR',
                 ),
            array(  'username'        => 'assessor3', 
                    'password'        => '6LQcTkIP+7AYn2UQfafPanykBGZtgXgw0JWS3RdS1x5rPy2B9nYmXbPqpwHHMtxOAHuQ18TmUq72C32bXIZT3Q==' ,
                    'salt'            => '50b62d727a358f509f12c1cdec7dcd9a',
                    'name'            => 'Asesor',
                    'surname'         => '3',
                    'email_1'         => 'assessor3@assessor.es',
                    'dni'             => '33333333S',
                    'active'          => '1',
                    'region'          => 'Madrid',
                    'province'        => 'Madrid ',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => null,
                    'country'         => 'Spain',
                    'language'        => 'es_ES',
                    'partner'         => 'AD Madrid',
                    'user_role'       => 'ROLE_ASSESSOR',
                 ),
            array(  'username'        => 'assessor4', 
                    'password'        => '6LQcTkIP+7AYn2UQfafPanykBGZtgXgw0JWS3RdS1x5rPy2B9nYmXbPqpwHHMtxOAHuQ18TmUq72C32bXIZT3Q==' ,
                    'salt'            => '50b62d727a358f509f12c1cdec7dcd9a',
                    'name'            => 'Assessor',
                    'surname'         => '4',
                    'email_1'         => 'assessor4@assessor.es',
                    'dni'             => '44444444S',
                    'active'          => '1',
                    'region'          => 'Canarias',
                    'province'        => 'Sta. Cruz de Tenerife',
                    'created_at'      => new \DateTime(),
                    'modified_at'     => new \DateTime(),
                    'workshop'        => null,
                    'country'         => 'England',
                    'language'        => 'en_EN',
                    'partner'         => 'AD Canarias',
                    'user_role'       => 'ROLE_ASSESSOR',
                 ),
        );
        foreach ($users as $user) {
            $entidad = new User();
            $entidad->setUsername($user['username']);
            $entidad->setPassword($user['password']);
            $entidad->setSalt($user['salt']);
            $entidad->setName($user['name']);
            $entidad->setSurname($user['surname']);
            $entidad->setEmail1($user['email_1']);
            $entidad->setDni($user['dni']);
            $entidad->setActive($user['active']);
            $entidad->setRegion($this->getReference($user['region']));
            $entidad->setProvince($this->getReference($user['province']));
            $entidad->setCreatedAt($user['created_at']);
            $entidad->setModifiedAt($user['modified_at']);
            $entidad->setCountry($this->getReference($user['country']));
            $entidad->setLanguage($this->getReference($user['language']));
            $entidad->setPartner($this->getReference($user['partner']));
            $entidad->addRole($this->getReference($user['user_role']));
            $manager->persist($entidad);
            
            $this->addReference($entidad->getUsername(), $entidad);
            
        }
        $manager->flush();
    }
}

?>