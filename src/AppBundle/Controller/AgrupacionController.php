<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\AgrupacionDatatable;
use AppBundle\Entity\Agrupacion;
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
use AppBundle\Form\AgrupacionType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;

/**
 * Class AgrupacionController
 *
 * @package AppBundle\Controller
 */
class AgrupacionController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * AgrupacionController constructor.
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
		$datatable = $this->get('sg_datatables.factory')->create(AgrupacionDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('agrupacion/query.html.twig', [
			'datatable' => $datatable,
		]);
	}

	/**
	 * @param Request $request
	 * @param int $id
	 * @return JsonResponse|Response
	 * @throws Exception
	 */
	public function queryBySeguimientoAction(Request $request, $id)
	{
		$Seguimiento = $this->getDoctrine()->getManager()->getRepository("AppBundle:Seguimiento")->find($id);
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(AgrupacionDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$qb = $datatableQueryBuilder->getQb();
			$qb->andWhere('seguimiento = :seguimiento');
			$qb->setParameter('seguimiento', $Seguimiento);

			return $responseService->getResponse();
		}

		return $this->render('agrupacion/query.html.twig', [
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

		$Agrupacion = $this->getDoctrine()->getManager()->getRepository("AppBundle:Agrupacion")->find($id);

		$form = $this->createForm(AgrupacionType::class, $Agrupacion);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Agrupacion);
				$this->getDoctrine()->getManager()->flush();
				$status = "AGRUPACIÓN " . $Agrupacion->getCodigo() . " MODIFICADA CORRECTAMENTE";
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Agrupacion->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			}
		}

		$params = ["agrupacion" => $Agrupacion,
			"accion" => "MODIFICACIÓN",
			"form" => $form->createView()];
		return $this->render("agrupacion/edit.html.twig", $params);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{

		$Agrupacion = new Agrupacion();

		$form = $this->createForm(AgrupacionType::class, $Agrupacion);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Agrupacion);
				$this->getDoctrine()->getManager()->flush();
				return $this->redirectToRoute('queryAgrupacion');
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Agrupacion->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			}
		}

		$params = ["agrupacion" => $Agrupacion,
			"accion" => "CREACIÓN",
			"form" => $form->createView()];
		return $this->render("agrupacion/edit.html.twig", $params);
	}


	/**
	 * @param $id
	 * @return Response
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */

	public function exportarAction($id)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$Agrupacion = $EntityManager->getRepository("AppBundle:Agrupacion")->find($id);


		$reader = IOFactory::createReader('Xlsx');
		/** @var Spreadsheet $sheet */
		$Spreadsheet = $reader->load('plantillas/PlantillaAgrupacion.xlsx');
		$sheet = $Spreadsheet->getActiveSheet();
		$sheet->setCellValue('B10', $Agrupacion->getId());
		$sheet->setCellValue('C10', $Agrupacion->getCodigo());
		$sheet->setCellValue('D10', $Agrupacion->getDescripcion());
		$Encargos = $EntityManager->getRepository("AppBundle:Encargo")->findBy(["agrupacion"=>$Agrupacion]);
		$row=14;
		foreach ($Encargos as $Encargo) {
			$sheet->insertNewRowBefore($row, 1);
			$sheet->setCellValue('B'.$row, $Encargo->getId());
			$sheet->setCellValue('C'. $row, $Encargo->getNumero());
			$sheet->setCellValue('D'. $row, $Encargo->getTitulo());
			$sheet->setCellValue('E'. $row, $Encargo->getObjetoEncargo()->getCodigo());
			$sheet->setCellValue('F'. $row, $Encargo->getEstadoActual()->getCodigo());
			$sheet->setCellValue('G'. $row, $Encargo->getFcEstadoActual());
			$sheet->setCellValue('H'. $row, '');
			$row++;
		}

		$rango= "A3:G".$row;
		$estiloArray = [ 'font' => [ 'name' => 'Arial',
									 'bold' => true,
									 'italic' => false,
									 'underline' => Font::UNDERLINE_DOUBLE,
									 'strikethrough' => false,
								     'color' => [ 'rgb' => '808080' ] ],
						'borders' => [ 'bottom' => [ 'borderStyle' => Border::BORDER_DASHDOT,
													 'color' => [ 'rgb' => '808080' ] ],
									   'top' => [ 'borderStyle' => Border::BORDER_DASHDOT,
										   		  'color' => [ 'rgb' => '808080' ] ] ],
					    'alignment' => [ 'horizontal' => Alignment::HORIZONTAL_CENTER,
										 'vertical' => Alignment::VERTICAL_CENTER,
										 'wrapText' => true, ],
						'quotePrefix' => true ];

//		$sheet->getStyle($rango)->applyFromArray($estiloArray);
//		$fichero = IOFactory::createWriter($sheet, 'Xlsx');

		$writer = new Xlsx($Spreadsheet);
		$fechaActual = new DateTime();
		$filename = 'Agrupacion-' . $Agrupacion->getCodigo() . '-' . $fechaActual->format('Ymd-His') . '.xlsx';
		$writer->save($filename);

		/** @var Response $response */
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
