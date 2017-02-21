<?php

namespace Adservice\UtilBundle\Command;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Adservice\TicketBundle\Entity\Post;

class checkExpiredTicketCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('check:expiredTicket')
            ->setDescription("Checking ticket's expired time")
        ;
    }

    /**
     * Comando que comprueba el tiempo que lleva inactivo un ticket
     *      - CADUCADO: +1mes sin actividad
     *          - Información: Se cierra a los 10 días
     *          - Diagnosis: Se avisa al pasar 1 mes
     *                       Se cierra 15 dias después si no ha habido actividad
     *
     * @param  InputInterface  $input  An InputInterface instance
     * @param  OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $tickets = $em->getRepository('TicketBundle:Ticket')->findByNot(array('status' => 2));

        $status = $em->getRepository('TicketBundle:Status')->findAll();
        $sa = $em->getRepository('UserBundle:User')->find(1);

        $date = new \DateTime();
        // MAGIC: Esto es Magia: por algun motivo sin el var_dump
        // mas info: http://imgur.com/gallery/YsbKHg1
        //var_dump($date);
        //$expDate = strtotime ( '+15 day' , strtotime ( $date->date ) ) ;
        // Se acaba usando DateTime de 15 dias
        $expDate = new \DateTime('+15 day');

        $msg_expirated = $this->getContainer()->get('translator')->trans('mail.inactivity_warning');
        $msg_expired = $this->getContainer()->get('translator')->trans('mail.inactiveTicket.title');

        $expirated_tickets = array();   // +30d sin actividad
        $info_closed_tickets = array(); // +10d sin actividad
        $diag_closed_tickets = array(); // +45d sin actividad

        $count = 0;

        $message = \Swift_Message::newInstance()
            ->setFrom('noreply@adserviceticketing.com')
            ->setCharset('UTF-8')
            ->setContentType('text/html');

        foreach ($tickets as $ticket)
        {
            $diff = date_diff($date, $ticket->getModifiedAt());

            // INFO_CLOSED_TICKETS (INFO +10d sin actividad)

                if($diff->days >= 10 and $ticket->getImportance() != null and $ticket->getImportance()->getImportance() == 'information')
                {
                    $ticket->setStatus($status[1]); // $status[1] = array(2 => 'closed')
                    $ticket->setSolution($msg_expired);
                    $ticket->setExpirationDate($date);
                    $ticket->setModifiedBy($sa);

                    $em->persist($ticket);
/*
                    $message
                    ->setSubject($msg_expired)
                    ->setTo($ticket->getWorkshop()->getEmail1())
                    ->setBody($this->getContainer()->get('templating')->render('UtilBundle:Mailing:cmd_ticket_expired.html.twig', array('ticket' => $ticket)));

                    $this->getContainer()->get('mailer')->send($message);
*/
                    $count++;
                }

            // DIAG_CLOSED_TICKETS (DIAG +45d sin actividad)
                // se calculan +15d ya que a los 30d se crea un aviso y se setea 'expiration_date' a +15d

                elseif($ticket->getStatus() == $status[3] and $diff->days >= 15 and $ticket->getExpirationDate() != null) // $status[3] = array(4 => 'expirated')
                {
                    $ticket->setStatus($status[1]); // $status[1] = array(2 => 'closed')
                    $ticket->setSolution($msg_expired);
                    $ticket->setModifiedBy($sa);

                    $em->persist($ticket);
/*   
                    $message
                    ->setSubject($msg_expired)
                    ->setTo($ticket->getWorkshop()->getEmail1())
                    ->setBody($this->getContainer()->get('templating')->render('UtilBundle:Mailing:cmd_ticket_expired.html.twig', array('ticket' => $ticket)));

                    $this->getContainer()->get('mailer')->send($message);
*/
                    $count++;
                }

            // EXPIRATED_TICKETS (DIAG +30d sin actividad)

                elseif($diff->days >= 30 and $ticket->getExpirationDate() == null)
                {
                    $post = new Post();
                    $post->setTicket($ticket);
                    $post->setMessage($msg_expirated);
                    $post->setCreatedBy($sa);
                    $post->setModifiedBy($sa);
                    $post->setCreatedAt($date);
                    $post->setModifiedAt($date);

                    $ticket->setStatus($status[3]); // $status[3] = array(4 => 'expirated')
                    $ticket->setModifiedAt($date);
                    $ticket->setModifiedBy($sa);

                    $ticket->setExpirationDate($expDate);

                    $em->persist($post);
                    $em->persist($ticket);
/*
                    $message
                    ->setSubject($this->getContainer()->get('translator')->trans('mail.inactivity_warning.title'))
                    ->setTo($ticket->getWorkshop()->getEmail1())
                    ->setBody($this->getContainer()->get('templating')->render('UtilBundle:Mailing:cmd_ticket_expirated.html.twig', array('ticket' => $ticket)));

                    $this->getContainer()->get('mailer')->send($message);
*/
                    $count++;
                }
        }
        $em->flush();
        $output->writeln('Se han modificado '.$count.' tickets por inactividad.');
    }
}