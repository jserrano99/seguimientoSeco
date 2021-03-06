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
 * @ORM\Table(name="linea_certificado")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LineaCertificadoRepository")
 *
 */

class LineaCertificado
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
	 * @var CertificadoServicios\null
	 *
	 * @ORM\ManyToOne(targetEntity="CertificadoServicios")
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
	 * @var TipoCuota\null
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoCuota")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_cuota_id", referencedColumnName="id")
	 * })
	 */
	private $tipoCuota;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="penalizacion", type="float", nullable=true)
	 */
	private $penalizacion;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="dias_totales", type="integer", nullable=true)
	 */
	private $diasTotales;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="dias_mes", type="integer", nullable=true)
	 */
	private $diasMes;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="horas_dia", type="float", nullable=true)
	 */
	private $horasDia;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="horas_mes", type="float", nullable=true)
	 */
	private $horasMes;


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
     * Set penalizacion.
     *
     * @param float|null $penalizacion
     *
     * @return LineaCertificado
     */
    public function setPenalizacion($penalizacion = null)
    {
        $this->penalizacion = $penalizacion;

        return $this;
    }

    /**
     * Get penalizacion.
     *
     * @return float|null
     */
    public function getPenalizacion()
    {
        return $this->penalizacion;
    }

    /**
     * Set certificadoServicios.
     *
     * @param \AppBundle\Entity\CertificadoServicios|null $certificadoServicios
     *
     * @return LineaCertificado
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
     * @return LineaCertificado
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
     * Set tipoCuota.
     *
     * @param \AppBundle\Entity\TipoCuota|null $tipoCuota
     *
     * @return LineaCertificado
     */
    public function setTipoCuota(\AppBundle\Entity\TipoCuota $tipoCuota = null)
    {
        $this->tipoCuota = $tipoCuota;

        return $this;
    }

    /**
     * Get tipoCuota.
     *
     * @return \AppBundle\Entity\TipoCuota|null
     */
    public function getTipoCuota()
    {
        return $this->tipoCuota;
    }

	/**
	 * @return int
	 */
	public function getDiasTotales()
	{
		return $this->diasTotales;
	}

	/**
	 * @param int $diasTotales
	 */
	public function setDiasTotales($diasTotales)
	{
		$this->diasTotales = $diasTotales;
	}

	/**
	 * @return int
	 */
	public function getDiasMes()
	{
		return $this->diasMes;
	}

	/**
	 * @param int $diasMes
	 */
	public function setDiasMes($diasMes)
	{
		$this->diasMes = $diasMes;
	}

	/**
	 * @return float
	 */
	public function getHorasDia()
	{
		return $this->horasDia;
	}

	/**
	 * @param float $horasDia
	 */
	public function setHorasDia($horasDia)
	{
		$this->horasDia = $horasDia;
	}

	/**
	 * @return float
	 */
	public function getHorasMes()
	{
		return $this->horasMes;
	}

	/**
	 * @param float $horasMes
	 */
	public function setHorasMes($horasMes)
	{
		$this->horasMes = $horasMes;
	}


}
