<?php

namespace AppBundle\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Acceso;
use AppBundle\Entity\Usuario;

/**
 * Class LoginController
 *
 * @package AppBundle\Controller
 */
class LoginController extends Controller {
	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
    private $sesion;

	/**
	 * LoginController constructor.
	 */
    public function __construct() {
        $this->sesion = new Session();
    }

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(Request $request) {
	    $authenticationUtils = $this->get('security.authentication_utils');
	    $error = $authenticationUtils->getLastAuthenticationError();
	    $lastUsername = $authenticationUtils->getLastUsername();

	    $params=	['last_username' => $lastUsername,
		    'error' => $error];

	    return $this->render('login/login.html.twig',$params);
    }

	/**
	 * @param $Usuario
	 * @return array|null
	 */
	public function ultimoAcceso($Usuario) {
        $em = $this->getDoctrine()->getManager();
        $Acceso_repo = $em->getRepository("AppBundle:Acceso");
        $AccesoAll = $AccesoAll = $Acceso_repo->createQueryBuilder('u')
                        ->where('u.usuario = :usuario')
                        ->setParameter('usuario', $Usuario)
                        ->addOrderBy('u.id', 'desc')
                        ->getQuery()->getResult();
        if ($AccesoAll) {
            return $AccesoAll[0];
        } else {
            return null;
        }
    }

    /**
     * 
     * @param type $Usuario
     * @return \AppBundle\Controller\Acceso
     */
    public function creaAcceso($Usuario) {
        $em = $this->getDoctrine()->getManager();
        $Acceso = new Acceso();
        $Acceso->setUsuario($Usuario);
        $Acceso->setIp($_SERVER['REMOTE_ADDR']);
        $em->persist($Acceso);
        $em->flush();
        return $Acceso;
    }

    /**
     * 
     * @param type $username
     * @return type
     */
    public function verUsuario($username) {
        $em = $this->getDoctrine()->getManager();
        $Usuario_repo = $em->getRepository("AppBundle:Usuario");
        $UsuarioAll = $Usuario_repo->createQueryBuilder('u')
                        ->where('u.codigo = :codigo')
                        ->setParameter('codigo', $username)
                        ->getQuery()->getResult();
        if ($UsuarioAll) {
            return $UsuarioAll[0];
        } else {
            return null;
        }
    }

	/**
	 * @param $LdapAll
	 * @param $username
	 * @return \AppBundle\Entity\Usuario
	 * @throws \Exception
	 */
    public function creaUsuario($LdapAll, $username) {
        $EM = $this->getDoctrine()->getManager();
        $EstadoUsuario_repo = $EM->getRepository("AppBundle:EstadoUsuario");
        $EstadoUsuario = $EstadoUsuario_repo->find(1);
		$Usuario = new Usuario();
        $Usuario->setCodigo($username);
        $fecha = new DateTime();
        $fecha->setDate(date('Y'), date('m'), date('d'));
        $Usuario->setFcAlta($fecha);
        
        $Usuario->setNombre($LdapAll['displayname'][0]);
        $Usuario->setEmail($LdapAll['mail'][0]);
        $Usuario->setPerfil("ROLE_USER");
        $Usuario->setEstadoUsuario($EstadoUsuario);

        $EM->persist($Usuario);
        $EM->flush();
        return $Usuario;
    }
    /**
     * 
     * @param type $username
     * @param type $password
     * @return type
     */

    public function usuarioAutenticadoLdap($username, $password) {
        $autenticado = false;
        $ldaprdn = $username;
        $ldappass = $password;
//$attributes_ad = array("displayName","description","cn","givenName","sn","mail","co","mobile","company","displayName");
        $attributes_ad = array("displayName", "mail");
        $servidor = "salud.madrid.org";
        $dn = "OU=CSCM,DC=salud,DC=madrid,DC=org";
        $ldapconn = ldap_connect($servidor)
                or die("No se puede conectar con el servidor LDAP.");
        ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
        if ($ldapconn && trim($ldappass) != "") {
            $autenticado = @ldap_bind($ldapconn, $ldaprdn . "@" . $servidor, $ldappass);
            if ($autenticado) {
                $result = ldap_search($ldapconn, $dn, "(samaccountname=$username)", $attributes_ad);
                $entries = ldap_get_entries($ldapconn, $result);
                return $entries[0];
            }
        }

        return $autenticado;
    }
}
