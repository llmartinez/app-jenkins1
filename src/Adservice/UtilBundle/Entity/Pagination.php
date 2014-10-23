<?php
namespace Adservice\UtilBundle\Entity;

/**
 * Genera código para paginar una sentencia DQL
 *
 * Adservice\UtilBundle\Entity\Pagination
 * @author DMF
 */
class Pagination
{
    private $max_rows;       //Maximo de registros por pagina
    private $num_side_pages; //Numero de paginas a cada lado de la pagina actual

    private $page;           //Numero de la pagina actual
    private $first_row;      //Numero del primer registro a mostrar
    private $last_row;       //Numero del ultimo registro a mostrar
    private $total_pag;      //Numero total de paginas
    private $result;         //Resultado de la consulta

    public function __construct($page=1, $em=null, $dql=null) {
        $this->setMaxRows(10);
        $this->setNumSidePages(7);
        $this->setPage($page);
        $this->setLastRow($this->getMaxRows() * $page);
        $this->setFirstRow($this->getLastRow() - $this->getMaxRows()  );

        if($em!=null and $dql!=null) {
            $this->setResult($this->getRows($em, $dql));
            $length = $this->getRowsLength($em, $dql);
            $this->setTotalPagByLength($length);
        }
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
     * Set result
     *
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set total_pag
     *
     * @param string $length
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
     * Get rows
     *
     * @return string
     */
    public function getRows($em, $query)
    {
        $consulta = $em ->createQuery($query)
                        ->setMaxResults($this->getMaxRows())
                        ->setFirstResult($this->getFirstRow());

        /* PRUEBAS */
            // echo $query.'<br>';
            // var_dump($consulta->getResult());
            // die;
        $this->result = $consulta->getResult();
        return $consulta->getResult();
    }

    public function getRowsLength($em, $query)
    {
        $consulta = $em ->createQuery($query);

        $result = $consulta->getResult();
        $count  = count($result);

        return $count;
    }

}
