<?php

/**
 * Description of FicheroLog
 *
 * @author jluis
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FicheroLog
 *
 * @ORM\Table(name="fichero_log")
 * 
 * @ORM\Entity()
 */
class FicheroLog {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_proceso", type="datetime",  nullable=false)
     */
    private $fechaProceso;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_fichero", type="string", length= 255, nullable=true)
     */
    private $nombreFichero;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaProceso.
     *
     * @param \DateTime $fechaProceso
     *
     * @return FicheroLog
     */
    public function setFechaProceso($fechaProceso)
    {
        $this->fechaProceso = $fechaProceso;

        return $this;
    }

    /**
     * Get fechaProceso.
     *
     * @return \DateTime
     */
    public function getFechaProceso()
    {
        return $this->fechaProceso;
    }

    /**
     * Set nombreFichero.
     *
     * @param string|null $nombreFichero
     *
     * @return FicheroLog
     */
    public function setNombreFichero($nombreFichero = null)
    {
        $this->nombreFichero = $nombreFichero;

        return $this;
    }

    /**
     * Get nombreFichero.
     *
     * @return string|null
     */
    public function getNombreFichero()
    {
        return $this->nombreFichero;
    }
}
