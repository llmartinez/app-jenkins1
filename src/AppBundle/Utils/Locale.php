<?php
namespace AppBundle\Utils;
 
class Locale
{
    /* Obtiene el Locale por defecto: 
       - Primero busca el idioma del navegador, si no lo encuentra devuelve el locale por defecto (config.yml) 
    */
    public function getDefaultLocale($request)
    {
        $lang = $request->headers->get('accept-language');
        if($lang != null) $lc = substr($lang, 0, 2);

        if($lc != null) return $lc;
        else            return $request->getLocale();
    }

    /* Cambia el locale y redirecciona a la url anterior */
    public function changeLocale($_this, $request, $locale) {

        $currentPath = $request->server->get('HTTP_REFERER');

        if($locale != null AND $locale != $request->getLocale())
        {
            $currentLocale = $request->getLocale();

            if($locale != null AND $locale != $currentLocale)
            {
                // Cambio el idioma
                $request->setLocale($locale);
                $currentPath = str_replace('/'.$currentLocale.'/', '/'.$locale.'/', $currentPath);
            }
        }

        if($currentPath != "") $_this->redirect($currentPath);
        else                   $_this->redirect($_this->generateUrl('index', array('locale' => $locale)));

    }
}