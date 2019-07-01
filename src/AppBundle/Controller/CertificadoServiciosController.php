<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CertificadoServicios;
use AppBundle\Entity\EncargoPenalizado;
use AppBundle\Entity\FicheroLog;
use AppBundle\Entity\ImportesCertificado;
use AppBundle\Entity\LineaCertificado;
use AppBundle\Form\AddEncargoType;
use AppBundle\Form\CertificadoServiciosType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class CertificadoServiciosController
 *
 * @package AppBundle\Controller
 */
class CertificadoServiciosController extends Controller
{
	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $sesion;

	/**
	 * CertificadoServiciosController constructor.
	 */
	public function __construct()
	{
		$this->sesion = new Session();
	}


	public function queryAction(Request $request)
	{
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(\AppBundle\Datatables\CertificadoServiciosDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('certificadoServicios/query.html.twig', array(
			'datatable' => $datatable,
		));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param                                           $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, $id)
	{

		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

		$form = $this->createForm(CertificadoServiciosType::class, $CertificadoServicios);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
		}

		return $this->render("certificadoServicios/edit.html.twig", array(
			"form" => $form->createView(),
			"accion" => "EDITAR"));

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function addAction(Request $request)
	{

		$CertificadoServicios = new CertificadoServicios();
		$form = $this->createForm(CertificadoServiciosType::class, $CertificadoServicios);
		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			$CertificadoServicios2 = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->findOneBy(["mes" => $CertificadoServicios->getMes()]);
			if ($CertificadoServicios2) {
				$status = " YA EXISTE UN CERTIFICADO PARA ESTE MES ";
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->render("certificadoServicios/edit.html.twig", array(
					"form" => $form->createView(),
					"accion" => "GENERAR"));
			} else {
				$EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);
				$Contrato = $this->getDoctrine()->getManager()->getRepository("AppBundle:Contrato")->find(1);
				$CertificadoServicios->setEstadoCertificado($EstadoCertificado);
				$CertificadoServicios->setContrato($Contrato);
				$CertificadoServicios->setDescripcion("Certificado de Servicios " . $CertificadoServicios->getMes()->getDescripcion() . ", " . $CertificadoServicios->getMes()->getAnyo()->getDescripcion());
				$this->getDoctrine()->getManager()->persist($CertificadoServicios);
				$this->getDoctrine()->getManager()->flush();
				$this->generarCertificado($CertificadoServicios);
				return $this->redirectToRoute("queryCertificadoServicios");
			}
		}
		return $this->render("certificadoServicios/edit.html.twig", array(
			"form" => $form->createView(),
			"accion" => "GENERAR"
		));

	}


	/**
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($id)
	{
		$sentencia = " update encargo set bloqueado = null "
			. " where encargo.id in (select encargo_id from linea_certificado "
			. "  where certificado_servicios_id = :id )";


		$conexion = $this->getDoctrine()->getConnection()->prepare($sentencia);
		$params = [":id" => $id];
		$conexion->execute($params);

		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

		$this->getDoctrine()->getManager()->remove($CertificadoServicios);
		$this->getDoctrine()->getManager()->flush();

		$status = " CERTIFICADO DE SERVICIO ELIMINADO CORRECTAMENTE ";
		$this->sesion->getFlashBag()->add("status", $status);
		return $this->redirectToRoute("queryCertificadoServicios");
	}

	/**
	 * @param  \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @return mixed
	 * @throws \Exception
	 */

	public function generarCertificado($CertificadoServicios)
	{

		$EntityManager = $this->getDoctrine()->getManager();
		$FicheroLog = new FicheroLog();
		$fechaProceso = new \DateTime();
		$FicheroLog->setFechaProceso($fechaProceso);
		$FicheroLog->setFechaProceso($fechaProceso);


		$ServicioLog = $this->get('app.escribelog');
		$ServicioLog->setLogger('CERT. Servicios: = ' . $CertificadoServicios->getId());
		$ficheroLog = 'FicheroLog-' . $CertificadoServicios->getId();
		$FicheroLog->setFichero($ficheroLog);

		$EntityManager->persist($FicheroLog);
		$EntityManager->flush();

		$CertificadoServicios->setFicheroLog($FicheroLog);
		$EntityManager->persist($CertificadoServicios);
		$EntityManager->flush();

		$ServicioLog->setMensaje("GENERACIÓN CERTIFICADO SERVICIOS: " . $CertificadoServicios->getDescripcion());
		$ServicioLog->escribeLog($ficheroLog);

		/**
		 * SERVICIOS INCLUIDOS EN LA CUOTA FIJA
		 */
		$ServicioLog->setMensaje("==>INCLUIR ENCARGOS NO PLANIFICABLES ");
		$ServicioLog->escribeLog($ficheroLog);
		$this->incluirNPL($CertificadoServicios, $ServicioLog, $ficheroLog);

		$ServicioLog->setMensaje("==>INCLUIR ENCARGOS ADAPTACIONES MENORES ");
		$ServicioLog->escribeLog($ficheroLog);
		$this->incluirADM($CertificadoServicios, $ServicioLog, $ficheroLog);

		$ServicioLog->setMensaje("==>INCLUIR ENCARGOS EVOLUTIVOS DE CUOTA FIJA ");
		$ServicioLog->escribeLog($ficheroLog);
		$this->incluirSCF($CertificadoServicios, $ServicioLog, $ficheroLog);

		/**
		 * SERVICIOS INCLUIDOS EN LA CUOTA VARIABLE
		 */
		$ServicioLog->setMensaje("==>INCLUIR ENCARGOS EVOLUTIVOS DE CUOTA VARIABLE ");
		$ServicioLog->escribeLog($ficheroLog);
		$this->incluirPLA($CertificadoServicios, $ServicioLog, $ficheroLog);


		/**
		 * SERVICIOS INCLUIDOS EN LA CUOTA TASADA
		 */
		$ServicioLog->setMensaje("==>INCLUIR ENCARGOS EVOLUTIVOS DE CUOTA TASADA ");
		$ServicioLog->escribeLog($ficheroLog);
		$this->incluirTAS($CertificadoServicios, $ServicioLog, $ficheroLog);
		/**
		 * GENERAR LOS IMPORTES
		 */
		$ServicioLog->setMensaje("==>GENERACIÓN DE IMPORTES DEL CERTIFICADO");
		$ServicioLog->escribeLog($ficheroLog);
		$this->generarImportes($CertificadoServicios, $ServicioLog, $ficheroLog);

		return true;

	}

	/**
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function importesAction($id)
	{

		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

		$this->generarImportes($CertificadoServicios);

		$status = " IMPORTES DEL CERTIFICADO DE SERVICIO GENERADO CORRECTAMENTE ";
		$this->sesion->getFlashBag()->add("status", $status);
		return $this->redirectToRoute("queryCertificadoServicios");

	}


	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @return bool
	 */
	public function generarImportes($CertificadoServicios)
	{

		$sentencia = " delete from importes_certificado_servicios  "
			. " where  certificado_servicios_id = :id";

		$conexion = $this->getDoctrine()->getConnection()->prepare($sentencia);
		$params = [":id" => $CertificadoServicios->getId()];
		$conexion->execute($params);

		$ImporteCuotaFija = $this->importesCuotaFija($CertificadoServicios);
		$ImporteCuotaVariable = $this->importesCuotaVariable($CertificadoServicios);
		$ImporteCuotaTasada = $this->importesCuotaTasada($CertificadoServicios);

		$totalFactura = $ImporteCuotaFija->getImporte() + $ImporteCuotaVariable->getImporte() + $ImporteCuotaTasada->getImporte();
		if ($CertificadoServicios->getAplicaPenalizacion()) {
			$totalPenalizaciones = $ImporteCuotaFija->getPenalizacion() + $ImporteCuotaVariable->getPenalizacion() + $ImporteCuotaTasada->getPenalizacion();
			$maximoPenalizaciones = $totalFactura * 0.20;
			$penalizacionAplicable = min($maximoPenalizaciones, $totalPenalizaciones);
		} else {
			$totalPenalizaciones = 0;
			$maximoPenalizaciones = 0;
			$penalizacionAplicable = 0;
		}

		$cuotaIVA = $totalFactura * 0.21;
		$totalFacturaIVA = $totalFactura + $cuotaIVA - $penalizacionAplicable;
		$CertificadoServicios->setTotalFactura($totalFactura);
		$CertificadoServicios->setTotalPenalizaciones($totalPenalizaciones);
		$CertificadoServicios->setMaximoPenalizaciones($maximoPenalizaciones);
		$CertificadoServicios->setCuotaIva($cuotaIVA);
		$CertificadoServicios->setPenalizacionAplicable($penalizacionAplicable);
		$CertificadoServicios->setTotalFacturaConIva($totalFacturaIVA);
		$EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);
		$CertificadoServicios->setEstadoCertificado($EstadoCertificado);

		$this->getDoctrine()->getManager()->persist($CertificadoServicios);
		$this->getDoctrine()->getManager()->flush();

		return true;
	}

	/**
	 * @param CertificadoServicios $CertificadoServicios
	 * @return \AppBundle\Entity\ImportesCertificado
	 */
	public function importesCuotaTasada($CertificadoServicios)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(3);

		$LineaCertificadoAll = $EntityManager->getRepository("AppBundle:LineaCertificado")
			->findBy(["certificadoServicios" => $CertificadoServicios, "tipoCuota" => $TipoCuota]);

		$importe = 0;
		foreach ($LineaCertificadoAll as $LineaCertificado) {
			$importe = $importe + $LineaCertificado->getEncargo()->getCoste();
		}

		$horas = 0;
		$tarifa = 0;
		$penalizacion = 0;
		$total = 0;

		$ImportesCertificado = new ImportesCertificado();
		$ImportesCertificado->setCertificadoServicios($CertificadoServicios);
		$ImportesCertificado->setCodigo(2);
		$ImportesCertificado->setTipoCuota($TipoCuota);
		$ImportesCertificado->setDescripcion("Servicio de Implantaciones");
		$ImportesCertificado->setHorasCertificadas($horas);
		$ImportesCertificado->setTarifa($tarifa);
		$ImportesCertificado->setImporte($importe);
		$ImportesCertificado->setPenalizacion($penalizacion);
		$ImportesCertificado->setTotal($total);
		$EntityManager->persist($ImportesCertificado);
		$EntityManager->flush();
		return $ImportesCertificado;


	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @return \AppBundle\Entity\ImportesCertificado
	 */
	public function importesCuotaVariable($CertificadoServicios)
	{

		$EntityManager = $this->getDoctrine()->getManager();
		$TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(2);

		$tarifa = 37.47;

		$LineaCertificadoAll = $EntityManager->getRepository("AppBundle:LineaCertificado")
			->findBy(["certificadoServicios" => $CertificadoServicios, "tipoCuota" => $TipoCuota]);

		$importe = 0;
		$horas = 0;
		$penalizacion = 0;
		foreach ($LineaCertificadoAll as $LineaCertificado) {
			$horas = $horas + $LineaCertificado->getEncargo()->getHorasComprometidas();
			$importe = $importe + ($horas * $tarifa);
			$penalizacion = 0;
		}

		$total = $importe - $penalizacion;


		$ImportesCertificado = new ImportesCertificado();
		$ImportesCertificado->setCertificadoServicios($CertificadoServicios);
		$ImportesCertificado->setCodigo(2);
		$ImportesCertificado->setTipoCuota($TipoCuota);
		$ImportesCertificado->setDescripcion("Servicio de Evolución de Amplio Alcance");
		$ImportesCertificado->setHorasCertificadas($horas);
		$ImportesCertificado->setTarifa($tarifa);
		$ImportesCertificado->setImporte($importe);
		$ImportesCertificado->setPenalizacion($penalizacion);
		$ImportesCertificado->setTotal($total);
		$EntityManager->persist($ImportesCertificado);
		$EntityManager->flush();
		return $ImportesCertificado;

	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @return \AppBundle\Entity\ImportesCertificado
	 */
	public
	function importesCuotaFija($CertificadoServicios)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$TipoCuota = $entityManager->getRepository("AppBundle:TipoCuota")->find(1);

		$ImportesContrato = $entityManager->getRepository("AppBundle:ImportesContrato")->findOneBy(["contrato" => $CertificadoServicios->getContrato()]);

		$CertificadoServicios->getAplicaPenalizacion() == null ? $totalPenalización = 0 : $totalPenalización = $this->penalizacionNPL($CertificadoServicios);
		$ImportesCertificado = new ImportesCertificado();
		$ImportesCertificado->setCertificadoServicios($CertificadoServicios);
		$ImportesCertificado->setCodigo(1);
		$ImportesCertificado->setTipoCuota($TipoCuota);
		$ImportesCertificado->setDescripcion("Servicio de Atención, Soporte, Mantenimiento y Evolución de Corto Alcance");
		$ImportesCertificado->setHorasCertificadas(null);
		$ImportesCertificado->setTarifa(null);
		$ImportesCertificado->setImporte($ImportesContrato->getCuotaFijaMensual());
		$ImportesCertificado->setPenalizacion($totalPenalización);
		$ImportesCertificado->setTotal($ImportesContrato->getCuotaFijaMensual());
		$entityManager->persist($ImportesCertificado);
		$entityManager->flush();


		return $ImportesCertificado;
	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @return float
	 */
	public
	function penalizacionNPL($CertificadoServicios)
	{

		$entityManager = $this->getDoctrine()->getManager();
		$encargosNPL = $CertificadoServicios->getContadorNPL();

		$IndicadorIRS01 = $entityManager->getRepository("AppBundle:Indicador")->find(1);
		$EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS01]);
		$encargosCumplen = $encargosNPL - count($EncargosPenalizadosALL);

		$porcentaje = $encargosCumplen / $encargosNPL;
		$factor = 0;
		if ($porcentaje > 0.95) $factor = 0;
		if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
		if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
		if ($porcentaje <= 0.85) $factor = 1;

		$pesoIRS01 = $IndicadorIRS01->getPeso() * $factor;

		$IndicadorIRS02 = $entityManager->getRepository("AppBundle:Indicador")->find(3);
		$EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS02]);
		$encargosCumplen = $encargosNPL - count($EncargosPenalizadosALL);
		$porcentaje = $encargosCumplen / $encargosNPL;
		$factor = 0;
		if ($porcentaje > 0.95) $factor = 0;
		if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
		if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
		if ($porcentaje <= 0.85) $factor = 1;

		$pesoIRS02 = $IndicadorIRS02->getPeso() * $factor;

		$IndicadorIRS03 = $entityManager->getRepository("AppBundle:Indicador")->find(4);
		$EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS03]);
		$encargosCumplen = $encargosNPL - count($EncargosPenalizadosALL);
		$porcentaje = $encargosCumplen / $encargosNPL;
		$factor = 0;
		if ($porcentaje > 0.95) $factor = 0;
		if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
		if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
		if ($porcentaje <= 0.85) $factor = 1;

		$pesoIRS03 = $IndicadorIRS02->getPeso() * $factor;


		$peso = $pesoIRS01 + $pesoIRS02 + $pesoIRS03;

		$penalizacion = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;

		return $penalizacion;

	}

	public
	function penalizacionADM($CertificadoServicios)
	{
		return 0.0;

	}

	public
	function penalizacionPLA($CertificadoServicios)
	{
		return 0.0;

	}

	public
	function penalizacionTAS($CertificadoServicios)
	{
		return 0.0;

	}

	/**
	 * NO PLANIFICABLE
	 */
	/**
	 * @param CertificadoServicios            $CertificadoServicios
	 * @param \AppBundle\Servicios\EscribeLog $ServicioLog
	 * @param FicheroLog                      $ficheroLog
	 * @return bool
	 */
	public
	function incluirNPL($CertificadoServicios, $ServicioLog, $ficheroLog)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoObjetoEncargoNPL = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(1);
		$TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(1);

		$ObjetoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:ObjetoEncargo");
		$EncargoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo");
		$EstadoEncargoCRR = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(2);
		$EstadoEncargoFIN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(12);
		$EstadoEncargoCAN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(10);

		$IndicadorIRS01 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(1);
		$IndicadorIRS02 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(3);

		$ObjetosEncargoNPLAll = $ObjetoRepository->createQueryBuilder('u')
			->where('u.tipoObjeto = :tipoObjeto')
			->setParameter('tipoObjeto', $TipoObjetoEncargoNPL)
			->getQuery()->getResult();

		$ct = 0;
		foreach ($ObjetosEncargoNPLAll as $ObjetosEncargoNPL) {
			//CERRADOS, FINALIZADOS y CANCELADOS
			$Encargos = $EncargoRepository->createQueryBuilder('u')
				->where('u.objetoEncargo = :objetoEncargo')
				->andWhere('u.estadoActual in (:estadoEncargo1, :estadoEncargo2, :estadoEncargo3)')
				->andWhere('u.fcEstadoActual >= :fcini and u.fcEstadoActual <= :fcfin')
				->setParameter('estadoEncargo1', $EstadoEncargoCRR)
				->setParameter('estadoEncargo2', $EstadoEncargoFIN)
				->setParameter('estadoEncargo3', $EstadoEncargoCAN)
				->setParameter('objetoEncargo', $ObjetosEncargoNPL)
				->setParameter('fcini', $CertificadoServicios->getMes()->getFechaInicio())
				->setParameter('fcfin', $CertificadoServicios->getMes()->getFechaFin())
				->getQuery()->getResult();

			foreach ($Encargos as $Encargo) {
				$Existe = $this->encargoEnCertificado($Encargo);
				if ($Existe) {
					$ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Existe->getDescripcion());
					$ServicioLog->escribeLog($ficheroLog);
					continue;
				}
				$LineaCertificado = new LineaCertificado();
				$LineaCertificado->setCertificadoServicios($CertificadoServicios);
				$LineaCertificado->setTipoCuota($TipoCuota);
				$LineaCertificado->setEncargo($Encargo);
				$this->getDoctrine()->getManager()->persist($LineaCertificado);
				$this->getDoctrine()->getManager()->flush();
				$ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO ");
				$ServicioLog->escribeLog($ficheroLog);

				/**
				 * Si el encargo esta cancelado solo se tiene en cuenta para la imputación de horas, no en el calculo
				 * de ANS
				 */
				if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
					/**
					 * Calculo de las penalizaciones
					 */
					if ($Encargo->getCriticidad() == 3 and $Encargo->getTiempoResolucion() > 14400000) {
						$EncargoPenalizado = new EncargoPenalizado();
						$EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
						$EncargoPenalizado->setIndicador($IndicadorIRS01);
						$EncargoPenalizado->setEncargo($Encargo);
						$EntityManager->persist($EncargoPenalizado);
						$EntityManager->flush();
						$ServicioLog->setMensaje("Encargo " . $Encargo->getNumero() . " *** PENALIZADO *** ");
						$ServicioLog->escribeLog($ficheroLog);
					}
					if ($Encargo->getCriticidad() == 0 and $Encargo->getTiempoResolucion() > 144000000) {
						$EncargoPenalizado = new EncargoPenalizado();
						$EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
						$EncargoPenalizado->setIndicador($IndicadorIRS02);
						$EncargoPenalizado->setEncargo($Encargo);
						$EntityManager->persist($EncargoPenalizado);
						$EntityManager->flush();
						$ServicioLog->setMensaje("Encargo " . $Encargo->getNumero() . " *** PENALIZADO *** ");
						$ServicioLog->escribeLog($ficheroLog);
					}
				}
				$Encargo->setBloqueado(true);
				$this->getDoctrine()->getManager()->persist($Encargo);
				$this->getDoctrine()->getManager()->flush();
				$ct++;
			}
		}

		$CertificadoServicios->setContadorNPL($ct);
		$this->getDoctrine()->getManager()->persist($CertificadoServicios);
		$this->getDoctrine()->getManager()->flush();

		return true;

	}

	/**
	 * @param \AppBundle\Entity\Encargo $Encargo
	 * @return |null
	 */
	public
	function encargoEnCertificado($Encargo)
	{
		$LineaServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:LineaCertificado")->findOneBy(["encargo" => $Encargo]);

		if ($LineaServicios) {
			return $LineaServicios->getCertificadoServicios();
		} else
			return null;
	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @param \AppBundle\Servicios\EscribeLog        $ServicioLog
	 * @param FicheroLog                             $ficheroLog
	 * @return bool
	 * @throws \Exception
	 */
	public
	function incluirADM($CertificadoServicios, $ServicioLog, $ficheroLog)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoObjetoEncargoADM = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(3);
		$TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(1);

		$ObjetoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:ObjetoEncargo");
		$EncargoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo");
		$EstadoEncargoCRR = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(2);
		$EstadoEncargoFIN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(12);
		$EstadoEncargoCAN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(10);

		$IndicadorIRS03 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(3);

		$ObjetosEncargoAll = $ObjetoRepository->createQueryBuilder('u')
			->where('u.tipoObjeto = :tipoObjeto')
			->setParameter('tipoObjeto', $TipoObjetoEncargoADM)
			->getQuery()->getResult();


		foreach ($ObjetosEncargoAll as $ObjetosEncargo) {
			//CERRADOS, FINALIZADOS y CANCELADOS
			$Encargos = $EncargoRepository->createQueryBuilder('u')
				->where('u.objetoEncargo =  :objetoEncargo')
				->andWhere('u.estadoActual in (:estadoEncargo1, :estadoEncargo2, :estadoEncargo3)')
				->andWhere('u.fcEstadoActual >= :fcini and u.fcEstadoActual <= :fcfin')
				->setParameter('estadoEncargo1', $EstadoEncargoCRR)
				->setParameter('estadoEncargo2', $EstadoEncargoFIN)
				->setParameter('estadoEncargo3', $EstadoEncargoCAN)
				->setParameter('objetoEncargo', $ObjetosEncargo)
				->setParameter('fcini', $CertificadoServicios->getMes()->getFechaInicio())
				->setParameter('fcfin', $CertificadoServicios->getMes()->getFechaFin())
				->getQuery()->getResult();

			$ct = 0;
			foreach ($Encargos as $Encargo) {
				$Existe = $this->encargoEnCertificado($Encargo);
				if ($Existe) {
					$ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Existe->getDescripcion());
					$ServicioLog->escribeLog($ficheroLog);
					continue;
				}
				$LineaCertificado = new LineaCertificado();
				$LineaCertificado->setCertificadoServicios($CertificadoServicios);
				$LineaCertificado->setTipoCuota($TipoCuota);
				$LineaCertificado->setEncargo($Encargo);
				$this->getDoctrine()->getManager()->persist($LineaCertificado);
				$this->getDoctrine()->getManager()->flush();
				$ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO ");
				$ServicioLog->escribeLog($ficheroLog);

				if ($Encargo->getEstadoActual() != $EstadoEncargoCAN and !is_null($Encargo->getFcRequeridaEntrega())) {
					if ($Encargo->getFcEntrega() > $Encargo->getFcRequeridaEntrega()->add(new \DateInterval(('P1D')))) {
						//$fecha = $Encargo->getFcRequeridaEntrega()->diff($Encargo->getFcEntrega());
						$EncargoPenalizado = new EncargoPenalizado();
						$EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
						$EncargoPenalizado->setIndicador($IndicadorIRS03);
						$EncargoPenalizado->setEncargo($Encargo);
						$EntityManager->persist($EncargoPenalizado);
						$EntityManager->flush();
						$ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " *** PENALIZADO *** ");
						$ServicioLog->escribeLog($ficheroLog);
					}
				}
				$Encargo->setBloqueado(true);
				$this->getDoctrine()->getManager()->persist($Encargo);
				$this->getDoctrine()->getManager()->flush();
				$ct++;
			}
		}

		$CertificadoServicios->setContadorADM($ct);
		$this->getDoctrine()->getManager()->persist($CertificadoServicios);
		$this->getDoctrine()->getManager()->flush();

		return true;
	}

	/**
	 * @param $CertificadoServicios
	 * @return bool
	 */
	public
	function incluirSCF($CertificadoServicios, $ServicioLog, $ficheroLog)
	{
		$EntityManager = $this->getDoctrine()->getManager();

		$TipoObjetoEncargoSCF = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(4);
		$TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(1);

		$ObjetoRepository = $EntityManager->getRepository("AppBundle:ObjetoEncargo");
		$EncargoRepository = $EntityManager->getRepository("AppBundle:Encargo");
		$EstadoEncargoCRR = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(2);
		$EstadoEncargoFIN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(12);
		$EstadoEncargoCAN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(10);

		//$IndicadorIRS03 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(3);

		$ObjetosEncargoAll = $ObjetoRepository->createQueryBuilder('u')
			->where('u.tipoObjeto = :tipoObjeto')
			->setParameter('tipoObjeto', $TipoObjetoEncargoSCF)
			->getQuery()->getResult();

		foreach ($ObjetosEncargoAll as $ObjetosEncargo) {
			//CERRADOS, FINALIZADOS y CANCELADOS
			$Encargos = $EncargoRepository->createQueryBuilder('u')
				->where('u.objetoEncargo =  :objetoEncargo')
				->andWhere('u.estadoActual in (:estadoEncargo1, :estadoEncargo2, :estadoEncargo3)')
				->andWhere('u.fcEstadoActual >= :fcini and u.fcEstadoActual <= :fcfin')
				->setParameter('estadoEncargo1', $EstadoEncargoCRR)
				->setParameter('estadoEncargo2', $EstadoEncargoFIN)
				->setParameter('estadoEncargo3', $EstadoEncargoCAN)
				->setParameter('objetoEncargo', $ObjetosEncargo)
				->setParameter('fcini', $CertificadoServicios->getMes()->getFechaInicio())
				->setParameter('fcfin', $CertificadoServicios->getMes()->getFechaFin())
				->getQuery()->getResult();

			foreach ($Encargos as $Encargo) {
				$Existe = $this->encargoEnCertificado($Encargo);
				if ($Existe) {
					$ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Existe->getDescripcion());
					$ServicioLog->escribeLog($ficheroLog);
					continue;
				}

				$LineaCertificado = new LineaCertificado();
				$LineaCertificado->setCertificadoServicios($CertificadoServicios);
				$LineaCertificado->setTipoCuota($TipoCuota);
				$LineaCertificado->setEncargo($Encargo);
				$this->getDoctrine()->getManager()->persist($LineaCertificado);
				$this->getDoctrine()->getManager()->flush();
				$ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO ");
				$ServicioLog->escribeLog($ficheroLog);

//				if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
//					if ($Encargo->getFcEntrega() > $Encargo->getFcRequeridaEntrega()->add(new \DateInterval(('P1D')))) {
//						//$fecha = $Encargo->getFcRequeridaEntrega()->diff($Encargo->getFcEntrega());
//						$EncargoPenalizado = new EncargoPenalizado();
//						$EncargoPenalizado->setMes($CertificadoServicios->getMes());
//						$EncargoPenalizado->setIndicador($IndicadorIRS03);
//						$EncargoPenalizado->setEncargo($Encargo);
//						$EntityManager->persist($EncargoPenalizado);
//						$EntityManager->flush();
//      				$ServicioLog->setMensaje("+Encargo: ". $Encargo->getNumero(). " *** PENALIZADO *** ");
//		        		$ServicioLog->escribeLog($ficheroLog);
//					}
//				}

				$Encargo->setBloqueado(true);
				$this->getDoctrine()->getManager()->persist($Encargo);
				$this->getDoctrine()->getManager()->flush();
			}
		}

		return true;
	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @param \AppBundle\Servicios\EscribeLog        $ServicioLog
	 * @param FicheroLog                             $ficheroLog
	 * @return bool
	 * @throws \Exception
	 */
	public
	function incluirPLA($CertificadoServicios, $ServicioLog, $ficheroLog)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoObjetoEncargoPLA = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(2);
		$TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(2);


		$ObjetoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:ObjetoEncargo");
		$EncargoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo");
		$EstadoEncargoCRR = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(2);
		$EstadoEncargoFIN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(12);
		$EstadoEncargoCAN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(10);

		$IndicadorIRS03 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(3);

		$ObjetosEncargoAll = $ObjetoRepository->createQueryBuilder('u')
			->where('u.tipoObjeto = :tipoObjeto')
			->setParameter('tipoObjeto', $TipoObjetoEncargoPLA)
			->getQuery()->getResult();


		foreach ($ObjetosEncargoAll as $ObjetosEncargo) {

			//CERRADOS, FINALIZADOS y CANCELADOS
			$Encargos = $EncargoRepository->createQueryBuilder('u')
				->where('u.objetoEncargo =  :objetoEncargo')
				->andWhere('u.estadoActual in (:estadoEncargo1, :estadoEncargo2, :estadoEncargo3)')
				->andWhere('u.fcEstadoActual >= :fcini and u.fcEstadoActual <= :fcfin')
				->setParameter('estadoEncargo1', $EstadoEncargoCRR)
				->setParameter('estadoEncargo2', $EstadoEncargoFIN)
				->setParameter('estadoEncargo3', $EstadoEncargoCAN)
				->setParameter('objetoEncargo', $ObjetosEncargo)
				->setParameter('fcini', $CertificadoServicios->getMes()->getFechaInicio())
				->setParameter('fcfin', $CertificadoServicios->getMes()->getFechaFin())
				->getQuery()->getResult();

			foreach ($Encargos as $Encargo) {
				$Existe = $this->encargoEnCertificado($Encargo);
				if ($Existe) {
					$ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Existe->getTitulo());
					$ServicioLog->escribeLog($ficheroLog);
					continue;
				}

				if (is_null($Encargo->getAgrupacion())) {
					$ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " ERROR ENCARGO SIN AGRUPACIÓN " . $Encargo->getTitulo());
					$ServicioLog->escribeLog($ficheroLog);
					continue;
				}
				if ($Encargo->getAgrupacion()->getTipoAgrupacion()->getId() == 2) {
					$LineaCertificado = new LineaCertificado();
					$LineaCertificado->setCertificadoServicios($CertificadoServicios);
					$LineaCertificado->setTipoCuota($TipoCuota);
					$LineaCertificado->setEncargo($Encargo);
					$this->getDoctrine()->getManager()->persist($LineaCertificado);
					$this->getDoctrine()->getManager()->flush();
					$ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO ");
					$ServicioLog->escribeLog($ficheroLog);

					if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
						if ($Encargo->getFcEntrega() > $Encargo->getFcRequeridaEntrega()->add(new \DateInterval(('P1D')))) {
							//$fecha = $Encargo->getFcRequeridaEntrega()->diff($Encargo->getFcEntrega());
							$EncargoPenalizado = new EncargoPenalizado();
							$EncargoPenalizado->setMes($CertificadoServicios->getMes());
							$EncargoPenalizado->setIndicador($IndicadorIRS03);
							$EncargoPenalizado->setEncargo($Encargo);
							$EntityManager->persist($EncargoPenalizado);
							$EntityManager->flush();
							$ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " *** PENALIZADO *** ");
							$ServicioLog->escribeLog($ficheroLog);
						}
					}
				}

				$Encargo->setBloqueado(true);
				$this->getDoctrine()->getManager()->persist($Encargo);
				$this->getDoctrine()->getManager()->flush();
			}
		}

		return true;
	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @param \AppBundle\Servicios\EscribeLog        $ServicioLog
	 * @param FicheroLog                             $ficheroLog
	 */
	public
	function incluirTAS($CertificadoServicios, $ServicioLog, $ficheroLog)
	{
		//$TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(3);

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param                                           $id
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public
	function queryEncargosAction(Request $request, $id)
	{

		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(\AppBundle\Datatables\LineaCertificadoDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$qb = $datatableQueryBuilder->getQb();
			$qb->andWhere('certificadoServicios = :certificadoServicios');
			$qb->setParameter('certificadoServicios', $CertificadoServicios);
			return $responseService->getResponse();
		}

		return $this->render('certificadoServicios/queryEncargos.html.twig', array(
			'datatable' => $datatable,
			'certificado' => $CertificadoServicios
		));
	}

	public
	function excluirEncargoAction($lineaCertificado_id)
	{
		$LineaCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:LineaCertificado")->find($lineaCertificado_id);

		$EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(4);
		$CertificadoServicios = $LineaCertificado->getCertificadoServicios();
		$CertificadoServicios->setEstadoCertificado($EstadoCertificado);
		$CertificadoServicios->setTotalFactura(0);
		$CertificadoServicios->setCuotaIva(0);
		$CertificadoServicios->setTotalFacturaConIva(0);
		$CertificadoServicios->setTotalPenalizaciones(0);
		$CertificadoServicios->setMaximoPenalizaciones(0);
		$CertificadoServicios->setPenalizacionAplicable(0);


		$this->getDoctrine()->getManager()->persist($CertificadoServicios);
		$this->getDoctrine()->getManager()->flush();

		$Encargo = $LineaCertificado->getEncargo();
		$Encargo->setBloqueado(null);

		$this->getDoctrine()->getManager()->persist($Encargo);
		$this->getDoctrine()->getManager()->flush();

		$this->getDoctrine()->getManager()->remove($LineaCertificado);
		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute("queryCertificadoServicios");
	}

	/**
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public
	function cerrarAction($id)
	{
		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

		$EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(3);
		$CertificadoServicios->setEstadoCertificado($EstadoCertificado);

		$this->getDoctrine()->getManager()->persist($CertificadoServicios);
		$this->getDoctrine()->getManager()->flush();

		$status = "CERTIFICADO DE SERVICIO CERRADO CORRECTAMENTE ";
		$this->sesion->getFlashBag()->add("status", $status);

		return $this->redirectToRoute("queryCertificadoServicios");
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param                                           $id
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public
	function incluirEncargoAction(Request $request, $id)
	{
		$EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);

		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

		$form = $this->createForm(AddEncargoType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$Encargo = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo")->findOneBy(["numero" => $form->getNormData("numeroEncargo")]);
			if ($Encargo) {
				$LineaCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:LineaCertificado")->findOneBy(["encargo" => $Encargo]);
				if ($LineaCertificado) {
					$status = "NUMERO DE ENCARGO YA INCLUIDO EN UNA CERTIFICACIÓN DE SERVICIOS: " . $LineaCertificado->getCertificadoServicios()->getDescripcion();
					$this->sesion->getFlashBag()->add("status", $status);
				} else {
					$LineaCertificado = new LineaCertificado();
					$LineaCertificado->setEncargo($Encargo);
					$LineaCertificado->setCertificadoServicios($CertificadoServicios);
					$LineaCertificado->setTipoCuota($Encargo->getObjetoEncargo()->getTipoCuota());
					$this->getDoctrine()->getManager()->persist($LineaCertificado);
					$this->getDoctrine()->getManager()->flush();
					$CertificadoServicios->setEstadoCertificado($EstadoCertificado);
					$this->getDoctrine()->getManager()->persist($CertificadoServicios);
					$this->getDoctrine()->getManager()->flush();
					$status = "NUMERO DE ENCARGO INCLUIDO CORRECTAMENTE EN LA CERTIFICACIÓN DE SERVICIOS GENERE DE NUEVO LOS IMPORTES ";
					$this->sesion->getFlashBag()->add("status", $status);
					return $this->redirectToRoute("queryCertificadoServicios");
				}
			} else {
				$status = "NUMERO DE ENCARGO INEXISTENTE ";
				$this->sesion->getFlashBag()->add("status", $status);
			}
		}

		return $this->render('certificadoServicios/addEncargos.html.twig', array(
			'form' => $form->createView(),
			'certificado' => $CertificadoServicios
		));

	}


}
