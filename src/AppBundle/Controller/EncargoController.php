<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\EncargoDatatable;
use AppBundle\Entity\AnotacionEncargo;
use AppBundle\Form\AnotacionEncargoType;
use AppBundle\Form\EncargoType;
use Doctrine\DBAL\DBALException;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
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
use DateTime;

/**
 * Class EncargoController
 *
 * @package AppBundle\Controller
 */
class EncargoController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * EncargoController constructor.
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
		$datatable = $this->get('sg_datatables.factory')->create(EncargoDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('encargo/query.html.twig', [
			'datatable' => $datatable,
			'agrupacion' => null
		]);
	}

	public function queryByAgrupacionAction(Request $request, $idAgrupacion)
	{
		$Agrupacion = $this->getDoctrine()->getManager()->getRepository("AppBundle:Agrupacion")->find($idAgrupacion);

		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(EncargoDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$qb = $datatableQueryBuilder->getQb();
			$qb->andWhere('agrupacion = :agrupacion');
			$qb->setParameter('agrupacion', $Agrupacion);
			return $responseService->getResponse();

		}

		return $this->render('encargo/query.html.twig', [
			'datatable' => $datatable,
			'agrupacion' => $Agrupacion
		]);
	}


	/**
	 * @param $id
	 * @return RedirectResponse
	 */

	public function viewSecoAction($id)
	{
		$Encargo = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo")->find($id);
		$url = 'http://intranet.madrid.org/seco/html/web/CmmaEncargoConsulta.icm?cdCmmaEncargo=' . $Encargo->getNumero();
		return $this->redirect($url);

	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, $id)
	{
		$EntityManager = $this->getDoctrine()->getManager();

		$Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($id);

		$form = $this->createForm(EncargoType::class, $Encargo);
		$form->handleRequest($request);

		$Anotaciones = $EntityManager->getRepository("AppBundle:AnotacionEncargo")->findBy(["encargo" => $Encargo]);

		if ($form->isSubmitted()) {
			$this->getDoctrine()->getManager()->persist($Encargo);
			$this->getDoctrine()->getManager()->flush();
			$status = 'ENCARGO ' . $Encargo->getNumero() . ' MODIFICADO CORRECTAMENTE ';
			$this->sesion->getFlashBag()->add("status", $status);
			return $this->redirectToRoute("queryEncargo");
		}

		$params = ["encargo" => $Encargo,
			"accion" => "MODIFICACIÓN",
			"anotacionesEncargo" => $Anotaciones,
			"form" => $form->createView()];
		return $this->render("encargo/edit.html.twig", $params);
	}


	/**
	 * @param Request $request
	 * @param int $encargo_id
	 * @return RedirectResponse|Response
	 * @throws Exception
	 */
	public function addAnotacionAction(Request $request, $encargo_id)
	{

		$EntityManager = $this->getDoctrine()->getManager();

		$Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($encargo_id);
		$fecha = new DateTime();
		$AnotacionEncargo = new AnotacionEncargo();
		$AnotacionEncargo->setEncargo($Encargo);
		$AnotacionEncargo->setUsuario($this->getUser());
		$AnotacionEncargo->setFecha($fecha);

		$form = $this->createForm(AnotacionEncargoType::class, $AnotacionEncargo);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$EntityManager->persist($AnotacionEncargo);
			$EntityManager->flush();
			$status = 'ANOTACIÓN CREADA CORRECTAMENTE ';
			$this->sesion->getFlashBag()->add("status", $status);
			$params = ["id" => $Encargo->getId()];
			return $this->redirectToRoute("editEncargo", $params);
		}

		$params = ["encargo" => $Encargo,
			"form" => $form->createView()];
		return $this->render("encargo/addAnotacion.html.twig", $params);
	}

	/**
	 * @return Response
	 * @throws DBALException
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */

	public function exportarTodoAction()
	{
		$EntityManager = $this->getDoctrine()->getManager();

		$reader = IOFactory::createReader('Xlsx');
		/** @var Spreadsheet $sheet */
		$Spreadsheet = $reader->load('plantillas/PlantillaMagma.xlsx');
		$sheet = $Spreadsheet->getActiveSheet();
		$Encargos = $EntityManager->getRepository("AppBundle:Encargo")->findPla();
		$row = 7;
		$sheet->setCellValue('B' . $row, "ID");
		$sheet->setCellValue('C' . $row, "NUMERO");
		$sheet->setCellValue('D' . $row, "TITULO");
		$sheet->setCellValue('E' . $row, "OBJETO");
		$sheet->setCellValue('F' . $row, "ESTADO");
		$sheet->setCellValue('G' . $row, "AGRUPACION");
		$sheet->setCellValue('H' . $row, "FECHA INICIO");
		$sheet->setCellValue('I' . $row, "FECHA COMPROMISO");
		$sheet->setCellValue('J' . $row, "FECHA ENTREGA");
		$sheet->setCellValue('K' . $row, "HORAS COMPROMETIDAS");
		$row++;
		foreach ($Encargos as $RstEncargo) {
			$Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($RstEncargo["encargoId"]);
			$sheet->setCellValue('B' . $row, $Encargo->getId());
			$sheet->setCellValue('C' . $row, $Encargo->getNumero());
			$sheet->setCellValue('D' . $row, $Encargo->getTitulo());
			$sheet->setCellValue('E' . $row, $Encargo->getObjetoEncargo()->getDescripcion());
			$sheet->setCellValue('F' . $row, $Encargo->getEstadoActual()->getDescripcion());
			$sheet->setCellValue('G' . $row, $Encargo->getAgrupacion()->getDescripcion());
			$sheet->setCellValue('H' . $row, $Encargo->getFcComienzoEjecucion());
			$sheet->setCellValue('I' . $row, $Encargo->getFcCompromiso());
			$sheet->setCellValue('J' . $row, $Encargo->getFcEntrega());
			$sheet->setCellValue('K' . $row, $Encargo->getHorasComprometidas());
			$row++;
		}

		$rango = "B1:G" . $row;
		$estiloArray = ['font' => ['name' => 'Verdana',
			'bold' => true,
			'italic' => false,
			'underline' => Font::UNDERLINE_SINGLE,
			'strikethrough' => false,
			'color' => ['rgb' => '808080']],
			'borders' => ['bottom' => ['borderStyle' => Border::BORDER_DASHDOT,
				'color' => ['rgb' => '808080']],
				'top' => ['borderStyle' => Border::BORDER_DASHDOT,
					'color' => ['rgb' => '808080']]],
			'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER,
				'wrapText' => true,],
			'quotePrefix' => true];

//		$sheet->getStyle($rango)->applyFromArray($estiloArray);
//		$fichero = IOFactory::createWriter($sheet, 'Xlsx');

		$writer = new Xlsx($Spreadsheet);
		$fechaActual = new DateTime();
		$filename = 'ExportacionGlobal-' . $fechaActual->format('Ymd-His') . '.xlsx';
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

