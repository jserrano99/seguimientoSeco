<?php

/**
 * Description of CargaFicheroLog
 *
 * @author jluis
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CargaFicheroLog
 *
 * @ORM\Table(name="carga_fichero_log")
 * 
 * @ORM\Entity()
 */
class CargaFicheroLog {

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
    private $ficheroLog;


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
     * @return CargaFicheroLog
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
     * Set ficheroLog.
     *
     * @param string|null $ficheroLog
     *
     * @return CargaFicheroLog
     */
    public function setFicheroLog($ficheroLog = null)
    {
        $this->ficheroLog = $ficheroLog;

        return $this;
    }

    /**
     * Get ficheroLog.
     *
     * @return string|null
     */
    public function getFicheroLog()
    {
        return $this->ficheroLog;
    }
}
