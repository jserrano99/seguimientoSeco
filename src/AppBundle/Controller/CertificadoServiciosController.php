<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\CertificadoServiciosDatatable;
use AppBundle\Datatables\EncargoPenalizadoDatatable;
use AppBundle\Datatables\LineaCertificadoDatatable;
use AppBundle\Entity\CertificadoServicios;
use AppBundle\Entity\Encargo;
use AppBundle\Entity\EncargoPenalizado;
use AppBundle\Entity\FicheroLog;
use AppBundle\Entity\ImportesCertificado;
use AppBundle\Entity\Indicador;
use AppBundle\Entity\LineaCertificado;
use AppBundle\Entity\Penalizacion;
use AppBundle\Entity\TipoCuota;
use AppBundle\Form\AddEncargoType;
use AppBundle\Form\CertificadoServiciosType;
use AppBundle\Servicios\EscribeLog;
use DateInterval;
use DateTime;
use Doctrine\ORM\Query\ResultSetMapping;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Query\ResultSetMappingesultSetMapping;


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

        return $this->render('certificadoServicios/query.html.twig', array(
            'datatable' => $datatable,
        ));
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

        if ($form->isSubmitted()) {
        }

        return $this->render("certificadoServicios/edit.html.twig", array(
            "form" => $form->createView(),
            "accion" => "EDITAR",
            'estado' => $CertificadoServicios->getEstadoCertificado()));

    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
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
                return $this->render("certificadoServicios/edit.html.twig", [
                    "form" => $form->createView(),
                    "accion" => "GENERAR",
                    'estado' => 'PENDIENTE']);
            } else {
                $EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);
                $Contrato = $this->getDoctrine()->getManager()->getRepository("AppBundle:Contrato")->find(1);
                $CertificadoServicios->setEstadoCertificado($EstadoCertificado);
                $CertificadoServicios->setContrato($Contrato);
                $CertificadoServicios->setDescripcion("Certificado de Servicios " . $CertificadoServicios->getMes()->getDescripcion());
                $ImportesContrato = $this->getDoctrine()->getManager()->getRepository("AppBundle:ImportesContrato")->findOneBy(["contrato" => $Contrato]);
                $CertificadoServicios->setImporteCuotaFijaMensual($ImportesContrato->getCuotaFijaMensual());
                $this->getDoctrine()->getManager()->persist($CertificadoServicios);
                $this->getDoctrine()->getManager()->flush();
                $this->generarCertificado($CertificadoServicios);
                return $this->redirectToRoute("queryCertificadoServicios");
            }
        }
        return $this->render("certificadoServicios/edit.html.twig", [
            "form" => $form->createView(),
            "accion" => "GENERAR",
            'estado' => 'PENDIENTE'
        ]);

    }

    /**
     * @param $id
     * @return RedirectResponse
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
     * @param CertificadoServicios $CertificadoServicios
     * @return mixed
     * @throws Exception
     */

    public function generarCertificado($CertificadoServicios)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $FicheroLog = new FicheroLog();
        $fechaProceso = new DateTime();
        $FicheroLog->setFechaProceso($fechaProceso);
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
     * @return RedirectResponse
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
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public function aplicaPenalizacion($CertificadoServicios)
    {
        $conection = $this->getDoctrine()->getConnection();

        $this->penalizacionNPL($CertificadoServicios);
        $this->penalizacionIRIi($CertificadoServicios);
        $this->penalizacionIRQ01($CertificadoServicios);
        $this->penalizacionPLA($CertificadoServicios);
        $this->penalizacionTAS($CertificadoServicios);

        $sentencia = " select sum(importe) as totalPenalizacion from penalizaciones "
            . " where  certificado_servicios_id = :id";


        $stmt = $conection->prepare($sentencia);
        $params = [];
        $params[":id"] = $CertificadoServicios->getId();
        $stmt->execute($params);
        $penalizaciones = $stmt->fetch();

        return $penalizaciones["totalPenalizacion"];
    }


    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public function generarImportes($CertificadoServicios)
    {

        $sentencia = " delete from importes_certificado_servicios  "
            . " where  certificado_servicios_id = :id";

        $conexion = $this->getDoctrine()->getConnection()->prepare($sentencia);
        $params = [":id" => $CertificadoServicios->getId()];
        $conexion->execute($params);

        $sentencia = " delete from penalizaciones  "
            . " where  certificado_servicios_id = :id";

        $conexion = $this->getDoctrine()->getConnection()->prepare($sentencia);
        $params = [":id" => $CertificadoServicios->getId()];
        $conexion->execute($params);

        $ImporteCuotaFija = $this->importesCuotaFija($CertificadoServicios);
        $ImporteCuotaVariable = $this->importesCuotaVariable($CertificadoServicios);
        $ImporteCuotaTasada = $this->importesCuotaTasada($CertificadoServicios);

        $totalFactura = $ImporteCuotaFija->getImporte() + $ImporteCuotaVariable->getImporte() + $ImporteCuotaTasada->getImporte();
        $maximoPenalizaciones = $totalFactura * 0.20;
        $importePenalizacion = $this->aplicaPenalizacion($CertificadoServicios);


        IF ($importePenalizacion > $maximoPenalizaciones) {
            $penalizacionAplicable = $maximoPenalizaciones;
        } else {
            $penalizacionAplicable = $importePenalizacion;
        }
        $cuotaIVA = $totalFactura * 0.21;
        $totalFacturaIVA = $totalFactura + $cuotaIVA - $penalizacionAplicable;

        $CertificadoServicios->setTotalFactura($totalFactura);
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
     * @return ImportesCertificado
     */
    public function importesCuotaTasada($CertificadoServicios)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $TipoCuota = $EntityManager->getRepository("AppBundle:TipoCuota")->find(3);

        $LineaCertificadoAll = $EntityManager->getRepository("AppBundle:LineaCertificado")
            ->findBy(["certificadoServicios" => $CertificadoServicios, "tipoCuota" => $TipoCuota]);

        $importe = 0;
        foreach ($LineaCertificadoAll as $LineaCertificado) {

            /** @var LineaCertificado $LineaCertificado */
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
     * @param CertificadoServicios $CertificadoServicios
     * @return ImportesCertificado
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
        foreach ($LineaCertificadoAll as $LineaCertificado) {
            $horas = $horas + $LineaCertificado->getEncargo()->getHorasComprometidas();
            $importe = $importe + ($LineaCertificado->getEncargo()->getHorasComprometidas() * $tarifa);
        }

        $ImportesCertificado = new ImportesCertificado();
        $ImportesCertificado->setCertificadoServicios($CertificadoServicios);
        $ImportesCertificado->setCodigo(2);
        $ImportesCertificado->setTipoCuota($TipoCuota);
        $ImportesCertificado->setDescripcion("Servicio de Evolución de Amplio Alcance");
        $ImportesCertificado->setHorasCertificadas($horas);
        $ImportesCertificado->setTarifa($tarifa);
        $ImportesCertificado->setImporte($importe);
        $ImportesCertificado->setPenalizacion(0);
        $ImportesCertificado->setTotal(0);
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
        $entityManager = $this->getDoctrine()->getManager();
        $TipoCuota = $entityManager->getRepository("AppBundle:TipoCuota")->find(1);

        $ImportesContrato = $entityManager->getRepository("AppBundle:ImportesContrato")->findOneBy(["contrato" => $CertificadoServicios->getContrato()]);

        //$CertificadoServicios->getAplicaPenalizacion() == null ? $totalPenalización = 0 : $totalPenalización = $this->penalizacionNPL($CertificadoServicios);

        //$totalPenalizacion = $this->penalizacionNPL($CertificadoServicios);

        $ImportesCertificado = new ImportesCertificado();
        $ImportesCertificado->setCertificadoServicios($CertificadoServicios);
        $ImportesCertificado->setCodigo(1);
        $ImportesCertificado->setTipoCuota($TipoCuota);
        $ImportesCertificado->setDescripcion("Servicio de Atención, Soporte, Mantenimiento y Evolución de Corto Alcance");
        $ImportesCertificado->setHorasCertificadas(null);
        $ImportesCertificado->setTarifa(null);
        $ImportesCertificado->setImporte($ImportesContrato->getCuotaFijaMensual());
        $ImportesCertificado->setPenalizacion(0);
        $ImportesCertificado->setTotal($ImportesContrato->getCuotaFijaMensual());
        $entityManager->persist($ImportesCertificado);
        $entityManager->flush();


        return $ImportesCertificado;
    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public function penalizacionIRQ01($CertificadoServicios)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $RemedyRepo = $entityManager->getRepository("AppBundle:Remedy");

        $QuejasPeriodo = $RemedyRepo->createQueryBuilder('u')
            ->where("u.tipo = 'QUEJAS'")
            ->andWhere('u.fechaModificacion between :fcInicio and :fcFin')
            ->setParameter('fcInicio', $CertificadoServicios->getMes()->getfechaInicio())
            ->setParameter('fcFin', $CertificadoServicios->getMes()->getfechaFin())
            ->getQuery()->getResult();

        $nmQuejas = count($QuejasPeriodo);
        $IndicadorIRQ01 = $entityManager->getRepository("AppBundle:Indicador")->find(5);

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
        $entityManager->persist($Penalizacion);
        $entityManager->flush();

        return true;

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public function penalizacionIRIi($CertificadoServicios)
    {


        $entityManager = $this->getDoctrine()->getManager();

        $IndicadorIRIi = $entityManager->getRepository("AppBundle:Indicador")->find(6);

        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRIi,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null ]);

        $totalEncargos = $CertificadoServicios->getContadorNPLCRI() + $CertificadoServicios->getContadorNPLNOR();
        $encargosPenalizados = count($EncargosPenalizadosALL);

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
        $entityManager->persist($Penalizacion);
        $entityManager->flush();

        return true;

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public function penalizacionNPL($CertificadoServicios)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $encargosNPLCRI = $CertificadoServicios->getContadorNPLCRI();
        $encargosNPLNOR = $CertificadoServicios->getContadorNPLNOR();

        $IndicadorIRS01 = $entityManager->getRepository("AppBundle:Indicador")->find(1);
        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS01,
                                                                                            "certificadoServicios" => $CertificadoServicios,
                                                                                            "eliminada" => null ]);
        $encargosCumplen = $encargosNPLCRI - count($EncargosPenalizadosALL);

        $porcentaje = $encargosCumplen / $encargosNPLCRI;

        $factor = 0;
        if ($porcentaje > 0.95) $factor = 0;
        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
        if ($porcentaje <= 0.85) $factor = 1;
        $peso = $IndicadorIRS01->getPeso() * $factor;

        $importe = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;
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

        $entityManager->persist($Penalizacion);
        $entityManager->flush();

        $IndicadorIRS02 = $entityManager->getRepository("AppBundle:Indicador")->find(3);
        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS02,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null ]);
        $encargosCumplen = $encargosNPLNOR - count($EncargosPenalizadosALL);
        $porcentaje = $encargosCumplen / $encargosNPLNOR;
        $factor = 0;

        if ($porcentaje > 0.95) $factor = 0;
        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
        if ($porcentaje <= 0.85) $factor = 1;

        $peso = $IndicadorIRS02->getPeso() * $factor;
        $importe = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;

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

        $entityManager->persist($Penalizacion);
        $entityManager->flush();


        $IndicadorIRS03 = $entityManager->getRepository("AppBundle:Indicador")->find(4);
        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS03,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null ]);
        $encargosCumplen = $encargosNPLNOR - count($EncargosPenalizadosALL);
        $porcentaje = $encargosCumplen / $encargosNPLNOR;
        $factor = 0;

        if ($porcentaje > 0.95) $factor = 0;
        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
        if ($porcentaje <= 0.85) $factor = 1;

        $peso = $IndicadorIRS02->getPeso() * $factor;
        $importe = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;

        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorIRS03);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargosPenalizados(count($EncargosPenalizadosALL));
        $Penalizacion->setTotalEncargos($encargosNPLNOR);
        $Penalizacion->setTotalCumplen($encargosCumplen);
        $Penalizacion->setPorcentaje($porcentaje);
        $Penalizacion->setFactor($factor);
        $Penalizacion->setPeso($peso);
        $Penalizacion->setImporte($importe);

        $entityManager->persist($Penalizacion);
        $entityManager->flush();

        return true;

    }

    /**
     * @param CertificadoServicios $CertificadoServicios
     * @return bool
     */
    public
    function penalizacionADM($CertificadoServicios)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $encargosADM = $CertificadoServicios->getContadorADM();

        $IndicadorIRS03 = $entityManager->getRepository("AppBundle:Indicador")->find(4);
        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorIRS03,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null ]);
        $encargosCumplen = $encargosADM - count($EncargosPenalizadosALL);

        $porcentaje = $encargosCumplen / $encargosADM;

        $factor = 0;
        if ($porcentaje > 0.95) $factor = 0;
        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
        if ($porcentaje <= 0.85) $factor = 1;
        $peso = $IndicadorIRS03->getPeso() * $factor;

        $importe = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;
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
        $entityManager->persist($Penalizacion);
        $entityManager->flush();
        return true;
    }

    /**
     * @param $CertificadoServicios CertificadoServicios
     * @return float
     */
    public function penalizacionPLA($CertificadoServicios)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $IndicadorENC01 = $entityManager->getRepository("AppBundle:Indicador")->find(10);
        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorENC01,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null ]);

