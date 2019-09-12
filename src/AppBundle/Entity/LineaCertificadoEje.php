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
 * @ORM\Table(name="linea_certificado_eje")
 * @ORM\Entity()
 *
 */

class LineaCertificadoEje
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
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return CertificadoServicios\null
	 */
	public function getCertificadoServicios()
	{
		return $this->certificadoServicios;
	}

	/**
	 * @param CertificadoServicios\null $certificadoServicios
	 */
	public function setCertificadoServicios($certificadoServicios)
	{
		$this->certificadoServicios = $certificadoServicios;
	}

	/**
	 * @return Encargo\null
	 */
	public function getEncargo()
	{
		return $this->encargo;
	}

	/**
	 * @param Encargo\null $encargo
	 */
	public function setEncargo($encargo)
	{
		$this->encargo = $encargo;
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
