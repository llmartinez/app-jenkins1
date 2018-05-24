<?php

namespace Adservice\CarBundle\Service;

use Adservice\CarBundle\Entity\Car;
use Adservice\UtilBundle\Controller\UtilController;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CarHelper
{
    protected $em;
    protected $user;
    protected $twig;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage, TwigEngine $twig)
    {
        $this->em = $em;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->twig = $twig;
    }

    /**
     * Update car
     *
     * @param Car $originalCar
     * @param Car $updatedCar
     *
     * @return boolean
     */
    public function updateCar(Car $originalCar, Car $updatedCar)
    {
        UtilController::saveEntity($this->em, $updatedCar, $this->user);

        //Si la matrícula/marca/modelo/version son diferentes
        if($originalCar->isSameCar($updatedCar) == false) {
            $this->lockAndUpdateTickets($originalCar, $updatedCar);
        }

        return true;
    }

    /**
     * Lock and update ticket log
     *
     * @param Car $originalCar
     * @param Car $updatedCar
     */
    public function lockAndUpdateTickets(Car $originalCar, Car $updatedCar)
    {
        //$lockStatus = $this->em->getRepository('TicketBundle:Status')->findOneBy(array('name' => 'closed'));

        $tickets = $this->em->getRepository('TicketBundle:Ticket')->findBy(array('car' => $originalCar));

        foreach($tickets as $ticket) {
            //$ticket->setStatus($lockStatus);
            $ticket->setLog($this->generateLog($originalCar, $updatedCar, $ticket->getLog(), $this->user));
            $ticket->setCar($updatedCar);
        }

        $this->em->flush();
    }



    /**
     * Generate log
     *
     * @param Car $originalCar
     * @param Car $updatedCar
     * @param string $log
     *
     * @return string
     */
    public function generateLog(Car $originalCar, Car $updatedCar, $log, $user)
    {
        return $this->twig->render('@Ticket/Car/log_car.html.twig', array(
            'date' => date('d-m-Y H:i:s'),
            'userId' => $user->getId(),
            'oldCar' => $originalCar->toStringExtended(),
            'updatedCar' => $updatedCar->toStringExtended(),
            'log' => $log
        ));
    }

}