<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CargaFichero;
use AppBundle\Entity\Centro;
use AppBundle\Entity\EncargoRemedy;
use AppBundle\Entity\FicheroLog;
use AppBundle\Entity\Encargo;
use AppBundle\Entity\Fichero;
use AppBundle\Entity\Remedy;
use AppBundle\Entity\UsuarioRemedy;
use AppBundle\Form\ImportarType;
use AppBundle\Servicios\EscribeLog;
use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class CargaFicheroController extends Controller
{

	private $sesion;

	public function __construct()
	{
		$this->sesion = new Session();
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function queryAction(Request $request)
	{
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(\AppBundle\Datatables\CargaFicheroDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('cargaFichero/query.html.twig', [
				'datatable' => $datatable]
		);
	}


	/**
	 * @param $id
	 * @return Response
	 */
	public function descargaLogAction($id)
	{

		$CargaFichero = $this->getDoctrine()->getManager()->getRepository("AppBundle:CargaFichero")->find($id);

		$filename = $CargaFichero->getFicheroLog()->getNombreFichero();

		$response = new Response();
		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
		$response->headers->set('Content-Type', 'application/octet-stream');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'max-age=1');
		$response->setContent(file_get_contents($filename));

		return $response;
	}

	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 */

	public function cargaAction(Request $request)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$ImportarForm = $this->createForm(ImportarType::class);
		$ImportarForm->handleRequest($request);

		if ($ImportarForm->isSubmitted()) {
			$fichero = $ImportarForm["fichero"]->getData();
			if (!empty($fichero) && $fichero != null) {
				$file_name = $fichero->getClientOriginalName();
				$fichero->move("upload", $file_name);
				try {
					$file = "upload/" . $fichero->getClientOriginalName();
					$PHPExcel = IOFactory::load($file);
					$CargaFichero = new CargaFichero();
					$fecha = new DateTime();
					$CargaFichero->setFechaCarga($fecha);
					$CargaFichero->setDescripcion($ImportarForm["descripcion"]->getdata());
					$CargaFichero->setFichero($file_name);
					$Usuario = $this->getUser();
					$CargaFichero->setUsuario($Usuario);
					$entityManager->persist($CargaFichero);
					$entityManager->flush();

					$FicheroLog = new FicheroLog();
					$fechaProceso = new \DateTime();
					$FicheroLog->setFechaProceso($fechaProceso);

					$ServicioLog = $this->get('app.escribelog');
					$ServicioLog->setLogger('CARGA FICHERO : ID= ' . $CargaFichero->getId());
					$ficheroLog = 'FicheroLog-' . $CargaFichero->getId() . '.log';

					$ServicioLog->setMensaje("Comienza carga fichero: " . $file);
					$ServicioLog->escribeLog($ficheroLog);

					$this->cargaFichero($CargaFichero, $PHPExcel);
					$ServicioLog->setMensaje("Finalizada carga fichero: " . $file . " Registros Totales :" . $CargaFichero->getNumeroRegistros());
					$ServicioLog->escribeLog($ficheroLog);

					$this->cargaEncargos($CargaFichero, $ServicioLog, $ficheroLog);
					$ServicioLog->setMensaje("Finalizada carga encargos: " . $file . " Registros Cargados :" . $CargaFichero->getNumeroRegistrosCargados());
					$ServicioLog->escribeLog($ficheroLog);

					$FicheroLog->setNombreFichero($ServicioLog->getFileName());

					$entityManager->persist($FicheroLog);
					$entityManager->flush();


					$CargaFichero->setFicheroLog($FicheroLog);
					$entityManager->persist($CargaFichero);
					$entityManager->flush();
					$params = ["CargaFichero" => $CargaFichero];
					return $this->render("cargaFichero/finProceso.html.twig", $params);

				} catch (Exception $e) {
					$status = "***ERROR EN CARGA DE FICHERO **: " . $file_name;
					$this->sesion->getFlashBag()->add("status", $status);
					$params = "";
					return $this->render("cargaFichero/query.html.twig", $params);
				}
			}
		}
		$params = ["form" => $ImportarForm->createView()];
		return $this->render("cargaFichero/carga.html.twig", $params);
	}

	/**
	 * @param $CargaFichero
	 * @param $PHPExcel
	 * @return bool
	 * @throws \Exception
	 */
	public function cargaFichero($CargaFichero, $PHPExcel)
	{
		$entityManager = $this->getDoctrine()->getManager();

		$objWorksheet = $PHPExcel->setActiveSheetIndex(0);
		$highestRow = $objWorksheet->getHighestRow();

		$ct = 0;
		for ($i = 2; $i <= $highestRow; $i++) {
			if (!$entityManager->isOpen()) {
				$entityManager = $this->getDoctrine()->getManager()->create($entityManager->getConnection(), $entityManager->getConfiguration());
			}
			$headingsArray = $objWorksheet->rangeToArray('A' . $i . ':CG' . $i, null, true, true, true);
			$headingsArray = $headingsArray[$i];
			$Fichero = new Fichero();
			$Fichero->setCargaFichero($CargaFichero);
			$Fichero->setNumeroEncargo($headingsArray["D"]);
			$headingsArray["E"] == null ? $Fichero->setContrato("1099") : $Fichero->setContrato($headingsArray["E"]);
			$Fichero->setNumeroRemedy($headingsArray["G"]);
			$Fichero->setNumeroAgrupacion($headingsArray["I"]);
			$Fichero->setTitulo($headingsArray['L']);
			$Fichero->setDescripcion($headingsArray['L']);
			$Fichero->setObjetoEncargo($headingsArray["N"]);
			$Fichero->setCriticidad($headingsArray["Q"]);
			$Fichero->setEstadoActual($headingsArray["U"]);
			$Fichero->setFechaEstadoActual(new DateTime($headingsArray["V"]));
			$Fichero->setFechaRegistro(new DateTime($headingsArray["W"]));
			$Fichero->setFechaAsignacion(new DateTime($headingsArray["X"]));
			$headingsArray["Y"] == null ? $Fichero->setFechaEstimadaSolucion(null) : $Fichero->setFechaEstimadaSolucion(new DateTime($headingsArray["Y"]));
			$headingsArray["Z"] == null ? $Fichero->setFechaRequeridaValoracion(null) : $Fichero->setFechaRequeridaValoracion(new DateTime($headingsArray["Z"]));
			$headingsArray["AA"] == null ? $Fichero->setFechaRequeridaEntrega(null) : $Fichero->setFechaRequeridaEntrega(new DateTime($headingsArray["AA"]));
			$headingsArray["AB"] == null ? $Fichero->setFechaEntregaValoracion(null) : $Fichero->setFechaEntregaValoracion(new DateTime($headingsArray["AB"]));
			$headingsArray["AC"] == null ? $Fichero->setFechaCompromiso(null) : $Fichero->setFechaCompromiso(new DateTime($headingsArray["AC"]));
			$headingsArray["AD"] == null ? $Fichero->setFechaFinPrevista(null) : $Fichero->setFechaFinPrevista(new DateTime($headingsArray["AD"]));
			$headingsArray["AE"] == null ? $Fichero->setFechaComienzoEjecucion(null) : $Fichero->setFechaComienzoEjecucion(new DateTime($headingsArray["AE"]));
			$headingsArray["AF"] == null ? $Fichero->setFechaEntrega(null) : $Fichero->setFechaEntrega(new DateTime($headingsArray["AF"]));
			$headingsArray["AG"] == null ? $Fichero->setFechaResolucionIcm(null) : $Fichero->setFechaResolucionIcm(new DateTime($headingsArray["AG"]));
			$headingsArray["AH"] == null ? $Fichero->setFechaAceptacion(null) : $Fichero->setFechaAceptacion(new DateTime($headingsArray["AH"]));
			$headingsArray["AI"] == null ? $Fichero->setFechaAceptacion(null) : $Fichero->setFechaCierre(new DateTime($headingsArray["AI"]));
			$headingsArray["AJ"] == null ? $Fichero->setTiempoTotal(0) : $Fichero->setTiempoTotal($headingsArray["AJ"]);
			$headingsArray["AK"] == null ? $Fichero->setTiempoResolucion(0) : $Fichero->setTiempoResolucion($headingsArray["AK"]);

			$Fichero->setAplicacion($headingsArray["AL"]);
			$Fichero->setModuloFuncional($headingsArray["AM"]);
			$Fichero->setModuloTecnico($headingsArray["AN"]);
			$headingsArray["AO"] == null ? $Fichero->setHorasValoradas(0) : $Fichero->setHorasValoradas($headingsArray["AO"]);
			$headingsArray["AP"] == null ? $Fichero->setHorasComprometidas(0) : $Fichero->setHorasComprometidas($headingsArray["AP"]);
			$headingsArray["AQ"] == null ? $Fichero->setHorasRealizadas(0) : $Fichero->setHorasRealizadas($headingsArray["AQ"]);
			$headingsArray["AR"] == null ? $Fichero->setCoste(0) : $Fichero->setCoste($headingsArray["AR"]);

			$Fichero->setTipoSolucion($headingsArray["BY"]);
			$Fichero->setSolucionUsuario($headingsArray["BZ"]);
			$Fichero->setSolucionTecnica($headingsArray["BY"]);
			$Fichero->setOperacional1($headingsArray["CB"]);
			$Fichero->setOperacional2($headingsArray["CC"]);
			$Fichero->setOperacional3($headingsArray["CD"]);
			$Fichero->setMotivoCancelacion($headingsArray["BC"]);

			$entityManager->persist($Fichero);
			$entityManager->flush();
			$ct++;

		}

		$CargaFichero->setNumeroRegistros($ct);
		$entityManager->persist($CargaFichero);
		$entityManager->flush();

		return true;
	}

	public function recargaAction($id)
	{
		$CargaFichero = $this->getDoctrine()->getManager()->getRepository("AppBundle:CargaFichero")->find($id);

		$this->cargaEncargos($CargaFichero);
		return true;
	}

	/**
	 * @param $CargaFichero
	 * @param $ServicioLog
	 * @param $ficheroLog
	 * @return bool
	 */
	public function cargaEncargos($CargaFichero, $ServicioLog, $ficheroLog)
	{
		$entityManager = $this->getDoctrine()->getManager();

		$FicheroAll = $this->getDoctrine()->getManager()
			->getRepository("AppBundle:Fichero")->findBy(["cargaFichero" => $CargaFichero]);

		$ct = 0;
		foreach ($FicheroAll as $Fichero) {
			$Encargo = $entityManager->getRepository("AppBundle:Encargo")->findByNumero($Fichero->getNumeroEncargo());

			if (!$Encargo) {
				$Encargo = new Encargo();
			}

			if ($Encargo->isBloqueado()) {
				$ServicioLog->setMensaje(" **** Encargo= " . $Encargo->getNumero() . " Bloqueado ");
				$ServicioLog->escribeLog($ficheroLog);
				continue;
			}

			$Encargo->setNumero($Fichero->getNumeroEncargo());
			$Encargo->setNmRemedy($Fichero->getNumeroRemedy());
			$ObjetoEncargo = $entityManager->getRepository("AppBundle:ObjetoEncargo")->findByCodigo($Fichero->getObjetoEncargo());
			$Encargo->setObjetoEncargo($ObjetoEncargo);

			if ($Fichero->getContrato() == null)
				$Contrato = $entityManager->getRepository("AppBundle:Contrato")->findByCodigo('1099');
			else
				$Contrato = $entityManager->getRepository("AppBundle:Contrato")->findByCodigo($Fichero->getContrato());
			$Encargo->setContrato($Contrato);

			$Encargo->setNmRemedy($Fichero->getNumeroRemedy());
			$Agrupacion = $entityManager->getRepository("AppBundle:Agrupacion")->findByCodigo($Fichero->getNumeroAgrupacion());
			if (is_null($Agrupacion) and $ObjetoEncargo->getTipoObjeto()->getId() != 1) {
				$ServicioLog->setMensaje(" >>>> Encargo= " . $Encargo->getNumero() . " Agrupación inexistente: " . $Fichero->getNumeroAgrupacion());
				$ServicioLog->escribeLog($ficheroLog);
				continue;
			}

			$Encargo->setAgrupacion($Agrupacion);
			$Encargo->setTitulo($Fichero->getTitulo());
			$Encargo->setDescripcion($Fichero->getDescripcion());
			$EstadoActual = $entityManager->getRepository("AppBundle:EstadoEncargo")->findByCodigo($Fichero->getEstadoActual());
			$Encargo->setEstadoActual($EstadoActual);

			$Encargo->setFcEstadoActual($Fichero->getFechaEstadoActual());
			$Encargo->setFcRegistro($Fichero->getFechaRegistro());
			$Encargo->setFcAsignacion($Fichero->getFechaAsignacion());
			$Encargo->setFcEstimadaSolucion($Fichero->getFechaEstimadaSolucion());
			$Encargo->setFcRequeridaValoracion($Fichero->getFechaRequeridaValoracion());
			$Encargo->setFcRequeridaEntrega($Fichero->getFechaRequeridaEntrega());
			$Encargo->setFcEntregaValoracion($Fichero->getFechaEntregaValoracion());
			$Encargo->setFcCompromiso($Fichero->getFechaCompromiso());
			$Encargo->setFcFinPrevista($Fichero->getFechaFinPrevista());
			$Encargo->setFcComienzoEjecucion($Fichero->getFechaComienzoEjecucion());
			$Encargo->setFcEntrega($Fichero->getFechaEntrega());
			$Encargo->setFcResolucionIcm($Fichero->getFechaResolucionIcm());
			$Encargo->setFcAceptacion($Fichero->getFechaAceptacion());
			$Encargo->setFcCierre($Fichero->getFechaCierre());


			$Encargo->setCriticidad($Fichero->getCriticidad());
			$Criticidad = $entityManager->getRepository("AppBundle:Criticidad")->findOneBy(["codigo" => $Fichero->getCriticidad()]);
			$Encargo->setCriticidad2($Criticidad);

			$Encargo->setTiempoTotal($Fichero->getTiempoTotal());
			$Encargo->setTiempoResolucion($Fichero->getTiempoResolucion());

			$Aplicacion = $entityManager->getRepository("AppBundle:Aplicacion")->findByCodigo($Fichero->getAplicacion());
			$Encargo->setAplicacion($Aplicacion);

			$ModuloFuncional = $entityManager->getRepository("AppBundle:ModuloFuncional")->findByCodigo($Fichero->getModuloFuncional());
			$Encargo->setModuloFuncional($ModuloFuncional);

			$ModuloTecnico = $entityManager->getRepository("AppBundle:ModuloTecnico")->findByCodigo($Fichero->getModuloTecnico());
			$Encargo->setModuloTecnico($ModuloTecnico);
			$Encargo->setHorasValoradas($Fichero->getHorasValoradas());
			$Encargo->setHorasComprometidas($Fichero->getHorasComprometidas());
			$Encargo->setHorasRealizadas($Fichero->getHorasRealizadas());
			if ($Encargo->getObjetoEncargo()->getTipoCuota()->getId() == 2) {
				$Encargo->setCoste(round($Encargo->getHorasComprometidas() * 37.42, 2));
            }
			if ($Encargo->getObjetoEncargo()->getTipoCuota()->getId() == 3) {
				$Encargo->getCoste() > 0 ? null : $Encargo->setCoste(0);
			}
			$TipoSolucion = $entityManager->getRepository("AppBundle:TipoSolucion")->findByCodigo($Fichero->getTipoSolucion());
			$Encargo->setTipoSolucion($TipoSolucion);
			$Encargo->setSolucionUsuario($Fichero->getSolucionUsuario());
			$Encargo->setSolucionTecnica($Fichero->getSolucionTecnica());
			$Operacional1 = $entityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional1());
			$Operacional2 = $entityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional2());
			$Operacional3 = $entityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional3());
			$Encargo->setOperacional1($Operacional1);
			$Encargo->setOperacional2($Operacional2);
			$Encargo->setOperacional3($Operacional3);
			$Encargo->setMotivoCancelacion($Fichero->getMotivoCancelacion());
			$entityManager->persist($Encargo);
			$entityManager->flush();
			$ct++;
			$ServicioLog->setMensaje(" >>>> Encargo= " . $Encargo->getNumero() . " Actualizado <<<< ");
			$ServicioLog->escribeLog($ficheroLog);
		}

		$CargaFichero->setNumeroRegistrosCargados($ct);
		$entityManager->persist($CargaFichero);
		$entityManager->flush();

		return true;
	}


	public function cargaFicheroRemedyAction(Request $request)
	{
		$entityManager = $this->getDoctrine()->getManager();
		$ImportarForm = $this->createForm(ImportarType::class);
		$ImportarForm->handleRequest($request);


		if ($ImportarForm->isSubmitted()) {
			$fichero = $ImportarForm["fichero"]->getData();
			if (!empty($fichero) && $fichero != null) {
				$file_name = $fichero->getClientOriginalName();
				$fichero->move("upload", $file_name);
				try {

					$file = "upload/" . $fichero->getClientOriginalName();
					$PHPExcel = IOFactory::load($file);
					$CargaFichero = new CargaFichero();
					$fecha = new DateTime();
					$CargaFichero->setFechaCarga($fecha);
					$CargaFichero->setDescripcion($ImportarForm["descripcion"]->getdata());
					$CargaFichero->setFichero($file_name);
					$Usuario = $this->getUser();
					$CargaFichero->setUsuario($Usuario);
					$entityManager->persist($CargaFichero);
					$entityManager->flush();

					$FicheroLog = new FicheroLog();
					$fechaProceso = new \DateTime();
					$FicheroLog->setFechaProceso($fechaProceso);

					$ServicioLog = $this->get('app.escribelog');
					$ServicioLog->setLogger('CARGA FICHERO Remedy: ID= ' . $CargaFichero->getId());
					$ficheroLog = 'FicheroLog-' . $CargaFichero->getId() . ".log";

					$ServicioLog->setMensaje("Comienza carga fichero remedy: " . $file);
					$ServicioLog->escribeLog($ficheroLog);

					$this->cargaRemedy($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog);
					$ServicioLog->setMensaje("Finaliza carga fichero remedy: " . $file . " Registros Totales :" . $CargaFichero->getNumeroRegistros());
					$ServicioLog->escribeLog($ficheroLog);

					$CargaFichero->setFicheroLog($FicheroLog);
					$entityManager->persist($CargaFichero);
					$entityManager->flush();
					$params = ["CargaFichero" => $CargaFichero];
					return $this->render("cargaFichero/finProceso.html.twig", $params);

				} catch (Exception $e) {
					$status = "***ERROR EN CARGA DE FICHERO **: " . $file_name;
					$this->sesion->getFlashBag()->add("status", $status);
					$params = "";
					return $this->render("cargaFichero/query.html.twig", $params);
				}
			}
		}
		$params = ["form" => $ImportarForm->createView()];
		return $this->render("cargaFichero/carga.html.twig", $params);
	}

	/**
	 * @param CargaFichero $CargaFichero
	 * @param $PHPExcel
	 * @param EscribeLog $ServicioLog
	 * @param  $ficheroLog
	 * @return bool
	 */
	public function cargaRemedy($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog)
	{
		$entityManager = $this->getDoctrine()->getManager();

		$objWorksheet = $PHPExcel->setActiveSheetIndex(0);
		$highestRow = $objWorksheet->getHighestRow();

		$ct = 0;
		for ($i = 2; $i <= $highestRow; $i++) {
			if (!$entityManager->isOpen()) {
				$entityManager = $this->getDoctrine()->getManager()->create($entityManager->getConnection(), $entityManager->getConfiguration());
			}
			$headingsArray = $objWorksheet->rangeToArray('A' . $i . ':Q' . $i, null, true, true, true);
			$headingsArray = $headingsArray[$i];

			$Remedy = $entityManager->getRepository("AppBundle:Remedy")->findOneBy(["numero" => $headingsArray["B"]]);

			if (is_null($Remedy)) {
				$Remedy = new Remedy();
			}

			$Aplicacion = $entityManager->getRepository("AppBundle:Aplicacion")->findOneBy(["codigo" => $headingsArray["J"]]);

			if (is_null($Aplicacion)) {
				$ServicioLog->setMensaje(" **** Ticket :  " . $headingsArray["B"] . " aplicación no tratada : " . $headingsArray["J"]);
				$ServicioLog->escribeLog($ficheroLog);
				continue;
			}

			$Centro = $entityManager->getRepository("AppBundle:Centro")->findOneBy(["descripcion" => $headingsArray["G"]]);
			if (is_null($Centro)) {
				$Centro = new Centro();
				$Centro->setDescripcion($headingsArray["G"]);
				$entityManager->persist($Centro);
				$entityManager->flush();
				$ServicioLog->setMensaje(" **** Generado Centro= " . $Centro->getDescripcion());
				$ServicioLog->escribeLog($ficheroLog);
			}

			$UsuarioRemedy = $entityManager->getRepository("AppBundle:UsuarioRemedy")->findOneBy(["login" => $headingsArray["N"]]);

			if (is_null($UsuarioRemedy)) {
				$UsuarioRemedy = $entityManager->getRepository("AppBundle:UsuarioRemedy")->findOneBy(["apellidos" => $headingsArray["H"]]);
				if (is_null($UsuarioRemedy)) {
					$UsuarioRemedy = new UsuarioRemedy();
					$UsuarioRemedy->setLogin($headingsArray["N"]);
					$UsuarioRemedy->setApellidos($headingsArray["H"]);
					$UsuarioRemedy->setNombre($headingsArray["I"]);
					$UsuarioRemedy->setCentro($Centro);
					$entityManager->persist($UsuarioRemedy);
					$entityManager->flush();
					$ServicioLog->setMensaje(" **** Generado Usuario login= " . $UsuarioRemedy->getLogin() .
						" Nombre = " . $UsuarioRemedy->getApellidos() . ", " . $UsuarioRemedy->getNombre() .
						" Centro " . $Centro->getDescripcion());
					$ServicioLog->escribeLog($ficheroLog);
				}
			}

			$Remedy->setAplicacion($Aplicacion);
			$Remedy->setTipo($headingsArray["A"]);
			$Remedy->setNumero($headingsArray["B"]);
			$Remedy->setEstado($headingsArray["C"]);
			$Remedy->setCriticidad($headingsArray["E"]);
			$Remedy->setArea($headingsArray["F"]);
			$Remedy->setUsuarioRemedy($UsuarioRemedy);
			$Remedy->setCentro($Centro);
			$Remedy->setApellidos($headingsArray["H"]);
			$Remedy->setNombre($headingsArray["I"]);
			$Remedy->setProductoNivel3($headingsArray["J"]);
			$Remedy->setProductoNivel4($headingsArray["K"]);
			$Remedy->setFechaModificacion(DateTime::createFromFormat('d/m/Y H:i:s', $headingsArray["L"]));

			$MesAll = $entityManager->getRepository("AppBundle:Mes")
				->createQueryBuilder('u')
				->where(':fecha between u.fechaInicio and u.fechaFin')
				->setParameter('fecha', $Remedy->getFechaModificacion())
				->getQuery()->getResult();
			$Mes = $MesAll[0];


			$headingsArray["P"] == null ? $Remedy->setFechaCierre(null) : $Remedy->setFechaCierre(DateTime::createFromFormat('d/m/Y H:i:s', $headingsArray["P"]));

			$Remedy->setLogin($headingsArray["N"]);
			$Remedy->setDescripcionProblema($headingsArray["O"]);
			$Remedy->setMes($Mes);
			$entityManager->persist($Remedy);
			$entityManager->flush();
			$ServicioLog->setMensaje(" **** Generado Ticket . " . $Remedy->getNumero() . " Estado: " . $Remedy->getEstado());
			$ServicioLog->escribeLog($ficheroLog);
			$ct++;

			$EncargoAll = $entityManager->getRepository("AppBundle:Encargo")->findBy(["nmRemedy" => $Remedy->getNumero()]);

			foreach ($EncargoAll as $Encargo) {
				$EncargoRemedy = $entityManager->getRepository("AppBundle:EncargoRemedy")->findOneBy(["encargo" => $Encargo, "remedy" => $Remedy]);
				if (is_null($EncargoRemedy)) {
					$EncargoRemedy = new EncargoRemedy();
					$EncargoRemedy->setEncargo($Encargo);
					$EncargoRemedy->setRemedy($Remedy);
					$entityManager->persist($EncargoRemedy);
					$entityManager->flush();
				}
			}

		}

		$CargaFichero->setNumeroRegistros($ct);
		$entityManager->persist($CargaFichero);
		$entityManager->flush();
		return true;
	}

}