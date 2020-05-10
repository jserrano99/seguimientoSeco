<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\AnalisisServicioDatatable;
use AppBundle\Entity\AnalisisServicio;
use AppBundle\Entity\AnalisisServicioDetalle;
use AppBundle\Entity\AnalisisServicioPeriodo;
use AppBundle\Entity\Mes;
use AppBundle\Form\PeriodoActualType;
use AppBundle\Form\PeriodoType;
use DateInterval;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;

/**
 * Class AnalisisServicioController
 *
 * @package AppBundle\Controller
 */
class AnalisisServicioController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * AnalisisServicioController constructor.
	 */
	public function __construct()
	{
		$this->sesion = new Session();
	}

	/**
	 * @param Request $request
	 * @return JsonResponse|Response
	 * @throws Exception
	 */
	public function queryAction(Request $request)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$AnalisisServicioAll = $EntityManager->getRepository("AppBundle:AnalisisServicio")->findAll();

		return $this->render('analisisServicio/queryAnalisisServicio.html.twig', [
			'AnalisisServicioALL' => $AnalisisServicioAll,
		]);
	}

	/**
	 * @param $id
	 * @return JsonResponse|Response
	 */
	public function queryAnalisisServicioPeriodoAction($id)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$AnalisisServicio = $EntityManager->getRepository("AppBundle:AnalisisServicio")->find($id);
		$AnalisisServicioPeriodoAll = $EntityManager->getRepository("AppBundle:AnalisisServicioPeriodo")->findBy(["analisisServicio" => $AnalisisServicio]);

		return $this->render('analisisServicio/queryAnalisisServicioPeriodo.html.twig', [
			'AnalisisServicio' => $AnalisisServicio,
			'AnalisisServicioAPeriodoAll' => $AnalisisServicioPeriodoAll,
		]);
	}

	/**
	 * @param $id
	 * @return JsonResponse|Response
	 */
	public function queryAnalisisServicioDetalleAction($id)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$AnalisisServicioPeriodo = $EntityManager->getRepository("AppBundle:AnalisisServicioPeriodo")->find($id);
		$AnalisisServicioDetalleAll = $EntityManager->getRepository("AppBundle:AnalisisServicioDetalle")->findBy(["analisisServicioPeriodo" => $AnalisisServicioPeriodo]);

		return $this->render('analisisServicio/queryAnalisisServicioDetalle.html.twig', [
			'AnalisisServicioPeriodo' => $AnalisisServicioPeriodo,
			'AnalisisServicioDetalleAll' => $AnalisisServicioDetalleAll,
		]);
	}
	/**
	 * @param Request $request
	 * @return Response
	 */
	public function addAction(Request $request)

	{
		$EntityManager = $this->getDoctrine()->getManager();
		$PeriodoActual = $EntityManager->getRepository("AppBundle:PeriodoActual")->find(1);
		$form = $this->createForm(PeriodoType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			return $this->redirectToRoute("queryAnalisisServicio");
		}

		$params = ["periodoActual" => $PeriodoActual,
			"form" => $form->createView()];
		return $this->render("analisisServicio/genera.html.twig", $params);

	}

	/**
	 * @param $periodo_id
	 * @return bool
	 */
	public function generaAnalisisServicioAction($periodo_id)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoObjetoNPL = $EntityManager->getRepository("AppBundle:TipoObjeto")->find(1);

		/** @var Mes $Periodo */
		$Periodo = $EntityManager->getRepository("AppBundle:Mes")->findOneBy(["id" => $periodo_id]);

		$AnalisisServicio = $EntityManager->getRepository("AppBundle:AnalisisServicio")->findOneBy(["mes" => $Periodo]);
		if ($AnalisisServicio) {
			$EntityManager->remove($AnalisisServicio);
		}

		$AplicacionAll = $EntityManager->getRepository("AppBundle:Aplicacion")->findAll();
		$ObjetosEncargoAll = $EntityManager->getRepository("AppBundle:ObjetoEncargo")
			->findBy(["tipoObjeto" => $TipoObjetoNPL]);
