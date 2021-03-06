<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\CertificadoServiciosDatatable;
use AppBundle\Datatables\EncargoPenalizadoDatatable;
use AppBundle\Datatables\LineaCertificadoDatatable;
use AppBundle\Entity\Anyo;
use AppBundle\Entity\CargaFichero;
use AppBundle\Entity\CertificadoServicios;
use AppBundle\Entity\Encargo;
use AppBundle\Entity\EncargoPenalizado;
use AppBundle\Entity\EstadoCertificado;
use AppBundle\Entity\FicheroLog;
use AppBundle\Entity\ImportesCertificado;
use AppBundle\Entity\ImportesContrato;
use AppBundle\Entity\Indicador;
use AppBundle\Entity\LineaCertificado;
use AppBundle\Entity\LineaCertificadoEje;
use AppBundle\Entity\Penalizacion;
use AppBundle\Entity\PosicionEconomica;
use AppBundle\Entity\TipoCuota;
use AppBundle\Form\AddEncargoType;
use AppBundle\Form\CertificadoServiciosType;
use AppBundle\Form\EncargoPenalizadoType;
use AppBundle\Form\ImportarType;
use AppBundle\Servicios\EscribeLog;
use DateInterval;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Exception;
use PDO;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class CertificadoServiciosController
 *
 * @package AppBundle\Controller
 */
class CertificadoServiciosController extends Controller
{
    /**
     * @var Session
     */
    private $sesion;

