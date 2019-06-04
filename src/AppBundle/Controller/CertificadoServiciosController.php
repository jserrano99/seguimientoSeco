<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CertificadoServicios;
use AppBundle\Entity\EncargoPenalizado;
use AppBundle\Entity\Filtro;
use AppBundle\Entity\ImportesCertificado;
use AppBundle\Entity\LineaCertificado;
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


	public function editAction($id)
	{

		$CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

		$this->generarCertificado($CertificadoServicios);
		$status = " CERTIFICADO DE SERVICIO GENERADO CORRECTAMENTE ";
		$this->sesion->getFlashBag()->add("status", $status);
		return $this->redirectToRoute("queryCertificadoServicios");

	}

	public function addAction(Request $request)
	{

		$Filtro = new Filtro();
		$filtroForm = $this->createForm(CertificadoServiciosType::class, $Filtro);
		$filtroForm->handleRequest($request);
		if ($filtroForm->isSubmitted()) {
			$CertificadoServiciosAll = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->findByMes($Filtro->getMes());
			if ($CertificadoServiciosAll) {
				$CertificadoServicios = $CertificadoServiciosAll[0];
				if ($CertificadoServicios) {
					$status = " YA EXISTE UN CERTIFICADO PARA ESTE MES ";
					$this->sesion->getFlashBag()->add("status", $status);
					return $this->render("certificadoServicios/edit.html.twig", array(
						"form" => $filtroForm->createView(),
						"accion" => "GENERAR"));
				}
			} else {
				$CertificadoServicios = new CertificadoServicios();
				$EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);
				$Contrato = $this->getDoctrine()->getManager()->getRepository("AppBundle:Contrato")->find(1);
				$CertificadoServicios->setEstadoCertificado($EstadoCertificado);
				$CertificadoServicios->setMes($Filtro->getMes());
				$CertificadoServicios->setContrato($Contrato);
				$CertificadoServicios->setDescripcion("Certificado de Servicios " . $Filtro->getMes()->getDescripcion() . ", " . $Filtro->getAnyo()->getDescripcion());
				$this->getDoctrine()->getManager()->persist($CertificadoServicios);
				$this->getDoctrine()->getManager()->flush();
			}

			$this->generarCertificado($CertificadoServicios);
			$status = " CERTIFICADO DE SERVICIO GENERADO CORRECTAMENTE ";
			$this->sesion->getFlashBag()->add("status", $status);
			return $this->redirectToRoute("queryCertificadoServicios");

		}

		return $this->render("certificadoServicios/edit.html.twig", array(
			"form" => $filtroForm->createView(),
			"accion" => "GENERAR"
		));


	}

	public function deleteAction($id)
	{

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

		/**
		 * SERVICIOS INCLUIDOS EN LA CUOTA FIJA
		 */

		$this->incluirNPL($CertificadoServicios);
		$this->incluirADM($CertificadoServicios);
		$this->incluirSCF($CertificadoServicios);

		/**
		 * SERVICIOS INCLUIDOS EN LA CUOTA VARIABLE
		 */

		$this->incluirPLA($CertificadoServicios);

		/**
		 * GENERAR LOS IMPORTES
		 */

		$this->generarImportes($CertificadoServicios);


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

		$importeCuotaFija = $this->importesCuotaFija($CertificadoServicios);
		$importeCuotaVariable = $this->importesCuotaVariable($CertificadoServicios);
		$importeCuotaTasada = $this->importesCuotaTasada($CertificadoServicios);


		$totalFactura = $importeCuotaFija + $importeCuotaVariable + $importeCuotaTasada;
		$totalPenalizaciones = 0.0;
		$maximoPenalizaciones = $totalFactura * 0.20;
		$penalizacionAplicable = min($maximoPenalizaciones, $totalPenalizaciones);
		$cuotaIVA = $totalFactura * 0.21;
		$totalFacturaIVA = $totalFactura + $cuotaIVA - $penalizacionAplicable;
		$CertificadoServicios->setTotalFactura($totalFactura);
		$CertificadoServicios->setTotalPenalizaciones($totalPenalizaciones);
		$CertificadoServicios->setMaximoPenalizaciones($maximoPenalizaciones);
		$CertificadoServicios->setCuotaIva($cuotaIVA);
		$CertificadoServicios->setPenalizacionAplicable($penalizacionAplicable);
		$CertificadoServicios->setTotalFacturaConIva($totalFacturaIVA);

		$this->getDoctrine()->getManager()->persist($CertificadoServicios);
		$this->getDoctrine()->getManager()->flush();

		dump($CertificadoServicios);
		die();

		return true;
	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @return float
	 */
	public function importesCuotaTasada($CertificadoServicios)
	{
		return 0.0;
	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @return float
	 */
	public function importesCuotaVariable($CertificadoServicios)
	{
		return 0.0;
	}

	/**
	 * @param \AppBundle\Entity\CertificadoServicios $CertificadoServicios
	 * @return bool
	 */
	public function importesCuotaFija($CertificadoServicios)
	{
		$entityManager = $this->getDoctrine()->getManager();

		$ImportesContratoRepo = $entityManager->getRepository("AppBundle:ImportesContrato");

		$ImportesContrato = $ImportesContratoRepo->createQueryBuilder('u')
			->where('u.contrato = :contrato')
			->setParameter('contrato', $CertificadoServicios->getContrato())
			->getQuery()->getResult();

		$ImportesContrato = $ImportesContrato[0];

		$ImportesCertificado = new ImportesCertificado();
		$ImportesCertificado->setCertificadoServicios($CertificadoServicios);
		$ImportesCertificado->setCodigo(1);
		$ImportesCertificado->setDescripcion("Servicio de Atención, Soporte, Mantenimiento y Evolución de Corto Alcance");
		$ImportesCertificado->setHorasCertificadas(null);
		$ImportesCertificado->setTarifa(null);
		$ImportesCertificado->setImporte($ImportesContrato->getCuotaFijaMensual());
		$ImportesCertificado->setPenalizacion(null);
		$ImportesCertificado->setTotal($ImportesContrato->getCuotaFijaMensual());

		$entityManager->persist($ImportesCertificado);
		$entityManager->flush();


		return $ImportesCertificado->getImporte();
	}


	/**
	 * NO PLANIFICABLE
	 *
	 * @param $CertificadoServicios
	 * @return bool
	 */
	public function incluirNPL($CertificadoServicios)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoObjetoEncargoNPL = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(1);

		$ObjetoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:ObjetoEncargo");
		$EncargoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo");
		$EstadoEncargoCRR = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(2);
		$EstadoEncargoFIN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(12);
		$EstadoEncargoCAN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(10);

		$IndicadorIRS01 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(1);
		$IndicadorIRS02 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(2);

		$ObjetosEncargoNPLAll = $ObjetoRepository->createQueryBuilder('u')
			->where('u.tipoObjeto = :tipoObjeto')
			->setParameter('tipoObjeto', $TipoObjetoEncargoNPL)
			->getQuery()->getResult();

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
				$LineaCertificado = new LineaCertificado();
				$LineaCertificado->setCertificadoServicios($CertificadoServicios);
				$LineaCertificado->setEncargo($Encargo);
				$this->getDoctrine()->getManager()->persist($LineaCertificado);
				$this->getDoctrine()->getManager()->flush();

				/**
				 * Si el encargo esta cancelado solo se tiene en cuenta para la imputación de horas, no en el calculo
				 * de ANS
				 */
				if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
					/**
					 * Calculo de las penalizaciones
					 */
					if ($Encargo->getCriticidad() == 1 and $Encargo->getTiempoResolucion() > 14400000) {
						$EncargoPenalizado = new EncargoPenalizado();
						$EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
						$EncargoPenalizado->setIndicador($IndicadorIRS01);
						$EncargoPenalizado->setEncargo($Encargo);
						$EntityManager->persist($EncargoPenalizado);
						$EntityManager->flush();
					}
					if ($Encargo->getCriticidad() == 0 and $Encargo->getTiempoResolucion() > 144000000) {
						$EncargoPenalizado = new EncargoPenalizado();
						$EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
						$EncargoPenalizado->setIndicador($IndicadorIRS02);
						$EncargoPenalizado->setEncargo($Encargo);
						$EntityManager->persist($EncargoPenalizado);
						$EntityManager->flush();
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
	 * @param $CertificadoServicios
	 * @return bool
	 * @throws \Exception
	 */
	public function incluirADM($CertificadoServicios)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoObjetoEncargoADM = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(3);

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


			foreach ($Encargos as $Encargo) {
				$LineaCertificado = new LineaCertificado();
				$LineaCertificado->setCertificadoServicios($CertificadoServicios);
				$LineaCertificado->setEncargo($Encargo);
				$this->getDoctrine()->getManager()->persist($LineaCertificado);
				$this->getDoctrine()->getManager()->flush();

				if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
					if ($Encargo->getFcEntrega() > $Encargo->getFcRequeridaEntrega()->add(new \DateInterval(('P1D')))) {
						//$fecha = $Encargo->getFcRequeridaEntrega()->diff($Encargo->getFcEntrega());
						$EncargoPenalizado = new EncargoPenalizado();
						$EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
						$EncargoPenalizado->setIndicador($IndicadorIRS03);
						$EncargoPenalizado->setEncargo($Encargo);
						$EntityManager->persist($EncargoPenalizado);
						$EntityManager->flush();
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
	 * @param $CertificadoServicios
	 * @return bool
	 */
	public function incluirSCF($CertificadoServicios)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoObjetoEncargoSCF = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(4);

		$ObjetoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:ObjetoEncargo");
		$EncargoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo");
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
				$LineaCertificado = new LineaCertificado();
				$LineaCertificado->setCertificadoServicios($CertificadoServicios);
				$LineaCertificado->setEncargo($Encargo);
				$this->getDoctrine()->getManager()->persist($LineaCertificado);
				$this->getDoctrine()->getManager()->flush();

//				if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
//					if ($Encargo->getFcEntrega() > $Encargo->getFcRequeridaEntrega()->add(new \DateInterval(('P1D')))) {
//						//$fecha = $Encargo->getFcRequeridaEntrega()->diff($Encargo->getFcEntrega());
//						$EncargoPenalizado = new EncargoPenalizado();
//						$EncargoPenalizado->setMes($CertificadoServicios->getMes());
//						$EncargoPenalizado->setIndicador($IndicadorIRS03);
//						$EncargoPenalizado->setEncargo($Encargo);
//						$EntityManager->persist($EncargoPenalizado);
//						$EntityManager->flush();
//					}
//				}

				$Encargo->setBloqueado(true);
				$this->getDoctrine()->getManager()->persist($Encargo);
				$this->getDoctrine()->getManager()->flush();
			}
		}

		return true;
	}

	public function incluirPLA($CertificadoServicios)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$TipoObjetoEncargoPLA = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(2);

		$ObjetoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:ObjetoEncargo");
		$EncargoRepository = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo");
		$EstadoEncargoCRR = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(2);
		$EstadoEncargoFIN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(12);
		$EstadoEncargoCAN = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(10);

		//$IndicadorIRS03 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(3);

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
				$LineaCertificado = new LineaCertificado();
				$LineaCertificado->setCertificadoServicios($CertificadoServicios);
				$LineaCertificado->setEncargo($Encargo);
				$this->getDoctrine()->getManager()->persist($LineaCertificado);
				$this->getDoctrine()->getManager()->flush();

//				if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
//					if ($Encargo->getFcEntrega() > $Encargo->getFcRequeridaEntrega()->add(new \DateInterval(('P1D')))) {
//						//$fecha = $Encargo->getFcRequeridaEntrega()->diff($Encargo->getFcEntrega());
//						$EncargoPenalizado = new EncargoPenalizado();
//						$EncargoPenalizado->setMes($CertificadoServicios->getMes());
//						$EncargoPenalizado->setIndicador($IndicadorIRS03);
//						$EncargoPenalizado->setEncargo($Encargo);
//						$EntityManager->persist($EncargoPenalizado);
//						$EntityManager->flush();
//					}
//				}

				$Encargo->setBloqueado(true);
				$this->getDoctrine()->getManager()->persist($Encargo);
				$this->getDoctrine()->getManager()->flush();
			}
		}

		return true;
	}

}
