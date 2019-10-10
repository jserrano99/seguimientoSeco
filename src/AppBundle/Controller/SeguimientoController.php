<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\SeguimientoDatatable;
use AppBundle\Entity\Seguimiento;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Form\SeguimientoType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;

/**
 * Class SeguimientoController
 *
 * @package AppBundle\Controller
 */
class SeguimientoController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * SeguimientoController constructor.
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
		$datatable = $this->get('sg_datatables.factory')->create(SeguimientoDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('seguimiento/query.html.twig', [
			'datatable' => $datatable,
		]);
	}

	/**
	 * @param Request $request
	 * @param int $id
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, $id)
	{

		$Seguimiento = $this->getDoctrine()->getManager()->getRepository("AppBundle:Seguimiento")->find($id);

		$form = $this->createForm(SeguimientoType::class, $Seguimiento);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Seguimiento);
				$this->getDoctrine()->getManager()->flush();
				$status = "AGRUPACIÓN " . $Seguimiento->getCodigo() . " MODIFICADA CORRECTAMENTE";
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Seguimiento->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			}
		}

		$params = ["seguimiento" => $Seguimiento,
			"accion" => "MODIFICACIÓN",
			"form" => $form->createView()];
		return $this->render("seguimiento/edit.html.twig", $params);

	}

	/**
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{

		$Seguimiento = new Seguimiento();

		$form = $this->createForm(SeguimientoType::class, $Seguimiento);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Seguimiento);
				$this->getDoctrine()->getManager()->flush();
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Seguimiento->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			}
		}

		$params = ["seguimiento" => $Seguimiento,
			"accion" => "CREACIÓN",
			"form" => $form->createView()];
		return $this->render("seguimiento/edit.html.twig", $params);
	}

	/**
	 * @param $id
	 * @return Response
	 * @throws DBALException
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */

	public function exportarAction($id)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$Seguimiento = $EntityManager->getRepository("AppBundle:Seguimiento")->find($id);
		$Agrupaciones = $EntityManager->getRepository("AppBundle:Agrupacion")->findBy(["seguimiento" => $Seguimiento]);


		$PHPExcel = new Spreadsheet();
		$sheet = $PHPExcel->getActiveSheet();
		$row = 1;
		$sheet->setCellValueByColumnAndRow(1, $row, 'ID AGRUPACION');
		$sheet->setCellValueByColumnAndRow(2, $row, 'CODIGO AGRUPACION');
		$sheet->setCellValueByColumnAndRow(3, $row, 'AGRUPACIÓN');
		$sheet->setCellValueByColumnAndRow(4, $row, 'ID');
		$sheet->setCellValueByColumnAndRow(5, $row, 'NUMERO');
		$sheet->setCellValueByColumnAndRow(6, $row, 'TITULO');
		$sheet->setCellValueByColumnAndRow(7, $row, 'OBJETO');
		$sheet->setCellValueByColumnAndRow(8, $row, 'ESTADO');
		$sheet->setCellValueByColumnAndRow(9, $row, 'FECHA ESTADO');
		$sheet->setCellValueByColumnAndRow(10, $row, 'ANOTACIÓN');
		$row++;
		foreach ($Agrupaciones as $Agrupacion) {
			$Encargos = $EntityManager->getRepository("AppBundle:Encargo")->findActivosByAgrupacion($Agrupacion);
			foreach ($Encargos as $Encargo) {
				$sheet->setCellValueByColumnAndRow(1, $row, $Encargo["agrupacionId"]);
				$sheet->setCellValueByColumnAndRow(2, $row, $Encargo["agrupacionCd"]);
				$sheet->setCellValueByColumnAndRow(3, $row, $Encargo["agrupacionDs"]);
				$sheet->setCellValueByColumnAndRow(4, $row, $Encargo["encargoId"]);
				$sheet->setCellValueByColumnAndRow(5, $row, $Encargo["encargoNumero"]);
				$sheet->setCellValueByColumnAndRow(6, $row, $Encargo["encargoTitulo"]);
				$sheet->setCellValueByColumnAndRow(7, $row, $Encargo["objetoEncargoCd"]);
				$sheet->setCellValueByColumnAndRow(8, $row, $Encargo["estadoEncargoCd"]);
				$sheet->setCellValueByColumnAndRow(9, $row, $Encargo["fechaEstadoActual"]);
				$sheet->setCellValueByColumnAndRow(10, $row, '');
				$row++;
			}
		}

		/** @var Spreadsheet $PHPExcel */
		$writer = IOFactory::createWriter($PHPExcel, 'Xlsx');
		$fechaActual = new DateTime();
		$filename = 'Seguimiento-' . $Seguimiento->getCodigo() . '-' . $fechaActual->format('Ymd-His') . '.xlsx';
		$writer->save($filename);

		$response = new Response();
		$response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
		$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'max-age=1');
		$response->setContent(file_get_contents($filename));

		return $response;
	}
}
