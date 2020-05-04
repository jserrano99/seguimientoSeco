<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\ComunDatatable;
use AppBundle\Entity\Comun;
use AppBundle\Form\PeriodoActualType;
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
use AppBundle\Form\ComunType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;

/**
 * Class ComunController
 *
 * @package AppBundle\Controller
 */
class ComunController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * ComunController constructor.
	 */
	public function __construct()
	{
		$this->sesion = new Session();
	}


    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
	public function actualizarPeriodoAction(Request $request)
	{
        $EntityManager = $this->getDoctrine()->getManager();


		$form = $this->createForm(PeriodoActualType::class);
		$form->handleRequest($request);
        $PeriodoActual = $EntityManager->getRepository("AppBundle:PeriodoActual")->find(1);
        $actualizado = true;

		if ($form->isSubmitted()) {
			try {
                $idMes = $_POST["formPeriodoActual"]["mes"];
                $Mes= $EntityManager->getRepository("AppBundle:Mes")->find($idMes);
                $PeriodoActual->setPeriodo($Mes);
               	$this->getDoctrine()->getManager()->persist($PeriodoActual);
				$this->getDoctrine()->getManager()->flush();
				$status = " Establecido  " . $Mes->getDescripcion() . " COMO ACTUAL ";
				$this->sesion->getFlashBag()->add("status", $status);
				$actualizado = false;
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
			}
		}

		$params = ["periodoActual" => $PeriodoActual,
            "actualizado" => $actualizado,
			"form" => $form->createView()];
		return $this->render("comun/cambiaPeriodo.html.twig", $params);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{

		$Comun = new Comun();

		$form = $this->createForm(ComunType::class, $Comun);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Comun);
				$this->getDoctrine()->getManager()->flush();
				return $this->redirectToRoute('queryComun');
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Comun->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryComun");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryComun");
			}
		}

		$params = ["Comun" => $Comun,
			"accion" => "CREACIÓN",
			"form" => $form->createView()];
		return $this->render("Comun/edit.html.twig", $params);
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
		$Comun = $EntityManager->getRepository("AppBundle:Comun")->find($id);


		$reader = IOFactory::createReader('Xlsx');
		/** @var Spreadsheet $sheet */
		$Spreadsheet = $reader->load('plantillas/PlantillaComun.xlsx');
		$sheet = $Spreadsheet->getActiveSheet();
		$sheet->setCellValue('B10', $Comun->getId());
		$sheet->setCellValue('C10', $Comun->getCodigo());
		$sheet->setCellValue('D10', $Comun->getDescripcion());
		$Encargos = $EntityManager->getRepository("AppBundle:Encargo")->findBy(["Comun"=>$Comun]);
		$row=14;
		foreach ($Encargos as $Encargo) {
			$sheet->insertNewRowBefore($row, 1);
			$sheet->setCellValue('B'.$row, $Encargo->getId());
			$sheet->setCellValue('C'. $row, $Encargo->getNumero());
			$sheet->setCellValue('D'. $row, $Encargo->getTitulo());
			$sheet->setCellValue('E'. $row, $Encargo->getObjetoEncargo()->getCodigo());
			$sheet->setCellValue('F'. $row, $Encargo->getEstadoActual()->getCodigo());
			$sheet->setCellValue('G'. $row, $Encargo->getFcEstadoActual());
			if ($Encargo->getObjetoEncargo()->getTipoObjeto()->getCodigo() != 'NPL') {
				$sheet->setCellValue('H' . $row, $Encargo->getHorasComprometidas());
				$sheet->setCellValue('I' . $row,'');
			} else {
				$sheet->setCellValue('H' . $row,'');
				$sheet->setCellValue('I' . $row, $Encargo->getHorasRealizadas());
			}
			$sheet->setCellValue('J'. $row, '');
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
		$filename = 'Comun-' . $Comun->getCodigo() . '-' . $fechaActual->format('Ymd-His') . '.xlsx';
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

    /**
     * @param $idAnyo
     * @return Response
     */
    public
    function selectMesAction($idAnyo)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $Anyo = $EntityManager->getRepository("AppBundle:Anyo")->find($idAnyo);

        $Periodos = $EntityManager->getRepository("AppBundle:Mes")->findBy(["anyo" => $Anyo]);

        $html =
            " <option value='' selected='selected'>Seleccione mes ....</option> ";
        foreach ($Periodos as $Periodo) {
            $opcion = " <option value=' " . $Periodo->getId() . "'>" . $Periodo->getDescripcion() . "</option> ";
            $html = $html . $opcion;
        }
        $html = $html . "</select>";


        $reponse = new Response($html);

        return $reponse;

    }
}
