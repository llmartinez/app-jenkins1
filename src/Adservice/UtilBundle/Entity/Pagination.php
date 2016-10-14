<?php

namespace Adservice\UtilBundle\Entity;

/**
 * Adservice\UtilBundle\Entity\Pagination
 *
 * @author Daniel Maya
 */
class Pagination
{
    private $max_rows;       //Maximo de registros por pagina
    private $num_side_pages; //Numero de paginas a cada lado de la pagina actual

    private $page;           //Numero de la pagina actual
    private $first_row;      //Numero del primer registro a mostrar
    private $last_row;       //Numero del ultimo registro a mostrar
    private $total_pag;      //Numero total de paginas

    public function __construct($page=1) {
        $this->setPage($page);
        $this->setMaxRows(10);
        $this->setNumSidePages(5);
        $this->setLastRow($this->getMaxRows() * $page);
        $this->setFirstRow($this->getLastRow() - $this->getMaxRows()  );
    }

    public function __toString() {
        return $this->getMaxRows();
    }

    /**
     * Set max_rows
     *
     * @param string $max_rows
     */
    public function setMaxRows($max_rows)
    {
        $this->max_rows = $max_rows;
        $this->last_row = $this->max_rows * $this->page;
        $this->first_row = $this->last_row - $this->max_rows;
    }
    /**
     * Get max_rows
     *
     * @return string
     */
    public function getMaxRows()
    {
        return $this->max_rows;
    }

    /**
     * Set num_side_pages
     *
     * @param string $num_side_pages
     */
    public function setNumSidePages($num_side_pages)
    {
        $this->num_side_pages = $num_side_pages;
    }
    /**
     * Get num_side_pages
     *
     * @return string
     */
    public function getNumSidePages()
    {
        return $this->num_side_pages;
    }

    /**
     * Set page
     *
     * @param string $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set first_row
     *
     * @param string $first_row
     */
    public function setFirstRow($first_row)
    {
        $this->first_row = $first_row;
    }

    /**
     * Get first_row
     *
     * @return string
     */
    public function getFirstRow()
    {
        return $this->first_row;
    }

    /**
     * Set last_row
     *
     * @param string $last_row
     */
    public function setLastRow($last_row)
    {
        $this->last_row = $last_row;
    }

    /**
     * Get last_row
     *
     * @return string
     */
    public function getLastRow()
    {
        return $this->last_row;
    }

    /**
     * Set total_pag
     *
     * @param string $total_pag
     */
    public function setTotalPag($total_pag)
    {
        $this->total_pag = $total_pag;
    }

    /**
     * Get total_pag
     *
     * @return string
     */
    public function getTotalPag()
    {
        return $this->total_pag;
    }

    /**
     * Set total_pag
     *
     * @param string $total_pag
     */
    public function setTotalPagByLength($length)
    {
        $this->total_pag = ceil( $length / $this->getMaxRows() );
    }

    /**
     * Get prev_pages
     *
     * @return string
     */
    public function getPrevPages()
    {
        $prev_pages     = 0;
        $page           = $this->getPage();
        $num_side_pages = $this->getNumSidePages();

        for ($i=1;$i<$num_side_pages;$i++)
        {
            if($page-$i > 0) $prev_pages++;
        }
        return $prev_pages;
    }

    /**
     * Get next_pages
     *
     * @return string
     */
    public function getNextPages()
    {
        $next_pages     = 0;
        $page           = $this->getPage();
        $num_side_pages = $this->getNumSidePages();
        $totalpage      = $this->getTotalPag();

        for ($i=1;$i<$num_side_pages;$i++)
        {
            if($page+$i <= $totalpage) $next_pages++;
        }
        return $next_pages;
    }

    /**
     * Cambia el maximo de resultados que muestra la paginación
     *
     * @return string
     */
    public function changeMaxRows($page, $rows)
    {
        $this->setMaxRows($rows);
        $this->setLastRow($this->getMaxRows() * $page);
        $this->setFirstRow($this->getLastRow() - $this->getMaxRows());

        return $this;
    }