//        $encargosCumplen = $encargosADM - count($EncargosPenalizadosALL);
//        $porcentaje = $encargosCumplen / $encargosADM;
//
//        $factor = 0;
//        if ($porcentaje > 0.95) $factor = 0;
//        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
//        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
//        if ($porcentaje <= 0.85) $factor = 1;
//        $peso = $IndicadorIRS03->getPeso() * $factor;
//        $importe = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;

        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorENC01);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos(0);
        $Penalizacion->setTotalEncargosPenalizados(0);
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje(0);
        $Penalizacion->setFactor(0);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte(0);
        $entityManager->persist($Penalizacion);
        $entityManager->flush();

        $IndicadorENC02 = $entityManager->getRepository("AppBundle:Indicador")->find(11);
        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorENC02,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null ]);

//        $encargosCumplen = $encargosADM - count($EncargosPenalizadosALL);
//        $porcentaje = $encargosCumplen / $encargosADM;
//
//        $factor = 0;
//        if ($porcentaje > 0.95) $factor = 0;
//        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
//        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
//        if ($porcentaje <= 0.85) $factor = 1;
//        $peso = $IndicadorIRS03->getPeso() * $factor;

//        $importe = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;
        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorENC02);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos(0);
        $Penalizacion->setTotalEncargosPenalizados(0);
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje(0);
        $Penalizacion->setFactor(0);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte(0);
        $entityManager->persist($Penalizacion);
        $entityManager->flush();

        return true;
    }

    public
    function penalizacionTAS($CertificadoServicios)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $IndicadorENT01 = $entityManager->getRepository("AppBundle:Indicador")->find(13);
        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorENT01,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null ]);