    /**
     * CertificadoServiciosController constructor.
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
        $datatable = $this->get('sg_datatables.factory')->create(CertificadoServiciosDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->get('sg_datatables.response');
            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
            $datatableQueryBuilder->buildQuery();
            return $responseService->getResponse();
        }

        return $this->render('certificadoServicios/query.html.twig', [
            'datatable' => $datatable,
        ]);
    }

    /**
     * @param Request $request
     * @param                                           $id
     * @return Response
     */
    public function editAction(Request $request, $id)
    {

        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

        $form = $this->createForm(CertificadoServiciosType::class, $CertificadoServicios);
        $form->handleRequest($request);

        return $this->render("certificadoServicios/edit.html.twig", [
            "form" => $form->createView(),
            "accion" => "EDITAR",
            'certificadoServicios' => $CertificadoServicios,
            'estado' => $CertificadoServicios->getEstadoCertificado()]);

    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function addAction(Request $request)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $CertificadoServicios = new CertificadoServicios();
        $form = $this->createForm(CertificadoServiciosType::class, $CertificadoServicios);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $idMes = $_POST["formCertificadoServicios"]["mes"];
            $Mes = $EntityManager->getRepository("AppBundle:Mes")->find($idMes);
            $CertificadoServicios2 = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->findOneBy(["mes" => $Mes]);
            if ($CertificadoServicios2) {
                if ($CertificadoServicios2->getEstadoCertificado()->getId() == 1 or
                    $CertificadoServicios2->getEstadoCertificado()->getId() == 4) {
                    $this->deleteAction($CertificadoServicios2->getId());
                } else {
                    $status = " YA EXISTE UN CERTIFICADO PARA ESTE MES ";
                    $this->sesion->getFlashBag()->add("status", $status);
                    return $this->render("certificadoServicios/edit.html.twig", [
                        "form" => $form->createView(),
                        "accion" => "GENERAR",
                        'estado' => 'PENDIENTE']);
                }
            }
            $EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);
            $Contrato = $this->getDoctrine()->getManager()->getRepository("AppBundle:Contrato")->find(1);
            $CertificadoServicios->setEstadoCertificado($EstadoCertificado);
            $CertificadoServicios->setContrato($Contrato);
            $CertificadoServicios->setMes($Mes);
            $CertificadoServicios->setDescripcion("Certificado de Servicios " . $Mes->getDescripcion());
            $CertificadoServicios->setFechaCertificado($Mes->getFechaInicio());
            $ImportesContrato = $this->importesContrato($CertificadoServicios);
            $CertificadoServicios->setImporteCuotaFijaMensual($ImportesContrato->getCuotaFijaMensual());
            $this->getDoctrine()->getManager()->persist($CertificadoServicios);
            $this->getDoctrine()->getManager()->flush();
            $this->generarCertificado($CertificadoServicios);
            return $this->redirectToRoute("queryCertificadoServicios");
        }

        return $this->render("certificadoServicios/edit.html.twig", ["form" => $form->createView(),
            "accion" => "GENERAR",
            'estado' => 'PENDIENTE']);

    }

    /**
     * @param $id
     * @return RedirectResponse
     * @throws DBALException
     */
    public
    function deleteAction($id)
    {
        $sentencia = " update encargo  set bloqueado = null "
            . " where encargo.id in (select encargo_id from linea_certificado "
            . "  where certificado_servicios_id = :id )";


        /** @var Connection $conexion */
        $conexion = $this->getDoctrine()->getConnection();
        $stmt = $conexion->prepare($sentencia);
        $params = [":id" => $id];

        $stmt->execute($params);

        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

        $this->getDoctrine()->getManager()->remove($CertificadoServicios);
        $this->getDoctrine()->getManager()->flush();

        $status = " CERTIFICADO DE SERVICIO ELIMINADO CORRECTAMENTE ";
        $this->sesion->getFlashBag()->add("status", $status);
        return $this->redirectToRoute("queryCertificadoServicios");
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return mixed
     * @throws Exception
     */

    public
    function generarCertificado($CertificadoServicios)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $FicheroLog = new FicheroLog();
        $fechaProceso = new DateTime();
        $FicheroLog->setFechaProceso($fechaProceso);


        $ServicioLog = $this->get('app.escribelog');
        $ServicioLog->setLogger('CERT. Servicios: = ' . $CertificadoServicios->getId());
        $ficheroLog = 'FicheroLog-' . $CertificadoServicios->getId();
        $FicheroLog->setNombreFichero($ficheroLog);

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
        /** @var FicheroLog $ficheroLog */
        $ServicioLog->setMensaje("==>INCLUIR ENCARGOS NO PLANIFICABLES ");
        $ServicioLog->escribeLog($ficheroLog);
        $this->incluirNPL($CertificadoServicios, $ServicioLog, $ficheroLog);

        $ServicioLog->setMensaje("==>INCLUIR ENCARGOS ADAPTACIONES MENORES ");
        $ServicioLog->escribeLog($ficheroLog);
        $this->incluirADM($CertificadoServicios, $ServicioLog, $ficheroLog);

        $ServicioLog->setMensaje("==>INCLUIR ENCARGOS EVOLUTIVOS DE CUOTA FIJA ");
        $ServicioLog->escribeLog($ficheroLog);
        $this->incluirSCF($CertificadoServicios, $ServicioLog, $ficheroLog);

        $ServicioLog->setMensaje("==>INCLUIR ENCARGOS EVOLUTIVOS DE CUOTA FIJA EN EJECUCIÓN ");
        $ServicioLog->escribeLog($ficheroLog);
        $this->incluirSCFEJE($CertificadoServicios, $ServicioLog, $ficheroLog);
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
         * CALCULA LAS PENALIZACIONES
         */
        $ServicioLog->setMensaje("==>GENERACIÓN DE PENALIZACIONES DEL CERTIFICADO");
        $ServicioLog->escribeLog($ficheroLog);
        $this->calculaPenalizaciones($CertificadoServicios);
        /**
         * GENERAR LOS IMPORTES
         */
        $ServicioLog->setMensaje("==>GENERACIÓN DE IMPORTES DEL CERTIFICADO");
        $ServicioLog->escribeLog($ficheroLog);
        $this->generarImportes($CertificadoServicios);

        return true;

    }

    /**
     * @param $id
     * @return RedirectResponse
     * @throws DBALException
     */
    public
    function importesAction($id)
    {

        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

        $this->generarImportes($CertificadoServicios);

        $status = " IMPORTES DEL CERTIFICADO DE SERVICIO GENERADO CORRECTAMENTE ";
        $this->sesion->getFlashBag()->add("status", $status);
        $params = ["id" => $CertificadoServicios->getId()];
        return $this->redirectToRoute("editCertificadoServicios", $params);

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool|float
     */
    public
    function importePenalizacion($CertificadoServicios)
    {

        $importeIRS01 = $this->importePenalizacionIRS01($CertificadoServicios);
        $importeIRS02 = $this->importePenalizacionIRS02($CertificadoServicios);
        $importeIRS03 = $this->importePenalizacionIRS03($CertificadoServicios);
        $importeIRIi = $this->importePenalizacionIRIi($CertificadoServicios);
        $importeIRQ01 = $this->importePenalizacionIRQ01($CertificadoServicios);
        $importeENC01 = $this->importePenalizacionENC01($CertificadoServicios);
        $importeENC02 = $this->importePenalizacionENC02($CertificadoServicios);
        $importeENT01 = $this->importePenalizacionENT01($CertificadoServicios);


        return $importeIRS01 +
            $importeIRS02 +
            $importeIRS03 +
            $importeIRIi +
            $importeIRQ01 +
            $importeENC01 +
            $importeENC02 +
            $importeENT01;

    }


    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     * @throws DBALException
     */
    public
    function generarImportes($CertificadoServicios)
    {

        $sentencia = " delete from importes_certificado_servicios  "
            . " where  certificado_servicios_id = :id";

        /** @var Connection $conexion */
        $conexion = $this->getDoctrine()->getConnection();
        $stmt = $conexion->prepare($sentencia);
        $params = [":id" => $CertificadoServicios->getId()];
        $stmt->execute($params);

        $sentencia = " delete from penalizaciones  "
            . " where  certificado_servicios_id = :id";

        $stmt = $conexion->prepare($sentencia);
        $params = [":id" => $CertificadoServicios->getId()];
        $stmt->execute($params);

        $ImporteCuotaFija = $this->importesCuotaFija($CertificadoServicios);
        $ImporteCuotaVariable = $this->importesCuotaVariable($CertificadoServicios);
        $importeCuotaTasada = $this->importesCuotaTasada($CertificadoServicios);

        $total = $ImporteCuotaFija->getImporte() + $ImporteCuotaVariable->getImporte() + $importeCuotaTasada;
        $total = round($total, 2);
        $maximoPenalizaciones = round($total * 0.20, 2);
        $importePenalizacion = round($this->importePenalizacion($CertificadoServicios), 2);


        if ($importePenalizacion > $maximoPenalizaciones) {
            $penalizacionAplicable = $maximoPenalizaciones;
        } else {
            $penalizacionAplicable = $importePenalizacion;
        }

        $baseImponible = $total - $penalizacionAplicable;
        $cuotaIVA = round($baseImponible * 0.21, 2);
        $totalFacturaIVA = $baseImponible + $cuotaIVA;

        $CertificadoServicios->setTotalCuota($total);
        $CertificadoServicios->setBaseImponible($baseImponible);
        $CertificadoServicios->setCuotaIva($cuotaIVA);
        $CertificadoServicios->setTotalFacturaConIva($totalFacturaIVA);
        $EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);
        $CertificadoServicios->setEstadoCertificado($EstadoCertificado);

        $CertificadoServicios->setPenalizacionAplicable($penalizacionAplicable);
        $CertificadoServicios->setTotalPenalizaciones($importePenalizacion);
        $CertificadoServicios->setMaximoPenalizaciones($maximoPenalizaciones);

        $this->getDoctrine()->getManager()->persist($CertificadoServicios);
        $this->getDoctrine()->getManager()->flush();

        return true;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return ImportesCertificado|float|int
     * @throws DBALException
     */
    public
    function importesCuotaTasada($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(3);

        $sentencia = " select * from view_actividad_tasada_acum "
            . " where  certificadoServiciosId = :id";

        /** @var Connection $conexion */
        $conexion = $this->getDoctrine()->getConnection();
        $stmt = $conexion->prepare($sentencia);
        $params = [":id" => $CertificadoServicios->getId()];
        $stmt->execute($params);

        $LineaCertificadoAll = $stmt->fetchAll();

        $importe = 0;
        foreach ($LineaCertificadoAll as $LineaCertificado) {
            $ImportesCertificado = new ImportesCertificado();
            $ImportesCertificado->setCertificadoServicios($CertificadoServicios);
            $ImportesCertificado->setCodigo(3);
            $ImportesCertificado->setTipoCuota($TipoCuota);
            $ImportesCertificado->setDescripcion($LineaCertificado["posicionEconomicaDs"]);
            $ImportesCertificado->setHorasCertificadas(0);
            $ImportesCertificado->setTarifa(0);
            $ImportesCertificado->setImporte(round($LineaCertificado["importeAcumulado"], 2));
            $ImportesCertificado->setPenalizacion(0);
            $ImportesCertificado->setTotal(0);
            /** @var PosicionEconomica $PosicionEconomica */
            $PosicionEconomica = $EntityManager->getRepository("AppBundle:PosicionEconomica")->find($LineaCertificado["posicionEconomicaId"]);
            $ImportesCertificado->setPosicionEconomica($PosicionEconomica);
            $EntityManager->persist($ImportesCertificado);
            $EntityManager->flush();
            $importe = $importe + $ImportesCertificado->getImporte();
        }

        return $importe;

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return ImportesCertificado
     */
    public
    function importesCuotaVariable($CertificadoServicios)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(2);
        /** @var PosicionEconomica $PosicionEconomica */
        $PosicionEconomica = $EntityManager->getRepository("AppBundle:PosicionEconomica")->find(2);

        $tarifa = 37.47;


        $LineaCertificadoAll = $EntityManager->getRepository("AppBundle:LineaCertificado")
            ->findBy(["certificadoServicios" => $CertificadoServicios, "tipoCuota" => $TipoCuota]);

        $importe = 0;
        $horas = 0;
        foreach ($LineaCertificadoAll as $LineaCertificado) {
            $horas = $horas + $LineaCertificado->getEncargo()->getHorasComprometidas();
            $importe = $importe + ($LineaCertificado->getEncargo()->getHorasComprometidas() * $tarifa);
        }

        $importe = round($importe, 2);

        $ImportesCertificado = new ImportesCertificado();
        $ImportesCertificado->setCertificadoServicios($CertificadoServicios);
        $ImportesCertificado->setCodigo(2);
        $ImportesCertificado->setTipoCuota($TipoCuota);
        $ImportesCertificado->setDescripcion($PosicionEconomica->getDescripcion());
        $ImportesCertificado->setHorasCertificadas($horas);
        $ImportesCertificado->setTarifa($tarifa);
        $ImportesCertificado->setImporte($importe);
        $ImportesCertificado->setPenalizacion(0);
        $ImportesCertificado->setTotal(0);
        $ImportesCertificado->setPosicionEconomica($PosicionEconomica);
        $EntityManager->persist($ImportesCertificado);
        $EntityManager->flush();
        return $ImportesCertificado;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return ImportesCertificado
     */
    public
    function importesCuotaFija($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(1);

        $ImportesContrato = $this->importesContrato($CertificadoServicios);
        $PosicionEconomica = $EntityManager->getRepository("AppBundle:PosicionEconomica")->find(1);

        $ImportesCertificado = new ImportesCertificado();
        $ImportesCertificado->setCertificadoServicios($CertificadoServicios);
        $ImportesCertificado->setCodigo(1);
        $ImportesCertificado->setTipoCuota($TipoCuota);
        $ImportesCertificado->setDescripcion($PosicionEconomica->getDescripcion());
        $ImportesCertificado->setHorasCertificadas(null);
        $ImportesCertificado->setTarifa(null);
        $ImportesCertificado->setImporte($ImportesContrato->getCuotaFijaMensual());
        $ImportesCertificado->setPenalizacion(0);
        $ImportesCertificado->setTotal($ImportesContrato->getCuotaFijaMensual());
        $ImportesCertificado->setPosicionEconomica($PosicionEconomica);
        $EntityManager->persist($ImportesCertificado);
        $EntityManager->flush();

        return $ImportesCertificado;

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public
    function importePenalizacionIRQ01($CertificadoServicios)
    {

        $EntityManager = $this->getDoctrine()->getManager();

        $RemedyRepo = $EntityManager->getRepository("AppBundle:Remedy");

        $QuejasPeriodo = $RemedyRepo->createQueryBuilder('u')
            ->where("u.tipo = 'QUEJAS'")
            ->andWhere('u.fechaModificacion between :fcInicio and :fcFin')
            ->setParameter('fcInicio', $CertificadoServicios->getMes()->getfechaInicio())
            ->setParameter('fcFin', $CertificadoServicios->getMes()->getfechaFin())
            ->getQuery()->getResult();

        $nmQuejas = count($QuejasPeriodo);
        $IndicadorIRQ01 = $EntityManager->getRepository("AppBundle:Indicador")->find(5);
        $importe = 0;
        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorIRQ01);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos(0);
        $Penalizacion->setTotalEncargosPenalizados($nmQuejas);
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje(0);
        $Penalizacion->setFactor(0);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte(0);
        $EntityManager->persist($Penalizacion);
        $EntityManager->flush();
        return $importe;

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public
    function importePenalizacionIRIi($CertificadoServicios)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $IndicadorIRIi = $EntityManager->getRepository("AppBundle:Indicador")->find(6);

        $EncargosPenalizadosALL = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRIi,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null]);

        $totalEncargos = $CertificadoServicios->getContadorNPLCRI() + $CertificadoServicios->getContadorNPLNOR();
        $encargosPenalizados = count($EncargosPenalizadosALL);

        if ($totalEncargos == 0)
            $porcentaje = 1;
        else
            $porcentaje = $encargosPenalizados / $totalEncargos;

        $factor = 0;
        if ($porcentaje < 0.05) $factor = 0;
        if ($porcentaje >= 0.05 and $porcentaje < 0.10) $factor = 0.006;
        if ($porcentaje >= 0.10 and $porcentaje < 0.15) $factor = 0.012;
        if ($porcentaje >= 0.15 and $porcentaje < 0.20) $factor = 0.018;
        if ($porcentaje >= 0.20) $factor = 0.04;

        $importePenalizacion = $CertificadoServicios->getImporteCuotaFijaMensual() * $factor;

        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorIRIi);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos($totalEncargos);
        $Penalizacion->setTotalEncargosPenalizados($encargosPenalizados);
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje($porcentaje);
        $Penalizacion->setFactor($factor);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte($importePenalizacion);
        $EntityManager->persist($Penalizacion);
        $EntityManager->flush();

        return $importePenalizacion;

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public
    function importePenalizacionIRS01($CertificadoServicios)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $encargosNPLCRI = $CertificadoServicios->getContadorNPLCRI();

        $IndicadorIRS01 = $EntityManager->getRepository("AppBundle:Indicador")->find(1);
        $EncargosPenalizadosALL = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS01,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null]);
        $encargosCumplen = $encargosNPLCRI - count($EncargosPenalizadosALL);

        if ($encargosNPLCRI > 0 and !is_null($encargosNPLCRI))
            $porcentaje = $encargosCumplen / $encargosNPLCRI;
        else
            $porcentaje = 1;

        $factor = 0;
        if ($porcentaje > 0.95) $factor = 0;
        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
        if ($porcentaje <= 0.85) $factor = 1;

        $peso = $IndicadorIRS01->getPeso() * $factor;
        $importe = round($CertificadoServicios->getImporteCuotaFijaMensual() * $peso, 2);
        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorIRS01);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos($encargosNPLCRI);
        $Penalizacion->setTotalEncargosPenalizados(count($EncargosPenalizadosALL));
        $Penalizacion->setTotalCumplen($encargosCumplen);
        $Penalizacion->setPorcentaje($porcentaje);
        $Penalizacion->setFactor($factor);
        $Penalizacion->setPeso($peso);
        $Penalizacion->setImporte($importe);

        $EntityManager->persist($Penalizacion);
        $EntityManager->flush();

        return $importe;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public
    function importePenalizacionIRS02($CertificadoServicios)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $encargosNPLNOR = $CertificadoServicios->getContadorNPLNOR();
        $IndicadorIRS02 = $EntityManager->getRepository("AppBundle:Indicador")->find(3);
        $EncargosPenalizadosALL = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS02,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null]);
        $encargosCumplen = $encargosNPLNOR - count($EncargosPenalizadosALL);
        if ($encargosNPLNOR > 0 and !is_null($encargosNPLNOR))
            $porcentaje = $encargosCumplen / $encargosNPLNOR;
        else
            $porcentaje = 1;
        $factor = 0;

        if ($porcentaje > 0.95) $factor = 0;
        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
        if ($porcentaje <= 0.85) $factor = 1;

        $peso = $IndicadorIRS02->getPeso() * $factor;
        $importe = round($CertificadoServicios->getImporteCuotaFijaMensual() * $peso, 2);

        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorIRS02);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargosPenalizados(count($EncargosPenalizadosALL));
        $Penalizacion->setTotalEncargos($encargosNPLNOR);
        $Penalizacion->setTotalCumplen($encargosCumplen);
        $Penalizacion->setPorcentaje($porcentaje);
        $Penalizacion->setFactor($factor);
        $Penalizacion->setPeso($peso);
        $Penalizacion->setImporte($importe);

        $EntityManager->persist($Penalizacion);
        $EntityManager->flush();

        return $importe;
    }


    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public
    function importePenalizacionIRS03($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $encargosADM = $CertificadoServicios->getContadorADMSinCan();

        $IndicadorIRS03 = $EntityManager->getRepository("AppBundle:Indicador")->find(4);
        $EncargosPenalizadosALL = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS03,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null]);
        $encargosCumplen = $encargosADM - count($EncargosPenalizadosALL);
        $porcentaje = $encargosCumplen / $encargosADM;

        $factor = 0;
        if ($porcentaje > 0.95) $factor = 0;
        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
        if ($porcentaje <= 0.85) $factor = 1;
        $peso = round($IndicadorIRS03->getPeso() * $factor, 2);
        $importe = round($CertificadoServicios->getImporteCuotaFijaMensual() * $peso, 2);
        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorIRS03);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos($encargosADM);
        $Penalizacion->setTotalEncargosPenalizados(count($EncargosPenalizadosALL));
        $Penalizacion->setTotalCumplen($encargosCumplen);
        $Penalizacion->setPorcentaje($porcentaje);
        $Penalizacion->setFactor($factor);
        $Penalizacion->setPeso($peso);
        $Penalizacion->setImporte($importe);
        $EntityManager->persist($Penalizacion);
        $EntityManager->flush();
        return $importe;
    }

    /**
     * @param $CertificadoServicios CertificadoServicios
     * @return float
     */
    public
    function importePenalizacionENC01($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();

        $IndicadorENC01 = $EntityManager->getRepository("AppBundle:Indicador")->find(10);
        $EncargosPenalizadosALL = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorENC01,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null]);

        $importe = 0;
        foreach ($EncargosPenalizadosALL as $EncargoPenalizado) {
            $importe = $importe + $EncargoPenalizado->getImportePenalizacion();
        }

        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorENC01);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos(0);
        $Penalizacion->setTotalEncargosPenalizados(count($EncargosPenalizadosALL));
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje(0);
        $Penalizacion->setFactor(0);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte(round($importe, 2));
        $EntityManager->persist($Penalizacion);
        $EntityManager->flush();

        return $importe;
    }

    /**
     * @param $CertificadoServicios CertificadoServicios
     * @return float
     */
    public
    function importePenalizacionENC02($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $IndicadorENC02 = $EntityManager->getRepository("AppBundle:Indicador")->find(11);
        $EncargosPenalizadosALL = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorENC02,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null]);

        $importe = 0;
        foreach ($EncargosPenalizadosALL as $EncargoPenalizado) {
            $importe = $importe + $EncargoPenalizado->getImportePenalizacion();
        }

        $importe = round($importe, 2);
        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorENC02);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos(0);
        $Penalizacion->setTotalEncargosPenalizados(count($EncargosPenalizadosALL));
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje(0);
        $Penalizacion->setFactor(0);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte($importe, 2);
        $EntityManager->persist($Penalizacion);
        $EntityManager->flush();

        return $importe;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public
    function importePenalizacionENT01($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();

        $IndicadorENT01 = $EntityManager->getRepository("AppBundle:Indicador")->find(13);
        $EncargosPenalizadosALL = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorENT01,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null]);

        $importe = 0;
        foreach ($EncargosPenalizadosALL as $EncargoPenalizado) {
            $importe = $importe + $EncargoPenalizado->getImportePenalizacion();
        }

        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorENT01);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos(0);
        $Penalizacion->setTotalEncargosPenalizados(count($EncargosPenalizadosALL));
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje(0);
        $Penalizacion->setFactor(0);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte(round($importe, 2));
        $EntityManager->persist($Penalizacion);
        $EntityManager->flush();

        return $importe;
    }

    /**
     * NO PLANIFICABLE
     */
    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     * @throws DBALException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public
    function incluirNPL($CertificadoServicios, $ServicioLog, $ficheroLog)
    {
        /** @var EntityManager $EntityManager */
        $EntityManager = $this->getDoctrine()->getManager();
        $TipoObjetoEncargoNPL = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(1);
        $TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(1);

        $ObjetoRepository = $EntityManager->getRepository("AppBundle:ObjetoEncargo");
        $EncargoRepository = $EntityManager->getRepository("AppBundle:Encargo");
        $EstadoEncargoCRR = $EntityManager->getRepository("AppBundle:EstadoEncargo")->find(2);
        $EstadoEncargoFIN = $EntityManager->getRepository("AppBundle:EstadoEncargo")->find(12);
        $EstadoEncargoCAN = $EntityManager->getRepository("AppBundle:EstadoEncargo")->find(10);

        $ct = 0;
        $ctCAN = 0;
        $ctNPLCRI = 0;
        $ctNPLNOR = 0;

        $sentencia = "select encargo.id as id from encargo " .
            " inner join objetos_encargo as oe on oe.id = encargo.objeto_encargo_id " .
            " where fc_estado_actual between :fcini and :fcfin and estado_actual_id in (2,10,12) " .
            " and oe.tipo_objeto_id = 1 ";

        $conection = $this->getDoctrine()->getConnection();
        $stmt = $conection->prepare($sentencia);
        $params = ["fcini" => $CertificadoServicios->getMes()->getFechaInicio()->format('Y-m-d'),
            "fcfin" => $CertificadoServicios->getMes()->getFechaFin()->format('Y-m-d')];
        $stmt->execute($params);
        $EncargosNPL = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($EncargosNPL as $EncargoNPL) {
            /** @var Encargo $Encargo */
            $Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($EncargoNPL["id"]);
            /** @var Encargo $Existe */
            $Existe = $this->encargoEnCertificado($Encargo);
            if ($Existe) {
                $ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Existe->getTitulo());
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

            $Encargo->setBloqueado(true);
            $this->getDoctrine()->getManager()->persist($Encargo);
            $this->getDoctrine()->getManager()->flush();
            if ($Encargo->getCriticidad2()->getDescripcion() == 'NORMAL' and $Encargo->getEstadoActual() != $EstadoEncargoCAN) {
                $ctNPLNOR++;
            }
            if ($Encargo->getCriticidad2()->getDescripcion() == 'CRÍTICA' and $Encargo->getEstadoActual() != $EstadoEncargoCAN) {
                $ctNPLCRI++;
            }
            if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
                $ct++;
            } else {
                $ctCAN++;
            }
        }

        $CertificadoServicios->setContadorNPL($ct);
        $CertificadoServicios->setContadorCAN($ctCAN);
        $CertificadoServicios->setContadorNPLCRI($ctNPLCRI);
        $CertificadoServicios->setContadorNPLNOR($ctNPLNOR);

        $this->getDoctrine()->getManager()->persist($CertificadoServicios);
        $this->getDoctrine()->getManager()->flush();

        return true;

    }

    /**
     * @param Encargo $Encargo
     * @return CertificadoServicios|null
     */
    public
    function encargoEnCertificado($Encargo)
    {
        /** @var LineaCertificado $LineaServicios */
        $LineaServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:LineaCertificado")->findOneBy(["encargo" => $Encargo]);

        if ($LineaServicios) {
            return $LineaServicios->getCertificadoServicios();
        } else
            return null;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param EscribeLog $ServicioLog
     * @param FicheroLog $ficheroLog
     * @return bool
     * @throws Exception
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
        $IndicadorIRS03 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(4);

        $ObjetosEncargoAll = $ObjetoRepository->createQueryBuilder('u')
            ->where('u.tipoObjeto = :tipoObjeto')
            ->setParameter('tipoObjeto', $TipoObjetoEncargoADM)
            ->getQuery()->getResult();

        $ct = 0;
        $ctPenalizado = 0;
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
            $ctSinCan = 0;
            /** @var Encargo $Encargo */
            foreach ($Encargos as $Encargo) {
                /** @var CertificadoServicios $Existe */
                $Existe = $this->encargoEnCertificado($Encargo);
                if ($Existe) {
                    $ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Existe->getDescripcion());
                    $ServicioLog->escribeLog($ficheroLog);
                    continue;
                }
                $Encargo->getEstadoActual() == $EstadoEncargoCAN ? null : $ctSinCan++;
                $LineaCertificado = new LineaCertificado();
                $LineaCertificado->setCertificadoServicios($CertificadoServicios);
                $LineaCertificado->setTipoCuota($TipoCuota);
                $LineaCertificado->setEncargo($Encargo);
                $this->getDoctrine()->getManager()->persist($LineaCertificado);
                $this->getDoctrine()->getManager()->flush();
                $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO ");
                $ServicioLog->escribeLog($ficheroLog);

                $Encargo->setBloqueado(true);
                $EntityManager->persist($Encargo);
                $EntityManager->flush();
                $ct++;

            }
        }

        $CertificadoServicios->setContadorADM($ct);
        $CertificadoServicios->setContadorADMSinCan($ctSinCan);
        $this->getDoctrine()->getManager()->persist($CertificadoServicios);
        $this->getDoctrine()->getManager()->flush();

        return true;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     * @throws Exception
     */
    public
    function incluirSCFEJE($CertificadoServicios, $ServicioLog, $ficheroLog)
    {
        $EntityManager = $this->getDoctrine()->getManager();

        $TipoObjetoEncargoSCF = $this->getDoctrine()->getManager()->getRepository("AppBundle:TipoObjeto")->find(4);

        $ObjetoRepository = $EntityManager->getRepository("AppBundle:ObjetoEncargo");
        $EncargoRepository = $EntityManager->getRepository("AppBundle:Encargo");
        $EstadoEncargoEJE = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoEncargo")->find(5);


        $ObjetosEncargoAll = $ObjetoRepository->createQueryBuilder('u')
            ->where('u.tipoObjeto = :tipoObjeto')
            ->setParameter('tipoObjeto', $TipoObjetoEncargoSCF)
            ->getQuery()->getResult();
        foreach ($ObjetosEncargoAll as $ObjetosEncargo) {
            $Encargos = $EncargoRepository->createQueryBuilder('u')
                ->where('u.objetoEncargo =  :objetoEncargo')
                ->andWhere('u.estadoActual = :estadoEncargo')
                ->andWhere('u.fcComienzoEjecucion <= :fcfin and u.fcCompromiso >= :fcini')
                ->setParameter('estadoEncargo', $EstadoEncargoEJE)
                ->setParameter('objetoEncargo', $ObjetosEncargo)
                ->setParameter('fcini', $CertificadoServicios->getMes()->getFechaInicio())
                ->setParameter('fcfin', $CertificadoServicios->getMes()->getFechaFin())
                ->getQuery()->getResult();

            /** @var Encargo $Encargo */
            foreach ($Encargos as $Encargo) {
                $fechaInicio = clone $Encargo->getFcComienzoEjecucion();
                $fechaFin = clone $Encargo->getFcCompromiso();
                $diasTotales = $this->getDiasHabiles($fechaInicio, $fechaFin);

                if ($diasTotales == 0 or is_null($diasTotales)) $diasTotales = 1;
                $horasDia = round($Encargo->getHorasComprometidas() / $diasTotales, 2);

                $fechaInicio2 = clone $Encargo->getFcComienzoEjecucion();
                $fechaFin2 = clone $Encargo->getFcCompromiso();
                if ($fechaInicio2 < $CertificadoServicios->getMes()->getFechaInicio()) {
                    $fechaInicio2 = clone $CertificadoServicios->getMes()->getFechaInicio();
                }
                if ($fechaFin2 > $CertificadoServicios->getMes()->getFechaFin()) {
                    $fechaFin2 = clone $CertificadoServicios->getMes()->getFechaFin();
                }
                $diasMes = $this->getDiasHabiles($fechaInicio2, $fechaFin2);
                if ($diasMes == 0) $diasMes = 1;

                $LineaCertificadoEje = new LineaCertificadoEje();
                $LineaCertificadoEje->setCertificadoServicios($CertificadoServicios);
                $LineaCertificadoEje->setEncargo($Encargo);
                $LineaCertificadoEje->setHorasDia($horasDia);
                $LineaCertificadoEje->setDiasTotales($diasTotales);
                $LineaCertificadoEje->setDiasMes($diasMes);
                $LineaCertificadoEje->setHorasMes($horasDia * $diasMes);

                $this->getDoctrine()->getManager()->persist($LineaCertificadoEje);
                $this->getDoctrine()->getManager()->flush();
                $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO EN EJECUCIÓN ");
                $ServicioLog->escribeLog($ficheroLog);
            }
        }
        return true;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param EscribeLog $ServicioLog
     * @param FicheroLog $ficheroLog
     * @return bool
     * @throws Exception
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

        //$IndicadorIRS03 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(3);

        $ObjetosEncargoAll = $ObjetoRepository->createQueryBuilder('u')
            ->where('u.tipoObjeto = :tipoObjeto')
            ->setParameter('tipoObjeto', $TipoObjetoEncargoSCF)
            ->getQuery()->getResult();

        foreach ($ObjetosEncargoAll as $ObjetosEncargo) {
            //CERRADOS, FINALIZADOS
            $Encargos = $EncargoRepository->createQueryBuilder('u')
                ->where('u.objetoEncargo =  :objetoEncargo')
                ->andWhere('u.estadoActual in (:estadoEncargo1, :estadoEncargo2) ')
                ->andWhere('u.fcEstadoActual >= :fcini and u.fcEstadoActual <= :fcfin')
                ->setParameter('estadoEncargo1', $EstadoEncargoCRR)
                ->setParameter('estadoEncargo2', $EstadoEncargoFIN)
                ->setParameter('objetoEncargo', $ObjetosEncargo)
                ->setParameter('fcini', $CertificadoServicios->getMes()->getFechaInicio())
                ->setParameter('fcfin', $CertificadoServicios->getMes()->getFechaFin())
                ->getQuery()->getResult();

            /** @var Encargo $Encargo */
            foreach ($Encargos as $Encargo) {
                $Existe = $this->encargoEnCertificado($Encargo);
                if ($Existe) {
                    $ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Existe->getDescripcion());
                    $ServicioLog->escribeLog($ficheroLog);
                    continue;
                }
                $fechaInicio = clone $Encargo->getFcComienzoEjecucion();
                $fechaFin = clone $Encargo->getFcCompromiso();
                $diasTotales = $this->getDiasHabiles($fechaInicio, $fechaFin);
                if ($diasTotales == 0) $diasTotales = 1;
                $horasDia = round($Encargo->getHorasComprometidas() / $diasTotales, 2);

                $fechaInicio2 = clone $Encargo->getFcComienzoEjecucion();
                $fechaFin2 = clone $Encargo->getFcCompromiso();
                if ($fechaInicio2 < $CertificadoServicios->getMes()->getFechaInicio()) {
                    $fechaInicio2 = clone $CertificadoServicios->getMes()->getFechaInicio();
                }
                if ($fechaFin2 > $CertificadoServicios->getMes()->getFechaFin()) {
                    $fechaFin2 = clone $CertificadoServicios->getMes()->getFechaFin();
                }
                $diasMes = $this->getDiasHabiles($fechaInicio2, $fechaFin2);

                if ($diasMes == 0 or is_null($diasMes)) $diasMes = 1;
                $LineaCertificado = new LineaCertificado();
                $LineaCertificado->setCertificadoServicios($CertificadoServicios);
                $LineaCertificado->setTipoCuota($TipoCuota);
                $LineaCertificado->setEncargo($Encargo);
                $LineaCertificado->setHorasDia($horasDia);
                $LineaCertificado->setDiasTotales($diasTotales);
                $LineaCertificado->setDiasMes($diasMes);
                $LineaCertificado->setHorasMes($horasDia * $diasMes);

                $this->getDoctrine()->getManager()->persist($LineaCertificado);
                $this->getDoctrine()->getManager()->flush();
                $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO ");
                $ServicioLog->escribeLog($ficheroLog);

                $Encargo->setBloqueado(true);
                $this->getDoctrine()->getManager()->persist($Encargo);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return true;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param EscribeLog $ServicioLog
     * @param FicheroLog $ficheroLog
     * @return bool
     * @throws Exception
     */
    public
    function incluirPLA($CertificadoServicios, $ServicioLog, $ficheroLog)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $tipoCuotaVariable = $EntityManager->getRepository("AppBundle:TipoCuota")->find(2);

        /** @var Connection $conection */
        $conection = $this->getDoctrine()->getConnection();
        $sentencia = " select * from view_nuevos_desarrollos as vnd"
            . "  where vnd.estadoEncargoId in ( 2, 12 )"
            . "  and vnd.fechaEstadoActual >= :fcInicio "
            . " and vnd.fechaEstadoActual <= :fcFin";

        $stmt = $conection->prepare($sentencia);
        $params = [":fcInicio" => $CertificadoServicios->getMes()->getFechaInicio()->format('Y-m-d'),
            ":fcFin" => $CertificadoServicios->getMes()->getFechaFin()->format('Y-m-d')];
        $stmt->execute($params);
        $Encargos = $stmt->fetchAll();


        foreach ($Encargos as $encargo) {
            /** @var Encargo $Encargo */
            $Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($encargo["encargoId"]);

            $Existe = $this->encargoEnCertificado($Encargo);
            if ($Existe) {
                $ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Encargo->getTitulo());
                $ServicioLog->escribeLog($ficheroLog);
                continue;
            }

            if (is_null($Encargo->getAgrupacion())) {
                $ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " ERROR ENCARGO SIN AGRUPACIÓN " . $Encargo->getTitulo());
                $ServicioLog->escribeLog($ficheroLog);
                continue;
            }


            $LineaCertificado = new LineaCertificado();
            $LineaCertificado->setCertificadoServicios($CertificadoServicios);
            $LineaCertificado->setTipoCuota($tipoCuotaVariable);
            $LineaCertificado->setEncargo($Encargo);

            $this->getDoctrine()->getManager()->persist($LineaCertificado);
            $this->getDoctrine()->getManager()->flush();
            $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO EN CUOTA VARIABLE");
            $ServicioLog->escribeLog($ficheroLog);
            $Encargo->setBloqueado(true);
            $this->getDoctrine()->getManager()->persist($Encargo);
            $this->getDoctrine()->getManager()->flush();
        }

        return true;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     * @throws Exception
     */
    public
    function incluirTAS($CertificadoServicios, $ServicioLog, $ficheroLog)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $tipoCuotaTasada = $EntityManager->getRepository("AppBundle:TipoCuota")->find(3);

        /** @var Connection $conection */
        $conection = $this->getDoctrine()->getConnection();
        $sentencia = " select * from view_implantaciones as vi "
            . "  where vi.estadoEncargoId in ( 2, 12 )"
            . "  and vi.fechaEstadoActual >= :fcInicio "
            . " and vi.fechaEstadoActual <= :fcFin ";

        $stmt = $conection->prepare($sentencia);
        $params = [":fcInicio" => $CertificadoServicios->getMes()->getFechaInicio()->format('Y-m-d'),
            ":fcFin" => $CertificadoServicios->getMes()->getFechaFin()->format('Y-m-d')];
        $stmt->execute($params);
        $Encargos = $stmt->fetchAll();


        foreach ($Encargos as $encargo) {
            /** @var Encargo $Encargo */
            $Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($encargo["encargoId"]);

            $Existe = $this->encargoEnCertificado($Encargo);
            if ($Existe) {
                $ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " YA INCLUIDO EN CERTIFICADO SERVICIOS: " . $Encargo->getTitulo());
                $ServicioLog->escribeLog($ficheroLog);
                continue;
            }

            if (is_null($Encargo->getAgrupacion())) {
                $ServicioLog->setMensaje("Encargo: " . $Encargo->getNumero() . " ERROR ENCARGO SIN AGRUPACIÓN " . $Encargo->getTitulo());
                $ServicioLog->escribeLog($ficheroLog);
                continue;
            }

            $LineaCertificado = new LineaCertificado();
            $LineaCertificado->setCertificadoServicios($CertificadoServicios);
            /** @var TipoCuota $tipoCuotaTasada */
            $LineaCertificado->setTipoCuota($tipoCuotaTasada);
            $LineaCertificado->setEncargo($Encargo);
            $this->getDoctrine()->getManager()->persist($LineaCertificado);
            $this->getDoctrine()->getManager()->flush();
            $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO EN CUOTA TASADA");
            $ServicioLog->escribeLog($ficheroLog);
            $Encargo->setBloqueado(true);
            $this->getDoctrine()->getManager()->persist($Encargo);
            $this->getDoctrine()->getManager()->flush();
        }

        return true;

    }

    /**
     * @param Request $request
     * @param                                           $id
     * @return JsonResponse|Response
     * @throws Exception
     */
    public
    function queryEncargosAction(Request $request, $id)
    {

        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->get('sg_datatables.factory')->create(LineaCertificadoDatatable::class);
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

        return $this->render('certificadoServicios/queryEncargos.html.twig', [
            'datatable' => $datatable,
            'certificado' => $CertificadoServicios
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws Exception
     */
    public
    function revisionPenalizacionesAction(Request $request, $id)
    {

        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->get('sg_datatables.factory')->create(EncargoPenalizadoDatatable::class);
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

        return $this->render('certificadoServicios/queryEncargosPenalizados.html.twig', [
            'datatable' => $datatable,
            'certificado' => $CertificadoServicios
        ]);
    }

    /**
     * @param integer $id
     * @return bool
     */
    public
    function inicializaCertificadoServicios($id)
    {
        $EntityManager = $this->getDoctrine()->getManager();

        /** @var int $id */
        $CertificadoServicios = $EntityManager->getRepository("AppBundle:CertificadoServicios")->find($id);
        $EstadoCertificado = $EntityManager->getRepository("AppBundle:EstadoCertificado")->find(1);

        $CertificadoServicios->setEstadoCertificado($EstadoCertificado);
        $CertificadoServicios->setTotalFactura(0);
        $CertificadoServicios->setCuotaIva(0);
        $CertificadoServicios->setTotalFacturaConIva(0);
        $CertificadoServicios->setTotalPenalizaciones(0);
        $CertificadoServicios->setMaximoPenalizaciones(0);
        $CertificadoServicios->setPenalizacionAplicable(0);

        $EntityManager->persist($CertificadoServicios);
        $EntityManager->flush();

        return true;

    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public
    function quitarPenalizacionAction($id)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        /** @var EncargoPenalizado $EncargoPenalizado */
        /** @var int $id */
        $EncargoPenalizado = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->find($id);

        $EncargoPenalizado->setEliminada(true);
        $EntityManager->persist($EncargoPenalizado);
        $EntityManager->flush();

        $this->inicializaCertificadoServicios($EncargoPenalizado->getCertificadoServicios()->getId());

        return $this->redirectToRoute("revisionPenalizaciones",
            ["id" => $EncargoPenalizado->getCertificadoServicios()->getId()]);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public
    function activarPenalizacionAction($id)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        /** @var EncargoPenalizado $EncargoPenalizado */
        $EncargoPenalizado = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->find($id);

        $EncargoPenalizado->setEliminada(null);
        $EntityManager->persist($EncargoPenalizado);
        $EntityManager->flush();

        $this->inicializaCertificadoServicios($EncargoPenalizado->getCertificadoServicios()->getId());

        return $this->redirectToRoute("revisionPenalizaciones",
            ["id" => $EncargoPenalizado->getCertificadoServicios()->getId()]);
    }

    /**
     * @param int $lineaCertificado_id
     * @return RedirectResponse
     */
    public
    function excluirEncargoAction($lineaCertificado_id)
    {
        /** @var LineaCertificado $LineaCertificado */
        $LineaCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:LineaCertificado")->find($lineaCertificado_id);

        $this->inicializaCertificadoServicios($LineaCertificado->getCertificadoServicios()->getId());

        $Encargo = $LineaCertificado->getEncargo();
        $Encargo->setBloqueado(false);

        $this->getDoctrine()->getManager()->persist($Encargo);
        $this->getDoctrine()->getManager()->flush();

        $this->getDoctrine()->getManager()->remove($LineaCertificado);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute("queryCertificadoServicios");
    }

    /**
     * @param $id
     * @return RedirectResponse
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
        $params = ["id" => $CertificadoServicios->getId()];
        return $this->redirectToRoute("editCertificadoServicios", $params);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public
    function abrirAction($id)
    {
        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

        $EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);
        $CertificadoServicios->setEstadoCertificado($EstadoCertificado);

        $this->getDoctrine()->getManager()->persist($CertificadoServicios);
        $this->getDoctrine()->getManager()->flush();

        $status = "CERTIFICADO DE SERVICIO ABIERTO CORRECTAMENTE ";
        $this->sesion->getFlashBag()->add("status", $status);
        $params = ["id" => $CertificadoServicios->getId()];
        return $this->redirectToRoute("editCertificadoServicios", $params);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse|Response
     * @throws Exception
     */
    public
    function incluirEncargoAction(Request $request, $id)
    {
        /** @var EstadoCertificado $EstadoCertificado */
        $EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);

        /** @var CertificadoServicios $CertificadoServicios */
        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);

        $form = $this->createForm(AddEncargoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var Encargo $Encargo */

            $Encargo = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo")->findOneBy(["numero" => $form->getNormData("numeroEncargo")]);
            if ($Encargo) {
                /** @var LineaCertificado $LineaCertificado */
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
                    $params = ["id" => $CertificadoServicios->getId()];
                    return $this->redirectToRoute("editCertificadoServicios", $params);
                }
            } else {
                $status = "NUMERO DE ENCARGO INEXISTENTE ";
                $this->sesion->getFlashBag()->add("status", $status);
            }
        }

        return $this->render('certificadoServicios/addEncargos.html.twig', [
            'form' => $form->createView(),
            'certificado' => $CertificadoServicios
        ]);

    }

    /**
     * @param Encargo $Encargo
     * @return bool
     * @throws DBALException
     */
    public
    function esReapertura($Encargo)
    {
        /** @var Connection $conection */
        $conection = $this->getDoctrine()->getConnection();

        $sentencia = "select ver.numeroRemedy, count(*) as total from linea_certificado as lc "
            . " inner join encargo as e on e.id = lc.encargo_id "
            . " inner join view_encargos_remedy as ver on ver.encargoId = lc.encargo_id "
            . " where ver.numeroRemedy= :numeroRemedy and ver.numeroEncargo < e.numero "
            . " group by ver.numeroRemedy ";


        $stmt = $conection->prepare($sentencia);
        $params = [];
        $params[":numeroRemedy"] = $Encargo->getNmRemedy();
        $stmt->execute($params);
        $reApertura = $stmt->fetch();

        if ($reApertura["total"] > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param DateTime $fechainicio
     * @param DateTime $fechafin
     * @return int
     * @throws Exception
     */
    public
    function getDiasHabiles($fechainicio, $fechafin)
    {// Arreglo de dias habiles, inicianlizacion
		$diasNoHabiles = ['Sab', 'Sat', 'Dom', 'Sun'];
		$dias = 0;
		// Se recorre desde la fecha de inicio a la fecha fin, incrementando en 1 dia
		/** @var DateTime $midia */
		for ($midia = $fechainicio; $midia <= $fechafin; $midia->add(new DateInterval('P1D'))) {
			$dia = $midia->format('D');
			if (!in_array($dia, $diasNoHabiles)) {
				$dias++;
			}
		}

		return $dias;
	}


    /**
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public
    function cargaRevisionPenalizacionesAction(Request $request, $id)
    {
        /** @var EntityManager $EntityManager */
        $EntityManager = $this->getDoctrine()->getManager();
        $IndicadorIRIi = $EntityManager->getRepository("AppBundle:Indicador")->findBy(["codigo" => "IRI(i)"]);
        $CertificadoServicios = $EntityManager->getRepository("AppBundle:CertificadoServicios")->find($id);
        $ImportarForm = $this->createForm(ImportarType::class);
        $ImportarForm->handleRequest($request);

        if ($ImportarForm->isSubmitted()) {
            /** @var UploadedFile $fichero */
            $fichero = $ImportarForm["fichero"]->getData();
            if (!empty($fichero) && $fichero != null) {
                $file_name = $fichero->getClientOriginalName();
                $fichero->move("upload", $file_name);
                $file = "upload/" . $fichero->getClientOriginalName();
                $PHPExcel = IOFactory::load($file);
                $fecha = new DateTime();
                $CargaFichero = new CargaFichero();
                $CargaFichero->setFechaCarga($fecha);
                $CargaFichero->setDescripcion($ImportarForm["descripcion"]->getdata());
                $CargaFichero->setFichero($file_name);
                $Usuario = $this->getUser();
                $CargaFichero->setUsuario($Usuario);
                $EntityManager->persist($CargaFichero);
                $EntityManager->flush();

                $objWorksheet = $PHPExcel->setActiveSheetIndex(0);
                $highestRow = $objWorksheet->getHighestRow();
                $ct = 0;
                for ($i = 2; $i <= $highestRow; $i++) {
                    $headingsArray = $objWorksheet->rangeToArray('A' . $i . ':G' . $i, null, true, true, true);
                    $headingsArray = $headingsArray[$i];
                    if (strtoupper($headingsArray["G"]) == 'OK') {
                        $encargoPenalizadoId = (int)$headingsArray["B"];
                        $nmEncargo = (int)$headingsArray["C"];
                        /** @var Encargo $Encargo */
                        $Encargo = $EntityManager->getRepository("AppBundle:Encargo")->findBy(["numero" => $nmEncargo]);
                        /** @var EncargoPenalizado $EncargoPenalizado */
                        $EncargoPenalizado = $EntityManager->getRepository("AppBundle:EncargoPenalizado")
                            ->findOneBy(["encargo" => $Encargo,
                                "indicador" => $IndicadorIRIi,
                                "certificadoServicios" => $CertificadoServicios]);

                        $EncargoPenalizado->setEliminada(true);
                        $EncargoPenalizado->setJustificacionEliminacion($headingsArray["F"]);
                        $EntityManager->persist($EncargoPenalizado);
                        $EntityManager->flush();
                    }
                }
                $this->inicializaCertificadoServicios($id);
                $status = "REVISIÓN DE PENALIZACIONES CARGADA CORRECTAMENTE";
                $this->sesion->getFlashBag()->add("status", $status);
                $params = ["id" => $id];
                return $this->redirectToRoute("editCertificadoServicios", $params);
            }
        }

        $params = ["form" => $ImportarForm->createView()];
        return $this->render("cargaFichero/carga.html.twig", $params);

    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     *
     */
    public function editPenalizacionAction(Request $request, $id)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $EncargoPenalizado = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->find($id);
        $Encargo = $EncargoPenalizado->getEncargo();
        $CertificadoServicios = $EncargoPenalizado->getCertificadoServicios();

        $form = $this->createForm(EncargoPenalizadoType::class, $EncargoPenalizado);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $EntityManager->persist($EncargoPenalizado);
            $EntityManager->flush();
            $status = "PENAIZACIÓN REVISADA CORRECTAMENTE";
            $this->sesion->getFlashBag()->add("status", $status);
            $params = ["id" => $CertificadoServicios->getId()];
            return $this->redirectToRoute("editCertificadoServicios", $params);
        }
        return $this->render("certificadoServicios/editPenalizacion.html.twig", [
            "form" => $form->createView(),
            "accion" => "EDITAR",
            'certificadoServicios' => $CertificadoServicios,
            'encargo' => $Encargo]);
    }

    /**
     * @param $id
     * @return Response
     * @throws DBALException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public
    function exportarReaperturasAction($id)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        /** @var CertificadoServicios $CertificadoServicios */
        $CertificadoServicios = $EntityManager->getRepository("AppBundle:CertificadoServicios")->find($id);
        /** @var Connection $conection */
        $conection = $this->getDoctrine()->getConnection();

        $sentencia = " select * from view_encargos_penalizados "
            . "where certificadoServiciosid = :certificadoServiciosId and indicadorId = 6";

        $stmt = $conection->prepare($sentencia);
        $params = [];
        $params[":certificadoServiciosId"] = $id;
        $stmt->execute($params);
        $Reaperturas = $stmt->fetchAll();

        $reader = IOFactory::createReader('Xlsx');
        /** @var Spreadsheet $sheet */
        $Spreadsheet = $reader->load('plantillas/PlantillaReaperturas.xlsx');
        $sheet = $Spreadsheet->getActiveSheet();
        $sheet->setCellValue('B8', $CertificadoServicios->getMes()->getDescripcion());
        $row = 12;
        foreach ($Reaperturas as $Reapertura) {
//			$sheet->insertNewRowBefore($row, 1);
            $sheet->setCellValue('B' . $row, $Reapertura["encargoPenalizadoId"]);
            $sheet->setCellValue('C' . $row, $Reapertura["encargoNumero"]);
            $sheet->setCellValue('D' . $row, $Reapertura["numeroRemedy"]);
            $sheet->setCellValue('E' . $row, $Reapertura["encargoTitulo"]);
            $sheet->setCellValue('F' . $row, '');
            $sheet->setCellValue('g' . $row, 'NO');
            $row++;
        }

        $rango = "B12:G" . $row;
        $estiloArray = ['font' => ['name' => 'Calibri',
            'bold' => false,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['rgb' => '808080']],
            'fill' => ['fill' => Fill::FILL_NONE,
                'color' => []],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_DASHDOT,
                'color' => ['rgb' => '808080']],
                'top' => ['borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '808080']]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,],
            'quotePrefix' => true];

        $sheet->getStyle($rango)->applyFromArray($estiloArray);

        $writer = new Xlsx($Spreadsheet);
        $fechaActual = new DateTime();
        $filename = 'Reaperturas-' . $CertificadoServicios->getId() . '-' . $fechaActual->format('Ymd-His') . '.xlsx';
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


    public
    function ajaxPeriodoAction($idAnyo)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $Anyo = $EntityManager->getRepository("AppBundle:Anyo")->find($idAnyo);

        $Periodos = $EntityManager->getRepository("AppBundle:Mes")->findBy(["anyo" => $Anyo]);

        $html = " <select id='formCertificadoServicios_mes' " .
            " name='formCertificadoServicios[mes]' " .
            " required='required' " .
            " class='form-control'>" .
            " <option value='' selected='selected'>Seleccione mes ....</option> ";
        foreach ($Periodos as $Periodo) {
            $opcion = " <option value=' " . $Periodo->getId() . "'>" . $Periodo->getDescripcion() . "</option> ";
            $html = $html . $opcion;
        }
        $html = $html . "</select>";

        return new Response($html);

    }

    /**
     * @param int $id
     * @return Response
     */
    public
    function queryHorasCuotaFijaAction($id)
    {

        $CertificadoServicios = $this->getDoctrine()->getManager()->getRepository("AppBundle:CertificadoServicios")->find($id);
        $conection = $this->getDoctrine()->getConnection();
        $sentencia = " select * from view_horas_cuota_fija where CertificadoServiciosId = :id";

        $stmt = $conection->prepare($sentencia);
        $params = ["id" => $id];
        $stmt->execute($params);
        $HorasCuotaFija = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $params = ["HorasCuotaFija" => $HorasCuotaFija, "certificadoServicios" => $CertificadoServicios];
        return $this->render("certificadoServicios/queryHorasCF.html.twig", $params);


    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return ImportesContrato|object|null
     */
    public
    function importesContrato($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $Contrato = $CertificadoServicios->getContrato();
        $Anyo = $CertificadoServicios->getMes()->getAnyo();
        return $EntityManager->getRepository("AppBundle:ImportesContrato")->findOneBy(["contrato" => $Contrato, 'anyo' => $Anyo]);
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @throws DBALException
     */
    public function calculaPenalizaciones($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $LineasCertificadoServicio = $EntityManager->getRepository("AppBundle:LineaCertificado")
            ->findBy(["certificadoServicios" => $CertificadoServicios]);

        $ctpenalizadosIRS01 = 0;
        $ctpenalizadosIRS02 = 0;
        $ctpenalizadosIRIi = 0;
        $ctpenalizadosIRS03 = 0;
        $ctpenalizadosENC01 = 0;
        $ctpenalizadosENC02 = 0;
        $ctpenalizadosENT01 = 0;
        $ctpenalizadosENT02 = 0;

        foreach ($LineasCertificadoServicio as $LineaCertificadoServicio) {
            $Encargo = $LineaCertificadoServicio->getEncargo();
            $TipoObjeto = $Encargo->getObjetoEncargo()->getTipoObjeto();
            switch ($TipoObjeto->getCodigo()) {
                case "NPL";
                    $this->penalizacionIRS01($CertificadoServicios, $Encargo) ? $ctpenalizadosIRS01++ : null;
                    $this->penalizacionIRS02($CertificadoServicios, $Encargo) ? $ctpenalizadosIRS02++ : null;
                    $this->penalizacionIRIi($CertificadoServicios, $Encargo) ? $ctpenalizadosIRIi++ : null;
                    break;
                case "ADM";
                    $this->penalizacionIRS03($CertificadoServicios, $Encargo) ? $ctpenalizadosIRS03++ : null;
                    break;
                case "SCF":
                case "PLA":
                    $this->penalizacionENC01($CertificadoServicios, $Encargo) ? $ctpenalizadosENC01++ : null;
                    $this->penalizacionENC02($CertificadoServicios, $Encargo) ? $ctpenalizadosENC02++ : null;
                    break;
                case "TAS":
                    $this->penalizacionENT01($CertificadoServicios, $Encargo) ? $ctpenalizadosENT01++ : null;
                    $this->penalizacionENT02($CertificadoServicios, $Encargo) ? $ctpenalizadosENT02++ : null;
                    break;
            }
        }
        $CertificadoServicios->setPenalizadosCRI($ctpenalizadosIRS01);
        $CertificadoServicios->setPenalizadosNOR($ctpenalizadosIRS02);
        $CertificadoServicios->setPenalizadosReaperturas($ctpenalizadosIRIi);
        $CertificadoServicios->setPenalizadosADM($ctpenalizadosIRS03);
        $CertificadoServicios->setPenalizadosENC01($ctpenalizadosENC01);
        $CertificadoServicios->setPenalizadosENC02($ctpenalizadosENC02);
        $CertificadoServicios->setPenalizadosENT01($ctpenalizadosENT01);
        $CertificadoServicios->setPenalizadosENT02($ctpenalizadosENT02);

        $EntityManager->persist($CertificadoServicios);
        $EntityManager->flush();;

        return;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param Encargo $Encargo
     * @return bool
     */
    public function penalizacionIRS01($CertificadoServicios, $Encargo)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $EstadoEncargoCAN = $EntityManager->getRepository("AppBundle:EstadoEncargo")->find(10);
        $IndicadorIRS01 = $EntityManager->getRepository("AppBundle:Indicador")->find(1);
        if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
            if ($Encargo->getCriticidad2()->getDescripcion() == 'CRÍTICA' and $Encargo->getTiempoResolucion() > 14400000) {
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                $EncargoPenalizado->setIndicador($IndicadorIRS01);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                return true;
            }
        }
        return false;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param Encargo $Encargo
     * @return bool
     */
    public function penalizacionIRS02($CertificadoServicios, $Encargo)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $EstadoEncargoCAN = $EntityManager->getRepository("AppBundle:EstadoEncargo")->find(10);
        $IndicadorIRS02 = $EntityManager->getRepository("AppBundle:Indicador")->find(3);
        if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {

            if ($Encargo->getCriticidad2()->getDescripcion() == 'NORMAL' and $Encargo->getTiempoResolucion() > 144000000) {
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                $EncargoPenalizado->setIndicador($IndicadorIRS02);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                return true;
            }
        }
        return false;
    }


    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param Encargo $Encargo
     * @return bool
     * @throws DBALException
     */
    public function penalizacionIRIi($CertificadoServicios, $Encargo)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $EstadoEncargoCAN = $EntityManager->getRepository("AppBundle:EstadoEncargo")->find(10);
        $IndicadorIRIi = $EntityManager->getRepository("AppBundle:Indicador")->find(6);
        if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
            if ($this->esReapertura($Encargo)) {
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                $EncargoPenalizado->setIndicador($IndicadorIRIi);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                return true;

            }
        }

        return false;

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param Encargo $Encargo
     * @return bool
     * @throws Exception
     */

    public function penalizacionIRS03($CertificadoServicios, $Encargo)
    {
        $EntityManager = $this->getDoctrine()->getManager();

        $IndicadorIRS03 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(4);

        if (!is_null($Encargo->getFcRequeridaEntrega())) {
            if ($Encargo->getFcEntrega() > $Encargo->getFcRequeridaEntrega()->add(new DateInterval(('P1D')))) {
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                $EncargoPenalizado->setIndicador($IndicadorIRS03);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                return true;
            }
        }
        return false;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param Encargo $Encargo
     * @return bool
     * @throws Exception
     */
    public function penalizacionENC01($CertificadoServicios, $Encargo)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $IndicadorENC01 = $EntityManager->getRepository("AppBundle:Indicador")->find(10);

        $fechaInicio = $Encargo->getFcRequeridaValoracion();
        $fechaFin = $Encargo->getFcEntregaValoracion();

        if ($fechaFin > $fechaInicio) {
//            $diff = $fechaInicio->diff($fechaFin);
//            $diasRetrasoValoracion = $diff->format('%a');
            $diasRetrasoValoracion = $this->getDiasHabiles($fechaInicio, $fechaFin);
            if ($diasRetrasoValoracion > 0) {
                $ImportesContrato = $this->importesContrato($CertificadoServicios);
                $thpCs = $ImportesContrato->getTarifaHoraCs();
                $importePenalizacion = $diasRetrasoValoracion * 8 * 2 * $thpCs;
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                $EncargoPenalizado->setIndicador($IndicadorENC01);
                $EncargoPenalizado->setDiasRetrasoValoracion($diasRetrasoValoracion);
                $EncargoPenalizado->setImportePenalizacion($importePenalizacion);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                return true;
            }
        }
        return false;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param Encargo $Encargo
     * @return bool
     */
    public function penalizacionENC02($CertificadoServicios, $Encargo)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $IndicadorENC02 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(11);

        $fechaInicio = $Encargo->getFcCompromiso();
        $fechaFin = $Encargo->getFcEntrega();

        $diff = $fechaInicio->diff($fechaFin);
        $diasRetrasoEntrega = $diff->format('%a');
        if ($fechaFin > $fechaInicio) {
            if ($diasRetrasoEntrega > 0) {
                $ImportesContrato = $this->importesContrato($CertificadoServicios);
                $thpCs = $ImportesContrato->getTarifaHoraCs();
                $fechaInicio = $Encargo->getFcComienzoEjecucion();
                $fechaFin = $Encargo->getFcCompromiso();
                $diff = $fechaInicio->diff($fechaFin);
                $diasPrevistosEjecucion = $diff->format("%a");
                $diasPrevistosEjecucion == 0 ? $factor = $diasRetrasoEntrega : $factor = $diasRetrasoEntrega / $diasPrevistosEjecucion;
                if ($Encargo->getCriticidad() == 0) {
                    $factor = $factor / 2;
                }
                /**
                 * Para encargos de Cuota Variable se coje el importe aceptado por MD, para encargos de cuota fihja se coge el numero de horas por tarifa
                 * de Consultor senior
                 */
                if ($Encargo->getObjetoEncargo()->getTipoObjeto()->getCodigo() == 'PLA') {
                    $costeEncargo = $Encargo->getCoste();
                } else {
                    $costeEncargo = $thpCs * $Encargo->getHorasComprometidas();
                }

                $importePenalizacion = $factor * $costeEncargo;
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                $EncargoPenalizado->setIndicador($IndicadorENC02);
                $EncargoPenalizado->setDiasRetrasoEntrega($diasRetrasoEntrega);
                $EncargoPenalizado->setDiasPrevistosEjecucion($diasPrevistosEjecucion);
                $EncargoPenalizado->setEncargo($Encargo);
                $EncargoPenalizado->setImportePenalizacion($importePenalizacion);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                return true;
            }
        }
        return false;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param Encargo $Encargo
     * @return bool
     */
    public
    function penalizacionENT01($CertificadoServicios, $Encargo)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        /** @var Indicador $IndicadorENT01 */
        $IndicadorENT01 = $EntityManager->getRepository("AppBundle:Indicador")->find(13);

        $fechaInicio = $Encargo->getFcCompromiso();
        $fechaFin = $Encargo->getFcEntrega();

        $diff = $fechaInicio->diff($fechaFin);
        $diasRetrasoEntrega = $diff->format('%a');
        $diff2 = $Encargo->getFcComienzoEjecucion()->diff($Encargo->getFcCompromiso());
        $diasEjecucion = $diff2->format('%a');

        if ($diasRetrasoEntrega > 0) {
            $fechaInicio = $Encargo->getFcComienzoEjecucion();
            $fechaFin = $Encargo->getFcEntrega();
            $diff = $fechaInicio->diff($fechaFin);
            $diasPrevistosEjecucion = $diff->format("%a");
            $diasPrevistosEjecucion == 0 ? $factor = $diasRetrasoEntrega : $factor = $diasRetrasoEntrega / $diasPrevistosEjecucion;

            if ($Encargo->getCriticidad() == 0) {
                $factor = $factor / 2;
            }

            $importePenalizacion = $factor * $Encargo->getCoste();
            $EncargoPenalizado = new EncargoPenalizado();
            $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
            $EncargoPenalizado->setIndicador($IndicadorENT01);
            $EncargoPenalizado->setEncargo($Encargo);
            $EncargoPenalizado->setDiasRetrasoEntrega($diasRetrasoEntrega);
            $EncargoPenalizado->setDiasPrevistosEjecucion($diasPrevistosEjecucion);
            $EncargoPenalizado->setImportePenalizacion($importePenalizacion);
            $EncargoPenalizado->setDiasEjecucion($diasEjecucion);
            $EntityManager->persist($EncargoPenalizado);
            $EntityManager->flush();
            return true;
        }

        return false;

    }


    public function penalizacionENT02($CertificadoServicios, $Encargo)
    {
        /** @var Indicador $IndicadorENT02 */
        $IndicadorENT02 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(14);

    }
}


