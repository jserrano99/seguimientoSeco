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
 * @ORM\Table(name="remedy")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RemedyRepository")
 *
 */

class Remedy
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
	 * @var string
	 *
	 * @ORM\Column(name="tipo", type="string",length=40,nullable=false)
	 */
	private $tipo;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="numero", type="string",length=40,nullable=false)
	 */
	private $numero;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="estado", type="string",length=40,nullable=false)
	 */
	private $estado;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="fecha_entrada", type="datetime",nullable=false)
	 */
	private $fechaEntrada;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_modificacion", type="datetime",nullable=false)
     */
    private $fechaModificacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cierre", type="datetime",nullable=false)
     */
    private $fechaCierre;


    /**
	 * @var string
	 *
	 * @ORM\Column(name="criticidad", type="string",length=40,nullable=false)
	 */
	private $criticidad;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="area", type="string",length=255,nullable=false)
	 */
	private $area;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="apellidos", type="string",length=255,nullable=false)
	 */
	private $apellidos;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nombre", type="string",length=255,nullable=false)
	 */
	private $nombre;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="producto_nivel3", type="string",length=255,nullable=false)
	 */
	private $productoNivel3;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="producto_nivel4", type="string",length=255,nullable=false)
	 */
	private $productoNivel4;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="login", type="string",length=255,nullable=false)
	 */
	private $login;

	/**
	 * @var text
	 *
	 * @ORM\Column(name="descripcion_problema", type="text",nullable=false)
	 */
	private $descripcionProblema;

	/**
	 * @var \AppBundle\Entity\Centro|null
	 * @ORM\ManyToOne(targetEntity="Centro")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="centro_id", referencedColumnName="id")
	 * })
	 */
	private $centro;

	/**
	 * @var \AppBundle\Entity\Aplicacion|null
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Aplicacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="aplicacion_id", referencedColumnName="id")
	 * })
	 */
	private $aplicacion;

    /**
     * @var \AppBundle\Entity\UsuarioRemedy|null
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UsuarioRemedy")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_remedy_id", referencedColumnName="id")
     * })
     */
    private $usuarioRemedy;

    /**
     * @var \AppBundle\Entity\Mes|null
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mes")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mes_id", referencedColumnName="id")
     * })
     */
    private $mes;


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
     * Set tipo.
     *
     * @param string $tipo
     *
     * @return Remedy
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo.
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set numero.
     *
     * @param string $numero
     *
     * @return Remedy
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set estado.
     *
     * @param string $estado
     *
     * @return Remedy
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado.
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set fechaEntrada.
     *
     * @param \DateTime $fechaEntrada
     *
     * @return Remedy
     */
    public function setFechaEntrada($fechaEntrada)
    {
        $this->fechaEntrada = $fechaEntrada;

        return $this;
    }

    /**
     * Get fechaEntrada.
     *
     * @return \DateTime
     */
    public function getFechaEntrada()
    {
        return $this->fechaEntrada;
    }

    /**
     * Set criticidad.
     *
     * @param string $criticidad
     *
     * @return Remedy
     */
    public function setCriticidad($criticidad)
    {
        $this->criticidad = $criticidad;

        return $this;
    }

    /**
     * Get criticidad.
     *
     * @return string
     */
    public function getCriticidad()
    {
        return $this->criticidad;
    }

    /**
     * Set area.
     *
     * @param string $area
     *
     * @return Remedy
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area.
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set apellidos.
     *
     * @param string $apellidos
     *
     * @return Remedy
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos.
     *
     * @return string
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Remedy
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set productoNivel3.
     *
     * @param string $productoNivel3
     *
     * @return Remedy
     */
    public function setProductoNivel3($productoNivel3)
    {
        $this->productoNivel3 = $productoNivel3;

        return $this;
    }

    /**
     * Get productoNivel3.
     *
     * @return string
     */
    public function getProductoNivel3()
    {
        return $this->productoNivel3;
    }

    /**
     * Set productoNivel4.
     *
     * @param string $productoNivel4
     *
     * @return Remedy
     */
    public function setProductoNivel4($productoNivel4)
    {
        $this->productoNivel4 = $productoNivel4;

        return $this;
    }

    /**
     * Get productoNivel4.
     *
     * @return string
     */
    public function getProductoNivel4()
    {
        return $this->productoNivel4;
    }

    /**
     * Set login.
     *
     * @param string $login
     *
     * @return Remedy
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set descripcionProblema.
     *
     * @param string $descripcionProblema
     *
     * @return Remedy
     */
    public function setDescripcionProblema($descripcionProblema)
    {
        $this->descripcionProblema = $descripcionProblema;

        return $this;
    }

    /**
     * Get descripcionProblema.
     *
     * @return string
     */
    public function getDescripcionProblema()
    {
        return $this->descripcionProblema;
    }

    /**
     * Set centro.
     *
     * @param \AppBundle\Entity\centro|null $centro
     *
     * @return Remedy
     */
    public function setCentro(\AppBundle\Entity\centro $centro = null)
    {
        $this->centro = $centro;

        return $this;
    }

    /**
     * Get centro.
     *
     * @return \AppBundle\Entity\centro|null
     */
    public function getCentro()
    {
        return $this->centro;
    }

    /**
     * Set aplicacion.
     *
     * @param \AppBundle\Entity\Aplicacion|null $aplicacion
     *
     * @return Remedy
     */
    public function setAplicacion(\AppBundle\Entity\Aplicacion $aplicacion = null)
    {
        $this->aplicacion = $aplicacion;

        return $this;
    }

    /**
     * Get aplicacion.
     *
     * @return \AppBundle\Entity\Aplicacion|null
     */
    public function getAplicacion()
    {
        return $this->aplicacion;
    }

    /**
     * Set fechaModificacion.
     *
     * @param \DateTime $fechaModificacion
     *
     * @return Remedy
     */
    public function setFechaModificacion($fechaModificacion)
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

    /**
     * Get fechaModificacion.
     *
     * @return \DateTime
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * Set fechaCierre.
     *
     * @param \DateTime $fechaCierre
     *
     * @return Remedy
     */
    public function setFechaCierre($fechaCierre)
    {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }

    /**
     * Get fechaCierre.
     *
     * @return \DateTime
     */
    public function getFechaCierre()
    {
        return $this->fechaCierre;
    }

    /**
     * Set usuarioRemedy.
     *
     * @param \AppBundle\Entity\UsuarioRemedy|null $usuarioRemedy
     *
     * @return Remedy
     */
    public function setUsuarioRemedy(\AppBundle\Entity\UsuarioRemedy $usuarioRemedy = null)
    {
        $this->usuarioRemedy = $usuarioRemedy;

        return $this;
    }

    /**
     * Get usuarioRemedy.
     *
     * @return \AppBundle\Entity\UsuarioRemedy|null
     */
    public function getUsuarioRemedy()
    {
        return $this->usuarioRemedy;
    }

    /**
     * Set mes.
     *
     * @param \AppBundle\Entity\Mes|null $mes
     *
     * @return Remedy
     */
    public function setMes(\AppBundle\Entity\Mes $mes = null)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes.
     *
     * @return \AppBundle\Entity\Mes|null
     */
    public function getMes()
    {
        return $this->mes;
    }
}
