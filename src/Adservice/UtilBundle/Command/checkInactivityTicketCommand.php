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
     *     - Diagnosis: Se cierra a los 2 meses
     *     - Información: Se cierra a los 10 días
     *
     * @param  InputInterface  $input  An InputInterface instance
     * @param  OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $tickets = $em->getRepository('TicketBundle:Ticket')->findBy(array('status' => 1));
        $closed_tickets = array();
        $pending_tickets = array();
        $count = 0;
        $count_closed = 0;
        $count_pending = 0;

        foreach ($tickets as $ticket) {

            if($ticket->getImportance() != NULL)
            {
                $importance = $ticket->getImportance()->getImportance();
                $diff = date_diff(new \DateTime(), $ticket->getModifiedAt());
                $close = false;

                if( $importance == 'information' and $diff->days > 10)
                {
                    $closed_tickets[] = $ticket->getId();
                    $count++;
                    $count_closed++;

                    $closed = $em->getRepository('TicketBundle:Status')->find(2);
                    $ticket->setStatus($closed);
                    $ticket->setModifiedAt(new \DateTime());
                    $em->persist($ticket);
                }
                elseif ($importance == 'advanced_diagnostics' and $diff->m >= 2)
                {
                    $pending_tickets[] = $ticket->getId();
                    $count++;
                    $count_pending++;

                    $ticket->setPending(1);
                    $ticket->setModifiedAt(new \DateTime());
                    $em->persist($ticket);
                }
            }
        }
        $em->flush();

        if($count > 0) {
            $mail   = $this->container->getParameter('mail_info');
            $message = \Swift_Message::newInstance()
                ->setSubject('Se han modificado '.$count.' tickets por inactividad.')
                ->setFrom('noreply@adserviceticketing.com')
                ->setTo($mail)
                ->setCharset('UTF-8')
                ->setContentType('text/html')
                ->setBody($this->getContainer()->get('templating')
                            ->render('UtilBundle:Mailing:admin_inactivity_tickets.html.twig', array('count' => $count,
                                                                                                    'count_closed' => $count_closed,
                                                                                                    'count_pending' => $count_pending,
                                                                                                    'closed_tickets' => $closed_tickets,
                                                                                                    'pending_tickets' => $pending_tickets)));
            $this->getContainer()->get('mailer')->send($message);
        }

        $output->writeln('Se han modificado '.$count.' tickets por inactividad.');
    }
}