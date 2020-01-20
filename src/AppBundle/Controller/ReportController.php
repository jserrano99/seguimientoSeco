<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Filtro;
use AppBundle\Entity\Report;
use AppBundle\Form\FiltroType;
use AppBundle\Form\ReportType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class ReportController
 *
 * @package AppBundle\Controller
 */
class ReportController extends Controller
{
    /**
     * @var Session
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
     * @param Request $request
     * @return Response
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
     * @param Request $request
     * @return Response
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
     * @param Request $request
     * @return Response
     */
    public function incidentesPorCentroAction(Request $request)
    {
        $Filtro = new Filtro();
        $filtroForm = $this->createForm(FiltroType::class, $Filtro);
        $filtroForm->handleRequest($request);
        if ($filtroForm->isSubmitted()) {
            $format = "pdf";
            $parametros = ["mesId" => $Filtro->getMes()->getId(),
                "centroId" => $Filtro->getCentro()->getId()];
            $reportUnit = "/reports/incidentesPorCentro2";
            return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
        }
        return $this->render("informe/edit.html.twig", array(
            "form" => $filtroForm->createView()
        ));

    }


    /**
     * @param Request $request
     * @return Response
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
	 * @param int $id
	 * @return Response
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
     * @param integer $id
     * @return mixed
     */
    public function penalizacionesAction($id)
    {
        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

        $format = "pdf";
        $parametros = ["certificadoServiciosId" => $CertificadoServicios->getId()];
        $reportUnit = "/reports/penalizaciones";
        return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function penalizacionesDetalleAction($id)
    {
        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

        $format = "pdf";
        $parametros = ["certificadoServiciosId" => $CertificadoServicios->getId()];
        $reportUnit = "/reports/penalizacionesDetalle";
        return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
    }

    /**
     * @param $id
     * @return Response
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
     * @return Response
     */
    public function seguimientoLineaBaseAction()
    {
        $format = "pdf";
        $reportUnit = "/reports/seguimientoLB";
        return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
    }

	/**
	 * @return Response
	 */
	public function seguimientoImportesAction()
	{
		$format = "pdf";
		$reportUnit = "/reports/seguimientoImportes";
		return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
	}

	/**
	 * @return Response
	 */
	public function esfuerzoComprometidoEquipoBaseAction()
	{
		$format = "pdf";
		$reportUnit = "/reports/esfuerzoComprometidoEquipoBase";
		return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
	}
	/**
	 * @return Response
	 */
	public function planificacionLBAction()
	{
		$format = "pdf";
		$reportUnit = "/reports/planificacionLB";
		return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
	}



	/**
     * @return Response
     */
    public function seguimientoPLAAction()
    {
        $format = "pdf";
        $reportUnit = "/reports/seguimientoPLA";
        return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
    }

    /**
     * @return Response
     */
    public function esfuerzoPLACompromisoAction()
    {
        $format = "pdf";
        $reportUnit = "/reports/seguimientoPLACompromiso";
        return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
    }

    /**
     * @return Response
     */
    public function plasByAgrupacionAction()
    {
        $format = "pdf";
        $reportUnit = "/reports/plasByAgrupacion";
        return $this->get('yoh.jasper.report')->generate($reportUnit, [], $format);
    }

	/**
	 * @return Response
	 */
	public function informeSeguimientoAction($seguimiento_id)
	{
		$format = "pdf";
		$reportUnit = "/reports/informeSeguimiento";
		$params = ["seguimientoId" => $seguimiento_id];
		return $this->get('yoh.jasper.report')->generate($reportUnit, $params, $format);
	}

	/**
     * @param $pAgrupacion
     * @return Response
     */
    public function encargosAgrupacionAction($id)
    {
        $format = "pdf";
        $reportUnit = "/reports/encargosAgrupacion";
        $params = ["agrupacionId" => $id];
        return $this->get('yoh.jasper.report')->generate($reportUnit, $params, $format);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function planificacionEBAction(Request $request)
    {
        $Filtro = new Filtro();
        $filtroForm = $this->createForm(FiltroType::class, $Filtro);
        $filtroForm->handleRequest($request);
        if ($filtroForm->isSubmitted()) {
            $format = "pdf";
            $parametros = ["mesesId" => $Filtro->getMes()->getId()];
            $reportUnit = "/reports/plan3";
//			dump($parametros);
//			die();
            return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
        }
        return $this->render("informe/edit.html.twig", array(
            "form" => $filtroForm->createView()
        ));


    }


    /**
     * @param Request $request
     * @return Response
     */
    public function esfuerzoNplAplicacionAction(Request $request)
    {
        $Filtro = new Filtro();
        $filtroForm = $this->createForm(FiltroType::class, $Filtro);
        $filtroForm->handleRequest($request);
        if ($filtroForm->isSubmitted()) {
            $format = "pdf";
            $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->findOneBy(["mes" => $Filtro->getMes()]);
            if (is_null($CertificadoServicios)) {
                $status = "***NO HAY CERTIFICADO DE SERVICIOS PARA ESTE MES ***";
                $this->sesion->getFlashBag()->add("status", $status);
            } else {
                $parametros = ["certificadoServiciosId" => $CertificadoServicios->getId()];
                $reportUnit = "/reports/esfuerzoMedioAplicacion";
                return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
            }

        }
        return $this->render("informe/edit.html.twig", array(
            "form" => $filtroForm->createView()
        ));

    }
}