//		$CriticidadAll = $EntityManager->getRepository("AppBundle:Criticidad")->findAll();
		$CriticidadNormal = $EntityManager->getRepository("AppBundle:Criticidad")->find(1);

		$fechaInicial = $Periodo->getFechaInicio();
		$fechaFinal = $Periodo->getFechaFin();
		$conection = $this->getDoctrine()->getConnection();
		$sentenciaEntradas = " select ifnull(count(*),0) as entradas "
			. " from view_encargos_npl_total "
			. " where date_format(fechaRegistro,'%Y-%m-%d') = :fecha "
			. "   and aplicacionId = :aplicacionId "
			. "   and criticidadId = :criticidadId "
			. "   and objetoEncargoId = :objetoEncargoId ";

		$sentenciaCancelados = " select ifnull(count(*),0) as cancelados "
			. " from view_encargos_npl_total "
			. " where date_format(fechaEstadoActual,'%Y-%m-%d') = :fecha "
			. "   and aplicacionId = :aplicacionId "
			. "   and criticidadId = :criticidadId "
			. "   and objetoEncargoId = :objetoEncargoId "
			. " and estadoEncargoCd = 'CAN' ";

		$sentenciaCerrados = " select ifnull(count(*),0) as cerrados ,"
			. " ifnull(avg(horasRealizadas),0) as esfuerzoMedio,"
			. " ifnull(sum(horasRealizadas),0) as esfuerzoTotal,"
			. " ifnull(avg(tiempoResolucion/3600000,0) as tiempoMedioResolucion,"
			. " ifnull(sum(tiempoResolucion/3600000),0) as tiempoTotalResolucion"
			. " from view_encargos_npl_total "
			. " where date_format(fechaEntrega,'%Y-%m-%d') = :fecha "
			. "   and aplicacionId = :aplicacionId "
			. "   and criticidadId = :criticidadId "
			. "   and objetoEncargoId = :objetoEncargoId "
			. "   and estadoEncargoCd = 'CRR'";

		// Se recorre desde la fecha de inicio a la fecha fin, incrementando en 1 dia
		/** @var DateTime $midia */
		$fechaInicio = clone $Periodo->getFechaInicio();
		$fechaFin = clone $Periodo->getFechaFin();
		$AnalisisServicio = new AnalisisServicio();
		$AnalisisServicio->setMes($Periodo);
		$EntityManager->persist($AnalisisServicio);
		$EntityManager->flush();
		$Contador2 = [];
		$Contador2["entradas"] = 0;
		$Contador2["cancelados"] = 0;
		$Contador2["cerrados"] = 0;
		$Contador2["esfuerzoTotal"] = 0;
		$Contador2["tiempoTotalResolucion"] = 0;
		foreach ($AplicacionAll as $Aplicacion) {
			foreach ($ObjetosEncargoAll as $ObjetoEncargo) {
				$AnalisisServicioPeridodo = new AnalisisServicioPeriodo();
				$AnalisisServicioPeridodo->setAnalisisServicio($AnalisisServicio);
				$AnalisisServicioPeridodo->setAplicacion($Aplicacion);
				$AnalisisServicioPeridodo->setObjetoEncargo($ObjetoEncargo);
				$AnalisisServicioPeridodo->setCriticidad($CriticidadNormal);
				$EntityManager->persist($AnalisisServicioPeridodo);
				$EntityManager->flush();
				$Contador = [];
				$Contador["entradas"] = 0;
				$Contador["cancelados"] = 0;
				$Contador["cerrados"] = 0;
				$Contador["esfuerzoTotal"] = 0;
				$Contador["tiempoTotalResolucion"] = 0;
				$fechaInicio = clone $Periodo->getFechaInicio();
				$fechaFin = clone $Periodo->getFechaFin();
				for ($midia = $fechaInicio; $midia <= $fechaFin; $midia->add(new DateInterval('P1D'))) {
					$AnalisisServicioDetalle = new AnalisisServicioDetalle();
					$AnalisisServicioDetalle->setAnalisisServicioPeriodo($AnalisisServicioPeridodo);
					$AnalisisServicioDetalle->setFecha($midia);
					$EntityManager->persist($AnalisisServicioDetalle);
					$EntityManager->flush();
					/**
					 * ENTRADAS
					 */
					$params = [":fecha" => $midia->format('Y-m-d'),
						":aplicacionId" => $Aplicacion->getId(),
						":objetoEncargoId" => $ObjetoEncargo->getId(),
						":criticidadId" => $CriticidadNormal->getId()];
					$stmt = $conection->prepare($sentenciaEntradas);
					$stmt->execute($params);
					$EntradasDia = $stmt->fetch();
					/**
					 * CANCELADOS
					 */
					$stmt = $conection->prepare($sentenciaCancelados);
					$stmt->execute($params);
					$Cancelados = $stmt->fetch();
					/**
					 * CERRADOS
					 */
					$stmt = $conection->prepare($sentenciaCerrados);
					$stmt->execute($params);
					$Cerrados = $stmt->fetch();

					$AnalisisServicioDetalle->setTotalEntradas($EntradasDia["entradas"]);
					$AnalisisServicioDetalle->setTotalCancelados($Cancelados["cancelados"]);
					$AnalisisServicioDetalle->setTotalCerrados($Cerrados["cerrados"]);
					$saldo = $EntradasDia["entradas"] - ($Cancelados["cancelados"] + $Cerrados["cerrados"]);
					$AnalisisServicioDetalle->setSaldo($saldo);
					$AnalisisServicioDetalle->setEsfuerzoTotal($Cerrados["esfuerzoTotal"]);
					$AnalisisServicioDetalle->setEsfuerzoMedio($Cerrados["esfuerzoMedio"]);
					$AnalisisServicioDetalle->setTiempoTotalResolucion($Cerrados["tiempoTotalResolucion"]);
					$AnalisisServicioDetalle->setTiempoMedioResolucion($Cerrados["tiempoMedioResolucion"]);
					$EntityManager->persist($AnalisisServicioDetalle);
					$EntityManager->flush();

					$Contador["entradas"] = $Contador["entradas"] + $AnalisisServicioDetalle->getTotalEntradas();
					$Contador["cancelados"] = $Contador["cancelados"] + $AnalisisServicioDetalle->getTotalCancelados();
					$Contador["cerrados"] = $Contador["cerrados"] + $AnalisisServicioDetalle->getTotalCerrados();
					$Contador["esfuerzoTotal"] = $Contador["esfuerzoTotal"] + $AnalisisServicioDetalle->getEsfuerzoTotal();
					$Contador["tiempoTotalResolucion"] = $Contador["tiempoTotalResolucion"] + $AnalisisServicioDetalle->getTiempoTotalResolucion();
				}
				$AnalisisServicioPeridodo->setTotalEntradas($Contador["entradas"]);
				$AnalisisServicioPeridodo->setTotalCerrados($Contador["cerrados"]);
				$AnalisisServicioPeridodo->setTotalCancelados($Contador["cancelados"]);
				$saldo = $Contador["entradas"] - ($Contador["cancelados"] + $Contador["cerrados"]);
				$AnalisisServicioPeridodo->setSaldo($saldo);

				$Contador["cerrados"] > 0 ? $esfuerzoMedio = $Contador["esfuerzoTotal"] / $Contador["cerrados"] : $esfuerzoMedio = 0;
				$AnalisisServicioPeridodo->setEsfuerzoMedio($esfuerzoMedio);
				$AnalisisServicioPeridodo->setEsfuerzoTotal($Contador["esfuerzoTotal"]);
				$Contador["cerrados"] > 0 ? $tiempoMedioResolucion = $Contador["tiempoTotalResolucion"] / $Contador["cerrados"] : $tiempoMedioResolucion = 0;
				$AnalisisServicioPeridodo->setTiempoTotalResolucion($Contador["tiempoTotalResolucion"]);
				$AnalisisServicioPeridodo->setTiempoMedioResolucion($tiempoMedioResolucion);
				$EntityManager->persist($AnalisisServicioPeridodo);
				$EntityManager->flush();

				$Contador2["entradas"] = $Contador2["entradas"] + $AnalisisServicioPeridodo->getTotalEntradas();
				$Contador2["cancelados"] = $Contador2["cancelados"] + $AnalisisServicioPeridodo->getTotalCancelados();
				$Contador2["cerrados"] = $Contador2["cerrados"] + $AnalisisServicioPeridodo->getTotalCerrados();
				$Contador2["esfuerzoTotal"] = $Contador2["esfuerzoTotal"] + $AnalisisServicioPeridodo->getEsfuerzoTotal();
				$Contador2["tiempoTotalResolucion"] = $Contador2["tiempoTotalResolucion"] + $AnalisisServicioPeridodo->getTiempoTotalResolucion();
			}
		}

		$AnalisisServicio->setTotalEntradas($Contador2["entradas"]);
		$AnalisisServicio->setTotalCerrados($Contador2["cerrados"]);
		$AnalisisServicio->setTotalCancelados($Contador2["cancelados"]);
		$saldo = $Contador2["entradas"] - ($Contador2["cancelados"] + $Contador2["cerrados"]);
		$AnalisisServicio->setSaldo($saldo);

		$Contador2["cerrados"] > 0 ? $esfuerzoMedio = $Contador2["esfuerzoTotal"] / $Contador2["cerrados"] : $esfuerzoMedio = 0;
		$AnalisisServicio->setEsfuerzoMedio($esfuerzoMedio);
		$AnalisisServicio->setEsfuerzoTotal($Contador2["esfuerzoTotal"]);
		$Contador2["cerrados"] > 0 ? $tiempoMedioResolucion = $Contador2["tiempoTotalResolucion"] / $Contador2["cerrados"] : $tiempoMedioResolucion = 0;
		$AnalisisServicio->setTiempoTotalResolucion($Contador2["tiempoTotalResolucion"]);
		$AnalisisServicio->setTiempoMedioResolucion($tiempoMedioResolucion);
		$EntityManager->persist($AnalisisServicio);
		$EntityManager->flush();

		return new Response();
	}


}
