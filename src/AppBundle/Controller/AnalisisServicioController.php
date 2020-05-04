<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\AnalisisServicioDatatable;
use AppBundle\Entity\AnalisisServicio;
use AppBundle\Entity\Mes;
use AppBundle\Form\PeriodoActualType;
use AppBundle\Form\PeriodoType;
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
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->get('sg_datatables.factory')->create(AnalisisServicioDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->get('sg_datatables.response');
            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
            $datatableQueryBuilder->buildQuery();
            return $responseService->getResponse();
        }

        return $this->render('analisisServicio/query.html.twig', [
            'datatable' => $datatable,
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
    public function generaAnalisiServicioAction($periodo_id)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $AnalisisServicio = new AnalisisServicio();

        /** @var Mes $Periodo */
        $Periodo = $EntityManager->getRepository("AppBundle:Mes")->findOneBy(["id" => $periodo_id]);

        $fechaInicial = $Periodo->getFechaInicio();
        $fechaFinal = $Periodo->getFechaFin();


        $AnalisisServicio->setMes($Periodo);
        $AnalisisServicio->setEstado("Solicitado");
        $AnalisisServicio->setTotalEntradas(0);
        $AnalisisServicio->setTotalCerrados(0);
        $AnalisisServicio->setTotalCancelados(0);
        $AnalisisServicio->setEsfuerzoTotal(0);
        $AnalisisServicio->setSaldo(0);
        $AnalisisServicio->setEsfuerzoMedio(0);
        $AnalisisServicio->setEsfuerzoTotal(0);
        $AnalisisServicio->setTiempoResolucion(0);
        $AnalisisServicio->setTiempoMedioResolucion(0);
        $EntityManager->persist($AnalisisServicio);
        $EntityManager->flush();


        return true;
    }

    public function queryDetalleAnalisisServicioPeriodoAction()
    {
        return true;
    }
}
