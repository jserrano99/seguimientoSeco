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
}