//        $encargosCumplen = $encargosADM - count($EncargosPenalizadosALL);
//        $porcentaje = $encargosCumplen / $encargosADM;
//
//        $factor = 0;
//        if ($porcentaje > 0.95) $factor = 0;
//        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
//        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
//        if ($porcentaje <= 0.85) $factor = 1;
//        $peso = $IndicadorIRS03->getPeso() * $factor;

//        $importe = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;
        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorENT01);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos(0);
        $Penalizacion->setTotalEncargosPenalizados(0);
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje(0);
        $Penalizacion->setFactor(0);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte(0);
        $entityManager->persist($Penalizacion);
        $entityManager->flush();

        $IndicadorENT02 = $entityManager->getRepository("AppBundle:Indicador")->find(14);
        $EncargosPenalizadosALL = $entityManager->getRepository("AppBundle:EncargoPenalizado")->findBy(["indicador" => $IndicadorENT02,
            "certificadoServicios" => $CertificadoServicios,
            "eliminada" => null ]);

//        $encargosCumplen = $encargosADM - count($EncargosPenalizadosALL);
//        $porcentaje = $encargosCumplen / $encargosADM;
//
//        $factor = 0;
//        if ($porcentaje > 0.95) $factor = 0;
//        if ($porcentaje > 0.90 and $porcentaje <= 0.95) $factor = 0.5;
//        if ($porcentaje > 0.85 and $porcentaje <= 0.90) $factor = 0.75;
//        if ($porcentaje <= 0.85) $factor = 1;
//        $peso = $IndicadorIRS03->getPeso() * $factor;

