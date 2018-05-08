<?php

namespace Adservice\UtilBundle\Services;

interface WebserviceInterface
{
    function getData($matricula);

    function transformData($data);

    function checkData($coincidencia);
}