    /**
     * Obtiene de manera modular los resultados de una query a través de los valores que se le pasan por parametro.
     *
     * @param  EntityManager $em
     *
     * @param  String $bundle     - El bundle principal de la consulta
     *                              (ej. TicketBundle)
     *
     * @param  String $entity     - La entidad principal de la consulta
     *                              (ej. Ticket)
     *
     * @param  Array  $params     - Parametros de la consulta (WHERE / AND)
     *                              (ej. $params[] = array("id", " = 1"); =Se_traduce_en=> " AND e.id = 1")
     *
     * @param  Entity $pagination - La entidad Pagination que estas utilizando
     *
     * @param  Array  $ordered    - Parametros de la ordenacion (ORDER BY)
     *                              (ej. $ordered = array('e.id', 'DESC'); =Se_traduce_en=> " ORDER BY e.id DESC")
     *
     * @param  Array  $joins      - Campos en los que se hace JOIN a una entidad con FK definida y parametros relacionados
     *                            - Si el array es de tres parametros pasará a ser un "JOIN ON" a una entidad con FK no definida en Doctrine
     *                              (ej. $joins[] = array('e.workshop w ', 'w.country != 0'); =Se_traduce_en=> " JOIN w.workshop w...AND w.country != 0" )
     *
     * @param  String $add        - Campos a mostrar que no pertenecen a la entidad principal de la consulta
     *                              (ej. $add=', w.name as name'; =Se_traduce_en=> "SELECT e, w.name as name FROM..."  )
     *
     * @param  String $group_by   - Campos a agrupar en la consulta (GROUP BY)
     *                              (ej. $group_by="w.name"; =Se_traduce_en=> "GROUP BY w.name")
     *
     * @return Array              - Devuelve un array con el resultado de la consulta
     *
     */
    public function getRows($em, $bundle, $entity, $params=null, $pagination=null, $ordered=null, $joins=null, $add='', $group_by=null)
    {
        $query = 'SELECT e '.$add;
        $from  = 'FROM '.$bundle.':'.$entity.' e ';
        $where = 'WHERE e.id >= 0 ';

        if($joins != null and $joins[0] != null) {
            foreach ($joins as $join) {
                                        if(isset($join[2])){
                                            $from  = $from.$join[2].' '.'JOIN '.$join[0].' ON '.$join[1].' ';
                                        }
                                        else{
                                            $from  = $from.'JOIN '.$join[0].' ';
                                            $where = $where.'AND '.$join[1].' ';
                                        }
                                    }
        }
        if($params != null and $params[0] != null) {
            foreach ($params as $param) {
                if($param != null)
                    $where = $where.'AND e.'.$param[0].' '.$param[1].' ';
            }
        }

        ($group_by != null) ? $group_by = 'GROUP BY '.$group_by.' ' : $group_by = '';

        if ($ordered != null) {
            if (is_array($ordered)) $order = 'ORDER BY '.$ordered[0].' '.$ordered[1].' ';
            else                    $order = 'ORDER BY e.modified_at '.$ordered.' ';
        }
        else{
             $order = '';
        }

        if($pagination != null){
            $consulta = $em ->createQuery($query.$from.$where.$group_by.$order)
                            ->setMaxResults($pagination->getMaxRows())
                            ->setFirstResult($pagination->getFirstRow());
        }else{
            $consulta = $em->createQuery($query.$from.$where.$group_by.$order);
        }

        /* PARA DEBUG */
            // echo $query.$from.$where.$group_by.$order.'<br>';
            //
            // var_dump($consulta->getResult());
            //
            // foreach ($consulta->getResult() as $key) {
            //     var_dump($key->getId());
            // }
            // die;
        return $consulta->getResult();
    }

    public function getRowsLength($em, $bundle, $entity, $params=null, $ordered=null, $joins=null, $add='', $group_by=null)
    {
        $query = 'SELECT COUNT(e) '.$add;
        $from  = 'FROM '.$bundle.':'.$entity.' e ';
        $where = 'WHERE e.id > 0 ';

        if($joins != null and $joins[0] != null) {
            foreach ($joins as $join) {
                                        if(isset($join[2])){
                                            $from  = $from.$join[2].' '.'JOIN '.$join[0].' ON '.$join[1].' ';
                                        }
                                        else{
                                            $from  = $from.'JOIN '.$join[0].' ';
                                            $where = $where.'AND '.$join[1].' ';
                                        }
                                    }
        }
        if($params != null and $params[0] != null) {
            foreach ($params as $param) {
                if($param != null)
                    $where = $where.'AND e.'.$param[0].' '.$param[1].' ';
            }
        }

        ($group_by != null) ? $group_by = 'GROUP BY '.$group_by.' ' : $group_by = '';

        if ($ordered != null) {
            if (is_array($ordered)) $order = 'ORDER BY '.$ordered[0].' '.$ordered[1].' ';
            else                    $order = 'ORDER BY e.modified_at '.$ordered.' ';
        }
        else{
             $order = '';
        }

        $consulta = $em ->createQuery($query.$from.$where.$group_by.$order);

        /* PRUEBAS */
            // echo $query.$from.$where.$group_by.$order.'<br>';
            // var_dump($consulta->getResult());
            // die;

        $result = $consulta->getResult();

        if(isset($result[0])) {
            $result = $result[0];
            $result = $result[1];
        }else{
            $result = 0;
        }

        return $result;
    }

    // public function getRowsWithQuery($em, $query, $page, $pagination=null)
    // {
    //     if($pagination != null){

    //         $consulta = $em ->createQuery($query)
    //                         ->setMaxResults($pagination->getMaxRows())
    //                         ->setFirstResult($pagination->getFirstRow());
    //     }else{
    //         $consulta = $em->createQuery($query);
    //     }

    //     return $consulta->getResult();
    // }

    // public function getRowsLengthWithQuery($em, $query)
    // {
    //     $consulta = $em ->createQuery($query);

    //     $result = $consulta->getResult();
    //     $result = $result[0];
    //     $result = $result[1];

    //     return $result;
    // }
}
