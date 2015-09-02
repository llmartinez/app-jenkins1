<?php

namespace Adservice\CarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adservice\CarBundle\Entity\Version
 *
 * @ORM\Table(name="Version_Vehiculo")
 * @ORM\Entity
 */
class Version {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="Version", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="Descripcion", type="string", length=255)
     */
    private $name;

    /**
     * @var integer $brand
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Brand")
     * @ORM\JoinColumn(name="marca", referencedColumnName="Marca")
     */
    private $brand;

    /**
     * @var integer $model
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Model")
     * @ORM\JoinColumn(name="modelo", referencedColumnName="Modelo")
     */
    private $model;


    /**
     * @var string $motor
     *
     * @ORM\Column(name="Motor", type="string", length=255)
     */
    private $motor;

    /**
     * @var string $inicio
     *
     * @ORM\Column(name="Inicio", type="string", length=6, nullable=true)
     */
    private $inicio;

    /**
     * @var string $fin
     *
     * @ORM\Column(name="Fin", type="string", length=6, nullable=true)
     */
    private $fin;

    /**
     * @var string $kw
     *
     * @ORM\Column(name="KW", type="string", length=255)
     */
    private $kw;

    /**
     * @var string $kw
     *
     * @ORM\Column(name="CV", type="string", length=255)
     */
    private $cv;

    /**
     * @var string $cm3
     *
     * @ORM\Column(name="cm3", type="string", length=255)
     */
    private $cm3;

    /**
     * @var integer $litros
     * @ORM\Column(name="Litros", type="integer", nullable=true)
     */
    private $litros;

    /**
     * @var integer $cilindros
     * @ORM\Column(name="Cilindros", type="integer", nullable=true)
     */
    private $cilindros;

    /**
     * @var integer $puertas
     * @ORM\Column(name="Puertas", type="integer", nullable=true)
     */
    private $puertas;

    /**
     * @var integer $deposito
     * @ORM\Column(name="Deposito", type="integer", nullable=true)
     */
    private $deposito;

    /**
     * @var integer $voltaje
     * @ORM\Column(name="Voltaje", type="integer", nullable=true)
     */
    private $voltaje;

    /**
     * @var integer $abs
     * @ORM\Column(name="ABS", type="integer", nullable=true)
     */
    private $abs;

    /**
     * @var integer $asr
     * @ORM\Column(name="ASR", type="integer", nullable=true)
     */
    private $asr;

    /**
     * @var integer $tipomotor
     * @ORM\Column(name="Tipo_Motor", type="integer", nullable=true)
     */
    private $tipomotor;

    /**
     * @var integer $tipovehiculo
     * @ORM\Column(name="Tipo_Vehiculo", type="integer", nullable=true)
     */
    private $tipovehiculo;

    /**
     * @var integer $tipotraccion
     * @ORM\Column(name="Tipo_Traccion", type="integer", nullable=true)
     */
    private $tipotraccion;

    /**
     * @var integer $tipofreno
     * @ORM\Column(name="Tipo_Freno", type="integer", nullable=true)
     */
    private $tipofreno;

    /**
     * @var integer $tipofrenado
     * @ORM\Column(name="Tipo_Frenado", type="integer", nullable=true)
     */
    private $tipofrenado;

    /**
     * @var integer $tipocombustible
     * @ORM\Column(name="Tipo_Combustible", type="integer", nullable=true)
     */
    private $tipocombustible;

    /**
     * @var integer $tipocatalizador
     * @ORM\Column(name="Tipo_Catalizador", type="integer", nullable=true)
     */
    private $tipocatalizador;

    /**
     * @var integer $tipotransmision
     * @ORM\Column(name="Tipo_Transmision", type="integer", nullable=true)
     */
    private $tipotransmision;

    /**
     * @var integer $tipoconstruccion
     * @ORM\Column(name="Tipo_Construccion", type="integer", nullable=true)
     */
    private $tipoconstruccion;

    /**
     * @var boolean $planrevision
     * @ORM\Column(name="PlanRevision", type="boolean", nullable=true)
     */
    private $planrevision;

    /**
     * @var boolean $datostecnicos
     * @ORM\Column(name="DatosTecnicos", type="boolean", nullable=true)
     */
    private $datostecnicos;

    /**
     * @var boolean $tiemposreparacion
     * @ORM\Column(name="TiemposReparacion", type="boolean", nullable=true)
     */
    private $tiemposreparacion;

    /**
     * @var boolean $alineacionruedas
     * @ORM\Column(name="AlineacionRuedas", type="boolean", nullable=true)
     */
    private $alineacionruedas;


    public function __toString() {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set marca
     *
     * @ORM\ManyToOne(targetEntity="\Adservice\CarBundle\Entity\Marca")
     */
    public function setMarca($marca)
    {
        $this->marca = $marca;
    }

    /**
     * Get marca
     *
     * @return integer
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set model
     *
     * @param \Adservice\CarBundle\Entity\Model $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Get model
     *
     * @return integer
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set motor
     *
     * @param string $motor
     */
    public function setMotor($motor)
    {
        $this->motor = $motor;
    }

    /**
     * Get motor
     *
     * @return string
     */
    public function getMotor()
    {
        return $this->motor;
    }

    /**
     * Set inicio
     *
     * @param string $inicio
     */
    public function setInicio($inicio)
    {
        $this->inicio = $inicio;
    }

    /**
     * Get inicio
     *
     * @return string
     */
    public function getInicio()
    {
        return $this->inicio;
    }

    /**
     * Set fin
     *
     * @param string $fin
     */
    public function setFin($fin)
    {
        $this->fin = $fin;
    }

    /**
     * Get fin
     *
     * @return string
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Get dateInicio
     *
     * @return string
     */
    public function getDateInicio()
    {
        $inicio = '01-'.substr ($this->inicio, -2).'-'.substr ($this->inicio, 0, -2); //ej. 01-01-2015
        return $this->version;
    }
    /**
     * Get dateFin
     *
     * @return string
     */
    public function getDateFin()
    {
        $fin    = '31-'.substr ($this->fin, -2).'-'.substr ($this->fin, 0, -2); //ej. 31-12-2015
        return $this->version;
    }

    /**
     * Set kw
     *
     * @param string $kw
     */
    public function setKw($kw)
    {
        $this->kw = $kw;
    }

    /**
     * Get kw
     *
     * @return string
     */
    public function getKw()
    {
        return $this->kw;
    }

    public function setCV($cv)
    {
        $this->cv = $cv;
    }

    public function getCV()
    {
        return $this->cv;
    }

    public function setCm3($cm3)
    {
        $this->cm3 = $cm3;
    }

    public function getCm3()
    {
        return $this->cm3;
    }

    public function setLitros($litros)
    {
        $this->litros = $litros;
    }

    public function getLitros()
    {
        return $this->$litros;
    }

    public function setCilindros($cilindros)
    {
        $this->cilindros = $cilindros;
    }

    public function getCilindros()
    {
        return $this->$cilindros;
    }

    public function setPuertas($puertas)
    {
        $this->puertas = $puertas;
    }

    public function getPuertas()
    {
        return $this->$puertas;
    }

    public function setDeposito($deposito)
    {
        $this->deposito = $deposito;
    }

    public function getDeposito()
    {
        return $this->$deposito;
    }

    public function setVoltaje($voltaje)
    {
        $this->voltaje = $voltaje;
    }

    public function getVoltaje()
    {
        return $this->$voltaje;
    }

    public function setABS($abs)
    {
        $this->abs = $abs;
    }

    public function getABS()
    {
        return $this->$abs;
    }

    public function setASR($asr)
    {
        $this->asr = $asr;
    }

    public function getASR()
    {
        return $this->$asr;
    }

    public function setTipoMotor($tipomotor)
    {
        $this->tipomotor = $tipomotor;
    }

    public function getTipoMotor()
    {
        return $this->$tipomotor;
    }

    public function setTipoVehiculo($tipovehiculo)
    {
        $this->tipovehiculo = $tipovehiculo;
    }

    public function getTipoVehiculo()
    {
        return $this->$tipovehiculo;
    }

    public function setTipoTraccion($tipotraccion)
    {
        $this->tipotraccion = $tipotraccion;
    }

    public function getTipoTraccion()
    {
        return $this->$tipotraccion;
    }

    public function setTipoFreno($tipofreno)
    {
        $this->tipofreno = $tipofreno;
    }

    public function getTipoFreno()
    {
        return $this->$tipofreno;
    }

    public function setTipoFrenado($tipofrenado)
    {
        $this->tipofrenado = $tipofrenado;
    }

    public function getTipoFrenado()
    {
        return $this->$tipofrenado;
    }

    public function setTipoCombustible($tipocombustible)
    {
        $this->tipocombustible = $tipocombustible;
    }

    public function getTipoCombustible()
    {
        return $this->$tipocombustible;
    }

    public function setTipoCatalizador($tipocatalizador)
    {
        $this->tipocatalizador = $tipocatalizador;
    }

    public function getTipoCatalizador()
    {
        return $this->$tipocatalizador;
    }

    public function setTipoTransmision($tipotransmision)
    {
        $this->tipotransmision = $tipotransmision;
    }

    public function getTipoTransmision()
    {
        return $this->$tipotransmision;
    }

    public function setTipoConstruccion($tipoconstruccion)
    {
        $this->tipoconstruccion = $tipoconstruccion;
    }

    public function getTipoConstruccion()
    {
        return $this->$tipoconstruccion;
    }

    public function setPlanRevision($planrevision)
    {
        $this->planrevision = $planrevision;
    }

    public function getPlanRevision()
    {
        return $this->$planrevision;
    }

    public function setDatosTecnicos($datostecnicos)
    {
        $this->datostecnicos = $datostecnicos;
    }

    public function getDatosTecnicos()
    {
        return $this->$datostecnicos;
    }

    public function setTiemposReparacion($tiemposreparacion)
    {
        $this->tiemposreparacion = $tiemposreparacion;
    }

    public function getTiemposReparacion()
    {
        return $this->$tiemposreparacion;
    }

    public function setAlineacionRuedas($alineacionruedas)
    {
        $this->alineacionruedas = $alineacionruedas;
    }

    public function getAlineacionRuedas()
    {
        return $this->$alineacionruedas;
    }

    public function to_json(){
        $json = array('id'                  => $this->getId(),
                      'name'                => $this->getName(),
                      'motor'               => $this->getMotor(),
                      'inicio'              => $this->getInicio(),
                      'fin'                 => $this->getFin(),
                      'kw'                  => $this->getKw(),
                      'cv'                  => $this->getCV(),
                      'cm3'                 => $this->getCm3(),
                      'model'               => $this->getModel()->getId(),
                      'brand'               => $this->getModel()->getBrand()->getId());
        return $json;
    }
}