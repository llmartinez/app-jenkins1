<?php
namespace AppBundle\Utils;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
 
class Export
{

//  HowToUse(pdf):     
//      $html = $this->renderView('::template.html.twig');
//      return $this->get('export')->pdf($this, "name", $html);

//      ... MoreInfo: https://github.com/KnpLabs/KnpSnappyBundle
    static function pdf($_this, $name, $html)
    {
        return new Response(
            $_this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="'.$name.'.pdf"'
            )
        );
    }
    
//  HowToUse(xls):     
//      $name = "TEST";
//      $headers = array("name", "tel"); // - $headers debe coincidir con las KEYS del array $items
//      $items = array();
//      $items[0] = array('name' => 'Name1', 'tel' => 'Tel1');
//      $items[1] = array('name' => 'Name2', 'tel' => 'Tel2');
//      return $this->get('export')->xls($this, $name, $headers, $items);

//      ... MoreInfo: https://github.com/PHPOffice/PHPExcel/tree/develop/Examples
    static function xls($_this/*, $name, $headers, $items*/)
    {
        /* For Testing */
        /* End Testing */

        // Variables para organizar las filas y columnas del xls
        $columns = array('-', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'Z', 'Y', 'Z');
        $col = 1;
        $row = 1;

        // solicitamos el servicio 'phpexcel' y creamos el objeto vacío...
        $phpExcelObject = $_this->get('phpexcel')->createPHPExcelObject();

        // asignamos una serie de propiedades al servicio
        $phpExcelObject->getProperties()
            ->setCreator("Grup Eina")
            ->setLastModifiedBy("Grup Eina")
            ->setTitle($name)
            ->setSubject($name);

        // establecemos como hoja activa la primera, y le asignamos un título
        $phpExcelObject->setActiveSheetIndex(0);
        $phpExcelObject->getActiveSheet()->setTitle($name);
        
        // escribimos las cabeceras de los campos que vamos a exportar
        foreach ($headers as $header) {
            $cell = $columns[$col].$row;
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue($cell, $header);
            $col++;
        }

        // ponemos la primera fila en negrita
        $phpExcelObject->getActiveSheet()->getStyle('A1:'.$columns[$col-1].'1')->getFont()->setBold(true);

        $row++;
        $col = 1;
        // recorremos los registros escribiéndolos en las celdas correspondientes
        foreach ($items as $item) {
            for ($col=0; $col < sizeof($headers); $col++) { 
                $cell = $columns[$col+1].$row;
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue($cell, $item[$headers[$col]]);
            }
            $row++;
        }

        // se crea el writer
        $writer = $_this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $_this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $name.'.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}