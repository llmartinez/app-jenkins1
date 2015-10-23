<?php

namespace Adservice\UtilBundle\Command;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class checkTestWorkshopsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('check:testworkshop')
            ->setDescription("Checking workshop's test expiration dates")
        ;
    }

    /**
     * Comando que comprueba la fecha de expiración de los talleres en pruebas
     *
     * @param  InputInterface  $input  An InputInterface instance
     * @param  OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $workshops = $em->getRepository('WorkshopBundle:Workshop')->findBy(array('test' => 1));
        //$count = 0;

        foreach ($workshops as $workshop) {

            $diff = date_diff(new \DateTime(), $workshop->getEndtestAt());

            if(($diff->invert == 1 and $diff->days >= 0)) {

                //$mail   = 'info@adserviceticketing.com';
                $mail   = 'dmaya@grupeina.com';

                $message = \Swift_Message::newInstance()
                    ->setSubject('Se ha terminado el período de prueba del taller '.$workshop->getId())
                    ->setFrom('noreply@adserviceticketing.com')
                    ->setTo($mail)
                    ->setCharset('UTF-8')
                    ->setContentType('text/html')
                    ->setBody($this->getContainer()->get('templating')->render('UtilBundle:Mailing:end_test_workshop.html.twig', array('workshop' => $workshop)));

                $this->getContainer()->get('mailer')->send($message);


                $workshop->setEndtestAt(null);
                $workshop->setTest(0);
                $em->persist($workshop);
                $em->flush();

                //$count++;
            }
            // $output->writeln('Se han modificado '.$count.' talleres.');
        }
    }
}