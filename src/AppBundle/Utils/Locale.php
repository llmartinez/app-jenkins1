<?php
namespace AppBundle\Utils;
 
class Locale
{
    /* Obtiene el Locale por defecto: 
       - Busca el idioma del usuario, sino del navegador, y sino devuelve el locale por defecto (config.yml) 
    */
    public function getDefaultLocale($_this, $request)
    {
        $user = $_this->get('security.context')->getToken()->getUser();

        if(isset($user))
        {
            $lc = $_this->get('utils')->getLanguages()[$user->getLanguage()];
        }
        if(!isset($lc))
        {
            $lang = $request->headers->get('accept-language');
            if($lang != null) $lc = substr($lang, 0, 2);
        }

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