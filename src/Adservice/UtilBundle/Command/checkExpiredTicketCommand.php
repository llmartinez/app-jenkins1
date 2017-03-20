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
     *      EXPIRED: 
     *      - T.Información: Se cierra a los 10 días sin actividad
     *      - T.Diagnosis:   Se avisa al pasar 1 mes sin actividad
     *                       Se cierra 15 dias después si no ha habido actividad
     *
     * @param  InputInterface  $input  An InputInterface instance
     * @param  OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $tickets = $em->getRepository('TicketBundle:Ticket')->findByNot(array('status' => 2)); // Cualquier estado menos 2 - cerrado

        $status = $em->getRepository('TicketBundle:Status')->findAll();
        $sa = $em->getRepository('UserBundle:User')->find(1);

        $date = new \DateTime();
        $expDate = new \DateTime('+15 day');

        /** MAGIC BUG: por algun motivo sin el var_dump lanza "[ErrorException] Notice: Undefined property: DateTime::$date"
          - mas info: http://imgur.com/gallery/YsbKHg1 **/
            // var_dump($date);
            // $expDate = strtotime ( '+15 day' , strtotime ( $date->date ) ) ;
            // Se acaba usando DateTime de 15 dias

        $msg_expirated = $this->getContainer()->get('translator')->trans('mail.inactivity_warning');
        $msg_expired = $this->getContainer()->get('translator')->trans('mail.inactiveTicket.title');
        $count = 0;

        $message = \Swift_Message::newInstance()
            ->setFrom('noreply@adserviceticketing.com')
            ->setCharset('UTF-8')
            ->setContentType('text/html');

        foreach ($tickets as $ticket)
        {
            $diff = date_diff($date, $ticket->getModifiedAt());

            // INFO_CLOSED_TICKETS 
            // - T.Información: Se cierra a los 10 días sin actividad

                if($diff->days >= 10 and $ticket->getImportance() != null and $ticket->getImportance()->getImportance() == 'information')
                {
                    $ticket->setStatus($status[1]); // $status[1] = array(2 => 'closed')
                    $ticket->setSolution($msg_expired);
                    $ticket->setExpirationDate($date);
                    $ticket->setModifiedBy($sa);

                    $em->persist($ticket);

                    $message
                    ->setSubject($msg_expired)
                    ->setTo($ticket->getWorkshop()->getEmail1())
                    ->setBody($this->getContainer()->get('templating')->render('UtilBundle:Mailing:cmd_ticket_expired.html.twig', array('ticket' => $ticket)));

                    $this->getContainer()->get('mailer')->send($message);

                    $count++;
                }

            // EXPIRATED_TICKETS
            // - T.Diagnosis: Se manda un aviso al pasar 30 dias sin actividad y se setea 'expiration_date' a +15d

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

                    $message
                    ->setSubject($this->getContainer()->get('translator')->trans('mail.inactivity_warning.title'))
                    ->setTo($ticket->getWorkshop()->getEmail1())
                    ->setBody($this->getContainer()->get('templating')->render('UtilBundle:Mailing:cmd_ticket_expirated.html.twig', array('ticket' => $ticket)));

                    $this->getContainer()->get('mailer')->send($message);

                    $count++;
                }

            // DIAG_CLOSED_TICKETS (+45d sin actividad)
            // - Se cierra al pasar 15 dias si no ha habido actividad después EXPIRATED_TICKETS (30 dias sin actividad y de seteo de 'expiration_date')

                elseif($ticket->getStatus() == $status[3] and $diff->days >= 15 and $ticket->getExpirationDate() != null) // $status[3] = array(4 => 'expirated')
                {
                    $ticket->setStatus($status[1]); // $status[1] = array(2 => 'closed')
                    $ticket->setSolution($msg_expired);
                    $ticket->setModifiedBy($sa);

                    $em->persist($ticket);
   
                    $message
                    ->setSubject($msg_expired)
                    ->setTo($ticket->getWorkshop()->getEmail1())
                    ->setBody($this->getContainer()->get('templating')->render('UtilBundle:Mailing:cmd_ticket_expired.html.twig', array('ticket' => $ticket)));

                    $this->getContainer()->get('mailer')->send($message);

                    $count++;
                }
        }
        $em->flush();
        $output->writeln('Se han modificado '.$count.' tickets por inactividad.');
    }
}