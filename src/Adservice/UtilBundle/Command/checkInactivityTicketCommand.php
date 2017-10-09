<?php

namespace Adservice\UtilBundle\Command;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class checkInactivityTicketCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('check:inactivityTicket')
            ->setDescription("Checking ticket's inactivity time")
        ;
    }

    /**
     * Comando que comprueba el tiempo que lleva inactivo un ticket
     *      - INACTIVO: +2h sin actividad
     *        Se marca como inactivo y se actualiza la fecha de modificación para mostrar primero en el listado
     *
     * @param  InputInterface  $input  An InputInterface instance
     * @param  OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('status' => 1, 'pending' => 1));
        $status = $em->getRepository('TicketBundle:Status')->findAll();
        $sa = $em->getRepository('UserBundle:User')->find(1);

        $count = 0;

        foreach ($tickets as $ticket)
        {
            $diff = date_diff(new \DateTime(), $ticket->getModifiedAt());

            // INACTIVO: +2h sin actividad
            //           - Se marca como inactivo y se actualiza la fecha de modificación para mostrar primero en el listado
            if($diff->h >= 2 or $diff->days >= 1)
            {
                $ticket->setStatus($status[2]);
                $ticket->setModifiedBy($sa);
                $ticket->setModifiedAt(new \DateTime());

                $em->persist($ticket);

                $count++;
            }
        }
        $em->flush();

        $output->writeln('Se han modificado '.$count.' tickets por inactividad.');
    }
}