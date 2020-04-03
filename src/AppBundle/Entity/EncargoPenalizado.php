<?php
/**
 * Created by PhpStorm.
 * User: jluis_local
 * Date: 10/04/2019
 * Time: 17:20
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Table(name="encargo_penalizado")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EncargoPenalizadoRepository")
 *
 */
class EncargoPenalizado
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var Indicador\null
     *
     * @ORM\ManyToOne(targetEntity="Indicador")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indicador_id", referencedColumnName="id")
     * })
     */
    private $indicador;

    /**
     * @var CertificadoServicios\null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CertificadoServicios")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="certificado_servicios_id", referencedColumnName="id")
     * })
     */
    private $certificadoServicios;

    /**
     * @var Encargo\null
     *
     * @ORM\ManyToOne(targetEntity="Encargo")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="encargo_id", referencedColumnName="id")
     * })
     */
    private $encargo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="eliminada", type="boolean", nullable=true)*
     */

    private $eliminada;

    /**
     * @var integer
     *
     * @ORM\Column(name="dias_retraso_valoracion", type="integer", nullable=true)*
     */

    private $diasRetrasoValoracion;

    /**
     * @var integer
     *
     * @ORM\Column(name="dias_retraso_entrega", type="integer", nullable=true)*
     */

    private $diasRetrasoEntrega;

    /**
     * @var integer
     *
     * @ORM\Column(name="dias_ejecucion", type="integer", nullable=true)*
     */

    private $diasEjecucion;

    /**
     * @var integer
     *
     * @ORM\Column(name="dias_previstos_ejecucion", type="integer", nullable=true)*
     */

    private $diasPrevistosEjecucion;

    /**
     * @var float
     *
     * @ORM\Column(name="importe_penalizacion", type="float", nullable=true)*
     */

    private $importePenalizacion;


    /**
     * @var text
     *
     * @ORM\Column(name="justificacion_eliminacion", type="text", nullable=true)*
     */

    private $justificacionEliminacion;

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
     * Set indicador.
     *
     * @param \AppBundle\Entity\Indicador|null $indicador
     *
     * @return EncargoPenalizado
     */
    public function setIndicador(\AppBundle\Entity\Indicador $indicador = null)
    {
        $this->indicador = $indicador;

        return $this;
    }

    /**
     * Get indicador.
     *
     * @return \AppBundle\Entity\Indicador|null
     */
    public function getIndicador()
    {
        return $this->indicador;
    }

    /**
     * Set certificadoServicios.
     *
     * @param \AppBundle\Entity\CertificadoServicios|null $certificadoServicios
     *
     * @return EncargoPenalizado
     */
    public function setCertificadoServicios(\AppBundle\Entity\CertificadoServicios $certificadoServicios = null)
    {
        $this->certificadoServicios = $certificadoServicios;

        return $this;
    }

    /**
     * Get certificadoServicios.
     *
     * @return \AppBundle\Entity\CertificadoServicios|null
     */
    public function getCertificadoServicios()
    {
        return $this->certificadoServicios;
    }

    /**
     * Set encargo.
     *
     * @param \AppBundle\Entity\Encargo|null $encargo
     *
     * @return EncargoPenalizado
     */
    public function setEncargo(\AppBundle\Entity\Encargo $encargo = null)
    {
        $this->encargo = $encargo;

        return $this;
    }

    /**
     * Get encargo.
     *
     * @return \AppBundle\Entity\Encargo|null
     */
    public function getEncargo()
    {
        return $this->encargo;
    }

    /**
     * @return bool
     */
    public function isEliminada()
    {
        return $this->eliminada;
    }

    /**
     * @param bool $eliminada
     */
    public function setEliminada($eliminada)
    {
        $this->eliminada = $eliminada;
    }

    /**
     * @return int
     */
    public function getDiasRetrasoValoracion()
    {
        return $this->diasRetrasoValoracion;
    }

    /**
     * @param int $diasRetrasoValoracion
     */
    public function setDiasRetrasoValoracion($diasRetrasoValoracion)
    {
        $this->diasRetrasoValoracion = $diasRetrasoValoracion;
    }

    /**
     * @return int
     */
    public function getDiasRetrasoEntrega()
    {
        return $this->diasRetrasoEntrega;
    }

    /**
     * @param int $diasRetrasoEntrega
     */
    public function setDiasRetrasoEntrega($diasRetrasoEntrega)
    {
        $this->diasRetrasoEntrega = $diasRetrasoEntrega;
    }

    /**
     * @return int
     */
    public function getDiasEjecucion()
    {
        return $this->diasEjecucion;
    }

    /**
     * @param int $diasEjecucion
     */
    public function setDiasEjecucion($diasEjecucion)
    {
        $this->diasEjecucion = $diasEjecucion;
    }

    /**
     * @return int
     */
    public function getDiasPrevistosEjecucion()
    {
        return $this->diasPrevistosEjecucion;
    }

    /**
     * @param int $diasPrevistosEjecucion
     */
    public function setDiasPrevistosEjecucion($diasPrevistosEjecucion)
    {
        $this->diasPrevistosEjecucion = $diasPrevistosEjecucion;
    }

    /**
     * @return text
     */
    public function getJustificacionEliminacion()
    {
        return $this->justificacionEliminacion;
    }

    /**
     * @param text $justificacionEliminacion
     */
    public function setJustificacionEliminacion($justificacionEliminacion)
    {
        $this->justificacionEliminacion = $justificacionEliminacion;
    }

    /**
     * @return float
     */
    public function getImportePenalizacion()
    {
        return $this->importePenalizacion;
    }

    /**
     * @param float $importePenalizacion
     */
    public function setImportePenalizacion($importePenalizacion)
    {
        $this->importePenalizacion = $importePenalizacion;
    }


}
