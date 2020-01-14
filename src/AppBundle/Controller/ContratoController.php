<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\ContratoDatatable;
use AppBundle\Entity\Contrato;
use AppBundle\Entity\ImportesContratoAnualidad;
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
use AppBundle\Form\ContratoType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;
use AppBundle\Form\ImportesContratoAnualidadType;

/**
 * Class ContratoController
 *
 * @package AppBundle\Controller
 */
class ContratoController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * ContratoController constructor.
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
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(ContratoDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('contrato/query.html.twig', [
			'datatable' => $datatable,
		]);
	}
	/**
	 * @param Request $request
	 * @param                                           $id
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, $id)
	{

		$EntityManager = $this->getDoctrine()->getManager();
		$Contrato = $EntityManager->getRepository("AppBundle:Contrato")->find($id);
		$form = $this->createForm(ContratoType::class, $Contrato);
		$form->handleRequest($request);

		$ImportesContrato = $EntityManager->getRepository("AppBundle:ImportesContrato")->findBy(["contrato" => $Contrato]);
		$ImportesContratoAnualidad = $EntityManager->getRepository("AppBundle:ImportesContratoAnualidad")->findBy(["contrato" => $Contrato]);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Contrato);
				$this->getDoctrine()->getManager()->flush();
				$status = "CONTRATO " . $Contrato->getCodigo() . " MODIFICADO CORRECTAMENTE";
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryContrato");
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA CONTRATO CON ESTE CÓDIGO: " . $Contrato->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryContrato");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryContrato");
			}
		}

		$params = ["contrato" => $Contrato,
			"accion" => "MODIFICACIÓN",
			'ImportesContrato' => $ImportesContrato,
			'ImportesContratoAnualidad' => $ImportesContratoAnualidad,
			"form" => $form->createView()];
		return $this->render("contrato/edit.html.twig", $params);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{

		$Contrato = new Contrato();

		$form = $this->createForm(ContratoType::class, $Contrato);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Contrato);
				$this->getDoctrine()->getManager()->flush();
				return $this->redirectToRoute('queryContrato');
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA CONTRATO CON ESTE CÓDIGO: " . $Contrato->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryContrato");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryContrato");
			}
		}

		$params = ["contrato" => $Contrato,
			"accion" => "CREACIÓN",
			"form" => $form->createView()];
		return $this->render("contrato/edit.html.twig", $params);
	}

	/**
	 * @param Request $request
	 * @param                                           $id
	 * @return RedirectResponse|Response
	 */
	public function editImporteAnualidadAction(Request $request, $id)
	{

		$EntityManager = $this->getDoctrine()->getManager();
		/** @var ImportesContratoAnualidad  $ImportesContratoAnualidad */
		$ImportesContratoAnualidad = $EntityManager->getRepository("AppBundle:ImportesContratoAnualidad")->find($id);
		$Contrato = $ImportesContratoAnualidad->getContrato();

		$form = $this->createForm(ImportesContratoAnualidadType::class, $ImportesContratoAnualidad);
		$form->handleRequest($request);


		if ($form->isSubmitted()) {
			try {
				$EntityManager->persist($ImportesContratoAnualidad);
				$EntityManager->flush();
				$status = "IMPORTE MODIFICADO CORRECTAMENTE";
				$this->sesion->getFlashBag()->add("status", $status);
				$params = ["id" => $ImportesContratoAnualidad->getContrato()->getId()];
				return $this->redirectToRoute("editContrato",$params);
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				$params = ["id" => $ImportesContratoAnualidad->getContrato()->getId()];
				return $this->redirectToRoute("editContrato",$params);
			}
		}

		$params = ["contrato" => $Contrato,
			"accion" => "MODIFICACIÓN",
			'ImportesContratoAnualidad' => $ImportesContratoAnualidad,
			"form" => $form->createView()];
		return $this->render("contrato/editImporte.html.twig", $params);
	}



}
