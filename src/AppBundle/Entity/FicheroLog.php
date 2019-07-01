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
     * @ORM\Column(name="fichero_log", type="string", length= 255, nullable=true)
     */
    private $fichero;


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
     * Set fichero.
     *
     * @param string|null $fichero
     *
     * @return FicheroLog
     */
    public function setFichero($fichero = null)
    {
        $this->fichero = $fichero;

        return $this;
    }

    /**
     * Get fichero.
     *
     * @return string|null
     */
    public function getFichero()
    {
        return $this->fichero;
    }
}
