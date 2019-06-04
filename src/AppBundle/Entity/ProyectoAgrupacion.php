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
 * @ORM\Table(name="proyecto_agrupacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProyectoAgrupacionRepository")
 *
 */

class ProyectoAgrupacion
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
	 * @var Proyecto\null
	 *
	 * @ORM\ManyToOne(targetEntity="Proyecto")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="proyecto_id", referencedColumnName="id")
	 * })
	 */
	private $proyecto;

	/**
	 * @var Agrupacion\null
	 *
	 * @ORM\ManyToOne(targetEntity="Agrupacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="agrupacion_id", referencedColumnName="id")
	 * })
	 */
	private $agrupacion;


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
     * Set proyecto.
     *
     * @param \AppBundle\Entity\Proyecto|null $proyecto
     *
     * @return ProyectoAgrupacion
     */
    public function setProyecto(\AppBundle\Entity\Proyecto $proyecto = null)
    {
        $this->proyecto = $proyecto;

        return $this;
    }

    /**
     * Get proyecto.
     *
     * @return \AppBundle\Entity\Proyecto|null
     */
    public function getProyecto()
    {
        return $this->proyecto;
    }

    /**
     * Set agrupacion.
     *
     * @param \AppBundle\Entity\Agrupacion|null $agrupacion
     *
     * @return ProyectoAgrupacion
     */
    public function setAgrupacion(\AppBundle\Entity\Agrupacion $agrupacion = null)
    {
        $this->agrupacion = $agrupacion;

        return $this;
    }

    /**
     * Get agrupacion.
     *
     * @return \AppBundle\Entity\Agrupacion|null
     */
    public function getAgrupacion()
    {
        return $this->agrupacion;
    }
}
