<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Filtro;
use AppBundle\Entity\Report;
use AppBundle\Form\FiltroType;
use AppBundle\Form\ReportType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ReportController
 *
 * @package AppBundle\Controller
 */
class ReportController extends Controller
{
	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $sesion;

	/**
	 * ReportController constructor.
	 */
	public function __construct()
	{
		$this->sesion = new Session();
	}

	public function filtroAction()
	{

		$Filtro = new Filtro();
		$filtroForm = $this->createForm(FiltroType::class, $Filtro);
		return $this->render("informe/edit.html.twig", array(
			"form" => $filtroForm->createView()
		));


	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 */
	public function esfuerzoPLAExtAction(Request $request)
	{
		$Filtro = new Filtro();
		$filtroForm = $this->createForm(FiltroType::class, $Filtro);
		$filtroForm->handleRequest($request);
		if ($filtroForm->isSubmitted()) {
			$format = "pdf";
			$parametros = ["FC_INICIO" => $Filtro->getMes()->getFechaInicio()->format('d/m/Y H:i:s'),
				"FC_FIN" => $Filtro->getMes()->getFechaFin()->format('d/m/Y H:i:s')];
			$reportUnit = "/reports/esfuerzoPLAExtDetalle";
			return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
		}
		return $this->render("informe/edit.html.twig", array(
			"form" => $filtroForm->createView()
		));

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 */
	public function esfuerzoPLAAction(Request $request)
	{
		$Filtro = new Filtro();
		$filtroForm = $this->createForm(FiltroType::class, $Filtro);
		$filtroForm->handleRequest($request);
		if ($filtroForm->isSubmitted()) {
			$format = "pdf";
			$parametros = ["FC_INICIO" => $Filtro->getMes()->getFechaInicio()->format('d/m/Y H:i:s'),
				"FC_FIN" => $Filtro->getMes()->getFechaFin()->format('d/m/Y H:i:s')];
			$reportUnit = "/reports/esfuerzoEnPLADetalle";
			return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
		}
		return $this->render("informe/edit.html.twig", array(
			"form" => $filtroForm->createView()
		));

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function incidentesPorCentroAction(Request $request)
	{
		$Filtro = new Filtro();
		$filtroForm = $this->createForm(FiltroType::class, $Filtro);
		$filtroForm->handleRequest($request);
		if ($filtroForm->isSubmitted()) {
			$format = "pdf";
			$parametros = ["fechaInicio" => $Filtro->getMes()->getFechaInicio()->format('d/m/Y H:i:s'),
				"fechaFin" => $Filtro->getMes()->getFechaFin()->format('d/m/Y H:i:s'),
				"centroId" => $Filtro->getCentro()->getId()];
			$reportUnit = "/reports/incidentesPorCentro";
			return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
		}
		return $this->render("informe/edit.html.twig", array(
			"form" => $filtroForm->createView()
		));

	}


	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function seguimientoNPLAction(Request $request)
	{
		$Filtro = new Filtro();
		$filtroForm = $this->createForm(FiltroType::class, $Filtro);
		$filtroForm->handleRequest($request);
		if ($filtroForm->isSubmitted()) {
			$format = "pdf";
			$parametros = ["fechaInicio" => $Filtro->getMes()->getFechaInicio()->format('d/m/Y H:i:s'),
				"fechaFin" => $Filtro->getMes()->getFechaFin()->format('d/m/Y H:i:s')];
			$reportUnit = "/reports/seguimientoNPL";
//			dump($parametros);
//			die();
			return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
		}
		return $this->render("informe/edit.html.twig", array(
			"form" => $filtroForm->createView()
		));

	}


	public function NPLFueraANSAction(Request $request)
	{
		$Filtro = new Filtro();
		$filtroForm = $this->createForm(FiltroType::class, $Filtro);
		$filtroForm->handleRequest($request);
		if ($filtroForm->isSubmitted()) {
			$format = "pdf";
			$parametros = ["FC_INICIO" => $Filtro->getMes()->getFechaInicio()->format('d/m/Y H:i:s'),
				"FC_FIN" => $Filtro->getMes()->getFechaFin()->format('d/m/Y H:i:s')];
			$reportUnit = "/reports/tiempoResolucionFueraANS2";
			return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
		}
		return $this->render("informe/edit.html.twig", array(
			"form" => $filtroForm->createView()
		));
	}

	/**
	 * @param $certificado_id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function imprimirCertificadoServiciosAction($id)
	{
		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

		IF ($CertificadoServicios->getEstadoCertificado()->getId() != 3) { // ESTADO CERRADO
			$EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(2);
			$CertificadoServicios->setEstadoCertificado($EstadoCertificado);
			$this->getDoctrine()->getManager()->persist($CertificadoServicios);
			$this->getDoctrine()->getManager()->flush();
		}

		$format = "pdf";
		$parametros = ["certificadoServiciosId" => $CertificadoServicios->getId()];
		$reportUnit = "/reports/certificadoServicios";
		return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
	}

	/**
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function certificadoActividadAction($id)
	{
		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

		$format = "pdf";
		$parametros = ["certificadoServiciosId" => $CertificadoServicios->getId()];
		$reportUnit = "/reports/certificadoActividad";
		return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);

	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function seguimientoLineaBaseAction()
	{
		$format = "pdf";
		$reportUnit = "/reports/seguimientoLB";
		return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function seguimientoPLAAction()
	{
		$format = "pdf";
		$reportUnit = "/reports/seguimientoPLA";
		return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
	}
	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function esfuerzoPLACompromisoAction()
	{
		$format = "pdf";
		$reportUnit = "/reports/seguimientoPLACompromiso";
		return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function plasByAgrupacionAction()
	{
		$format = "pdf";
		$reportUnit = "/reports/plasByAgrupacion";
		return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
	}

	/**
	 * @param $pAgrupacion
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function plasByAgrupacion2Action($pAgrupacion)
	{
		$format = "pdf";
		$reportUnit = "/reports/plasByAgrupacion2";
		$params = ["pAgrupacion" => $pAgrupacion];
		return $this->get('yoh.jasper.report')->generate($reportUnit, $params, $format);
	}

	public function planificacionAction($pAgrupacion)
	{
		$format = "pdf";
		$reportUnit = "/reports/pla001";
		$params = ["pAgrupacion" => $pAgrupacion];
		return $this->get('yoh.jasper.report')->generate($reportUnit, $params, $format);
	}

	public function planificacionProyectoAction($pProyecto)
	{
		$format = "pdf";
		$reportUnit = "/reports/planificacionProyecto";
		$params = ["pProyecto" => $pProyecto];
		return $this->get('yoh.jasper.report')->generate($reportUnit, $params, $format);
	}

}