//        $importe = $CertificadoServicios->getImporteCuotaFijaMensual() * $peso;
        $Penalizacion = new Penalizacion();
        $Penalizacion->setIndicador($IndicadorENT02);
        $Penalizacion->setCertificadoServicios($CertificadoServicios);
        $Penalizacion->setTotalEncargos(0);
        $Penalizacion->setTotalEncargosPenalizados(0);
        $Penalizacion->setTotalCumplen(0);
        $Penalizacion->setPorcentaje(0);
        $Penalizacion->setFactor(0);
        $Penalizacion->setPeso(0);
        $Penalizacion->setImporte(0);
        $entityManager->persist($Penalizacion);
        $entityManager->flush();

        return true;
    }

    /**
     * NO PLANIFICABLE
     */
    /**
     * @param CertificadoServicios $CertificadoServicios
     * @param EscribeLog $ServicioLog
     * @param FicheroLog $ficheroLog
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
        $IndicadorIRIi = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(6);

        $ObjetosEncargoNPLAll = $ObjetoRepository->createQueryBuilder('u')
            ->where('u.tipoObjeto = :tipoObjeto')
            ->setParameter('tipoObjeto', $TipoObjetoEncargoNPL)
            ->getQuery()->getResult();

        $ct = 0;
        $ctCAN = 0;
        $ctPenalizaoosCRI = 0;
        $ctPenalizaoosNOR = 0;
        $ctNPLCRI = 0;
        $ctNPLNOR = 0;


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
                        $ctPenalizaoosCRI++;
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
                        $ctPenalizaoosNOR++;
                    }

                    if ($this->esReapertura($Encargo)) {
                        $EncargoPenalizado = new EncargoPenalizado();
                        $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                        $EncargoPenalizado->setIndicador($IndicadorIRIi);
                        $EncargoPenalizado->setEncargo($Encargo);
                        $EntityManager->persist($EncargoPenalizado);
                        $EntityManager->flush();
                        $ServicioLog->setMensaje("Encargo " . $Encargo->getNumero() . " *** PENALIZADO *** ");
                        $ServicioLog->escribeLog($ficheroLog);
//                        $ctPenalizaoosIri++;
                    }


                }
                $Encargo->setBloqueado(true);
                $this->getDoctrine()->getManager()->persist($Encargo);
                $this->getDoctrine()->getManager()->flush();
                if ($Encargo->getCriticidad() == 0 and $Encargo->getEstadoActual() != $EstadoEncargoCAN) {
                    $ctNPLNOR++;
                }
                if ($Encargo->getCriticidad() == 3 and $Encargo->getEstadoActual() != $EstadoEncargoCAN) {
                    $ctNPLCRI++;
                }
                if ($Encargo->getEstadoActual() != $EstadoEncargoCAN) {
                    $ct++;
                } else {
                    $ctCAN++;
                }
            }
        }

        $CertificadoServicios->setContadorNPL($ct);
        $CertificadoServicios->setContadorCAN($ctCAN);
        $CertificadoServicios->setPenalizadosCRI($ctPenalizaoosCRI);
        $CertificadoServicios->setPenalizadosNOR($ctPenalizaoosNOR);
        $CertificadoServicios->setContadorNPLCRI($ctNPLCRI);
        $CertificadoServicios->setContadorNPLNOR($ctNPLNOR);

        $this->getDoctrine()->getManager()->persist($CertificadoServicios);
        $this->getDoctrine()->getManager()->flush();

        return true;

    }

    /**
     * @param Encargo $Encargo
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
            $ctPenalizado = 0;
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
                    if ($Encargo->getFcEntrega() > $Encargo->getFcRequeridaEntrega()->add(new DateInterval(('P1D')))) {
                        $EncargoPenalizado = new EncargoPenalizado();
                        $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                        $EncargoPenalizado->setIndicador($IndicadorIRS03);
                        $EncargoPenalizado->setEncargo($Encargo);
                        $EntityManager->persist($EncargoPenalizado);
                        $EntityManager->flush();
                        $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " *** PENALIZADO *** ");
                        $ServicioLog->escribeLog($ficheroLog);
                        $ctPenalizado++;
                    }
                }
                $Encargo->setBloqueado(true);
                $this->getDoctrine()->getManager()->persist($Encargo);
                $this->getDoctrine()->getManager()->flush();
                $ct++;
            }
        }

        $CertificadoServicios->setContadorADM($ct);
        $CertificadoServicios->setPenalizadosADM($ctPenalizado);
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
        $IndicadorENC01 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(10);
        $IndicadorENC02 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(11);
        $tipoCuotaVariable = $EntityManager->getRepository("AppBundle:TipoCuota")->find(2);

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

            $fechaInicio = $Encargo->getFcRequeridaValoracion();
            $fechaFin = $Encargo->getFcEntregaValoracion();
            $diasRetrasoValoracion = $this->getDiasHabiles($fechaInicio, $fechaFin);

            if ($diasRetrasoValoracion > 0) {
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                $EncargoPenalizado->setIndicador($IndicadorENC01);
                $EncargoPenalizado->setDiasRetrasoValoracion($diasRetrasoValoracion);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " *** PENALIZADO  ENC01*** ");
                $ServicioLog->escribeLog($ficheroLog);
            }

            $fechaInicio = $Encargo->getFcRequeridaEntrega();
            $fechaFin = $Encargo->getFcEntrega();
            $diasRetrasoEntrega = $this->getDiasHabiles($fechaInicio, $fechaFin);

            if ($diasRetrasoEntrega > 0) {
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                $EncargoPenalizado->setIndicador($IndicadorENC02);
                $EncargoPenalizado->setDiasRetrasoEntrega($diasRetrasoEntrega);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " *** PENALIZADO  ENC02*** ");
                $ServicioLog->escribeLog($ficheroLog);
            }

            $Encargo->setBloqueado(true);
            $this->getDoctrine()->getManager()->persist($Encargo);
            $this->getDoctrine()->getManager()->flush();
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
    function incluirTAS($CertificadoServicios, $ServicioLog, $ficheroLog)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $IndicadorENT01 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(13);
        $IndicadorENT02 = $this->getDoctrine()->getManager()->getRepository("AppBundle:Indicador")->find(14);
        $tipoCuotaTasada = $EntityManager->getRepository("AppBundle:TipoCuota")->find(3);

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
            /** @var TipoCuota $tipoCuotaVariable */
            $LineaCertificado->setTipoCuota($tipoCuotaTasada);
            $LineaCertificado->setEncargo($Encargo);
            $this->getDoctrine()->getManager()->persist($LineaCertificado);
            $this->getDoctrine()->getManager()->flush();
            $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " INCLUIDO EN CUOTA TASADA");
            $ServicioLog->escribeLog($ficheroLog);

            $fechaInicio = $Encargo->getFcRequeridaValoracion();
            $fechaFin = $Encargo->getFcEntregaValoracion();
            $diasRetrasoValoracion = $this->getDiasHabiles($fechaInicio, $fechaFin);

            if ($diasRetrasoValoracion > 0) {
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                /** @var Indicador $IndicadorENT01 */
                $EncargoPenalizado->setIndicador($IndicadorENT01);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " *** PENALIZADO  ENT01*** ");
                $ServicioLog->escribeLog($ficheroLog);
            }

            $fechaInicio = $Encargo->getFcRequeridaEntrega();
            $fechaFin = $Encargo->getFcEntrega();
            $diasRetrasoEntrega = $this->getDiasHabiles($fechaInicio, $fechaFin);

            if ($diasRetrasoEntrega > 0) {
                $EncargoPenalizado = new EncargoPenalizado();
                $EncargoPenalizado->setCertificadoServicios($CertificadoServicios);
                /** @var Indicador $IndicadorENT02 */
                $EncargoPenalizado->setIndicador($IndicadorENT02);
                $EncargoPenalizado->setEncargo($Encargo);
                $EntityManager->persist($EncargoPenalizado);
                $EntityManager->flush();
                $ServicioLog->setMensaje("+Encargo: " . $Encargo->getNumero() . " *** PENALIZADO  ENT02*** ");
                $ServicioLog->escribeLog($ficheroLog);
            }

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

        return $this->render('certificadoServicios/queryEncargos.html.twig', array(
            'datatable' => $datatable,
            'certificado' => $CertificadoServicios
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
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

        return $this->render('certificadoServicios/queryEncargosPenalizados.html.twig', array(
            'datatable' => $datatable,
            'certificado' => $CertificadoServicios
        ));
    }

    /**
     * @param integer $id
     * @return bool
     */
    public function inicializaCertificadoServicios($id)
    {
        $EntityManager = $this->getDoctrine()->getManager();

        $CertificadoServicios = $EntityManager->getRepository("AppBundle:CertificadoServicios")->find($id);
        $EstadoCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:EstadoCertificado")->find(1);

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

    public function quitarPenalizacionAction($id)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $EncargoPenalizado = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->find($id);

        $EncargoPenalizado->setEliminada(true);
        $EntityManager->persist($EncargoPenalizado);
        $EntityManager->flush();

        $this->inicializaCertificadoServicios($EncargoPenalizado->getCertificadoServicios()->getId());

        return $this->redirectToRoute("revisionPenalizaciones",
            ["id" => $EncargoPenalizado->getCertificadoServicios()->getId()]);
    }


    public function activarPenalizacionAction($id)
    {

        $EntityManager = $this->getDoctrine()->getManager();
        $EncargoPenalizado = $EntityManager->getRepository("AppBundle:EncargoPenalizado")->find($id);

        $EncargoPenalizado->setEliminada(null);
        $EntityManager->persist($EncargoPenalizado);
        $EntityManager->flush();

        $this->inicializaCertificadoServicios($EncargoPenalizado->getCertificadoServicios()->getId());

        return $this->redirectToRoute("revisionPenalizaciones",
            ["id" => $EncargoPenalizado->getCertificadoServicios()->getId()]);
    }

    /**
     * @param $lineaCertificado_id
     * @return RedirectResponse
     */
    public
    function excluirEncargoAction($lineaCertificado_id)
    {
        $LineaCertificado = $this->getDoctrine()->getManager()->getRepository("AppBundle:LineaCertificado")->find($lineaCertificado_id);

        $this->inicializaCertificadoServicios($LineaCertificado->getCertificadoServicios()->getId());

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

        return $this->redirectToRoute("queryCertificadoServicios");
    }

    /**
     * @param Request $request
     * @param                                           $id
     * @return JsonResponse|Response
     * @throws Exception
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

    /**
     * @param Encargo $Encargo
     * @return bool
     */
    public function esReapertura($Encargo)
    {

        $conection = $this->getDoctrine()->getConnection();

        $sentencia = " select ver.numeroRemedy, count(*)-1 as total from linea_certificado as lc "
            . " inner join view_encargos_remedy as ver on ver.encargoId = lc.encargo_id "
            . " where ver.numeroRemedy= :numeroRemedy "
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

    public function getDiasHabiles($fechainicio, $fechafin)
    {
        // Arreglo de dias habiles, inicianlizacion
        $diasNoHabiles = ['Sab', 'Sat', 'Dom', 'Sun'];
        $dias = 0;
        // Se recorre desde la fecha de inicio a la fecha fin, incrementando en 1 dia
        for ($midia = $fechainicio; $midia <= $fechafin; $midia->add(new DateInterval('P1D'))) {
            $dia = $midia->format('D');
            if (!in_array($dia, $diasNoHabiles)) {
                $dias++;
            }
        }

        return $dias;
    }

}
