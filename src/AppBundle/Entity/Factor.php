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
 * @ORM\Table(name="factores")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FactorRepository")
 *
 */

class Factor
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
	 * @var float
	 * @ORM\Column(name="corrector", type="float", nullable=true)*
	 *
	 */
	private  $corrector;

	/**
	 * @var float
	 * @ORM\Column(name="valor_inicial", type="float", nullable=true)*
	 *
	 */
	private $valorInicial;

	/**
	 * @var float
	 * @ORM\Column(name="valor_final", type="float", nullable=true)*
	 *
	 */
	private $valorFinal;




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
     * Set corrector.
     *
     * @param float|null $corrector
     *
     * @return Factor
     */
    public function setCorrector($corrector = null)
    {
        $this->corrector = $corrector;

        return $this;
    }

    /**
     * Get corrector.
     *
     * @return float|null
     */
    public function getCorrector()
    {
        return $this->corrector;
    }

    /**
     * Set valorInicial.
     *
     * @param float|null $valorInicial
     *
     * @return Factor
     */
    public function setValorInicial($valorInicial = null)
    {
        $this->valorInicial = $valorInicial;

        return $this;
    }

    /**
     * Get valorInicial.
     *
     * @return float|null
     */
    public function getValorInicial()
    {
        return $this->valorInicial;
    }

    /**
     * Set valorFinal.
     *
     * @param float|null $valorFinal
     *
     * @return Factor
     */
    public function setValorFinal($valorFinal = null)
    {
        $this->valorFinal = $valorFinal;

        return $this;
    }

    /**
     * Get valorFinal.
     *
     * @return float|null
     */
    public function getValorFinal()
    {
        return $this->valorFinal;
    }

    /**
     * Set indicador.
     *
     * @param \AppBundle\Entity\Indicador|null $indicador
     *
     * @return Factor
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
}
