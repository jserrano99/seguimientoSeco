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
 * @ORM\Table(name="usuario_remedy")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsuarioRemedyRepository")
 *
 */
class UsuarioRemedy
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
	 * @ORM\Column(name="login", type="string",length=25,nullable=false)
	 */
	private $login;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="apellidos", type="string", length=255, nullable=false)
	 */
	private $apellidos;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="nombre", type="string",length=255, nullable=true)
	 */
	private $nombre;


	/**
	 * @var Centro
	 *
	 * @ORM\ManyToOne(targetEntity="Centro")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="centro_id", referencedColumnName="id")
	 * })
	 */
	private $centro;


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
	 * Set login.
	 *
	 * @param string $login
	 *
	 * @return UsuarioRemedy
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
	 * Set apellidos.
	 *
	 * @param string $apellidos
	 *
	 * @return UsuarioRemedy
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
	 * @param string|null $nombre
	 *
	 * @return UsuarioRemedy
	 */
	public function setNombre($nombre = null)
	{
		$this->nombre = $nombre;

		return $this;
	}

	/**
	 * Get nombre.
	 *
	 * @return string|null
	 */
	public function getNombre()
	{
		return $this->nombre;
	}

	/**
	 * Set centro.
	 *
	 * @param \AppBundle\Entity\Centro|null $centro
	 *
	 * @return UsuarioRemedy
	 */
	public function setCentro(\AppBundle\Entity\Centro $centro = null)
	{
		$this->centro = $centro;

		return $this;
	}

	/**
	 * Get centro.
	 *
	 * @return \AppBundle\Entity\Centro|null
	 */
	public function getCentro()
	{
		return $this->centro;
	}

	public function __toString()
	{
		return $this->login . " " . $this->apellidos . ", " . $this->nombre;

	}
}
