<?php

namespace Adservice\WorkshopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ImportWorkshopController extends Controller {


    /**
     * Importa talleres desde CSV
     * @return type
     * @throws AccessDeniedException
     */
    public function importWorkshopAction() {
        $security = $this->get('security.context');
        if ($security->isGranted('ROLE_ADMIN') === false)
            throw new AccessDeniedException();

        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

		$form = $this->createFormBuilder()
		        ->add('submitFile', 'file', array('label' => 'File to Submit'))
		        ->getForm();

		if ($request->getMethod('post') == 'POST') {

		    $form->bindRequest($request);

		    if ($form->isValid()) {

		        $file = $form->get('submitFile');



var_dump($file);

$csv = fopen($file, 'r');
$cpt = 0;

while (($columns = fgetcsv($csv, 0, ',', '"')) !== false) {
   var_dump($columns[0]); // display first column

   $cpt++;
}
die;



		    }

		 }

		return $this->render('WorkshopBundle:Import:form.html.twig',
		    array('form' => $form->createView(),)
		);

    }
	public function readCSV($csvFile){
		$file_handle = fopen($csvFile, 'r');
		while (!feof($file_handle) ) {
			$line_of_text[] = fgetcsv($file_handle, 1024);
		}
		fclose($file_handle);
		return $line_of_text;
	}
}