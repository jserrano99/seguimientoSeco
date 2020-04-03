<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\CargaFicheroDatatable;
use AppBundle\Entity\AnotacionEncargo;
use AppBundle\Entity\Aplicacion;
use AppBundle\Entity\CargaFichero;
use AppBundle\Entity\Centro;
use AppBundle\Entity\EncargoRemedy;
use AppBundle\Entity\EstadoEncargo;
use AppBundle\Entity\FicheroLog;
use AppBundle\Entity\Encargo;
use AppBundle\Entity\Fichero;
use AppBundle\Entity\Remedy;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\UsuarioRemedy;
use AppBundle\Form\FicheroSecoType;
use AppBundle\Form\ImportarType;
use AppBundle\Servicios\EscribeLog;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ReflectionObject;
use ReflectionProperty;
use ReflectionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CargaFicheroController
 * @package AppBundle\Controller
 */
class CargaFicheroController extends Controller
{

    private $sesion;

    public function __construct()
    {
        $this->sesion = new Session();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function queryAction(Request $request)
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->get('sg_datatables.factory')->create(CargaFicheroDatatable::class);
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
     * @param int $id
     * @return Response
     */
    public function descargaLogAction($id)
    {

        /** @var CargaFichero $CargaFichero */
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
     * @return RedirectResponse|Response
     */

    public function cargaAction(Request $request)
    {
        /** @var EntityManager $EntityManager */
        $EntityManager = $this->getDoctrine()->getManager();
        $ImportarForm = $this->createForm(ImportarType::class);
        $ImportarForm->handleRequest($request);

        if ($ImportarForm->isSubmitted()) {
            /** @var UploadedFile $fichero */
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
                    /** @var Usuario $Usuario */
                    $Usuario = $this->getUser();
                    $CargaFichero->setUsuario($Usuario);
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();

                    $FicheroLog = new FicheroLog();
                    $fechaProceso = new DateTime();
                    $FicheroLog->setFechaProceso($fechaProceso);

                    $ServicioLog = $this->get('app.escribelog');
                    $ServicioLog->setLogger('CARGA FICHERO : ID= ' . $CargaFichero->getId());
                    /** @var string $ficheroLog */
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

                    $EntityManager->persist($FicheroLog);
                    $EntityManager->flush();


                    $CargaFichero->setFicheroLog($FicheroLog);
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();
                    $params = ["CargaFichero" => $CargaFichero];
                    return $this->render("cargaFichero/finProceso.html.twig", $params);

                } catch (Exception $e) {
                    $status = "***ERROR EN CARGA DE FICHERO **: " . $file_name;
                    $this->sesion->getFlashBag()->add("status", $status);
                    $status = $e->getMessage();
                    $this->sesion->getFlashBag()->add("status", $status);
                    $params = [""];
                    return $this->render("cargaFichero/errorProceso.html.twig", $params);
                }
            }
        }
        $params = ["form" => $ImportarForm->createView()];
        return $this->render("cargaFichero/carga.html.twig", $params);
    }

    /**
     * @param int $id
     * @return RedirectResponse
     *
     */
    public function deleteAction($id)
    {
        $EntityManager = $this->getDoctrine()->getManager();

        $CargaFichero = $EntityManager->getRepository("AppBundle:CargaFichero")->find($id);

        $EntityManager->remove($CargaFichero);
        $EntityManager->flush();

        return $this->redirectToRoute("queryFichero");

    }

    /**
     * @param CargaFichero $CargaFichero
     * @param Spreadsheet $PHPExcel
     * @return bool
     * @throws Exception
     */
    public function cargaFichero($CargaFichero, $PHPExcel)
    {
        /** @var EntityManager $EntityManager */
        $EntityManager = $this->getDoctrine()->getManager();

        $objWorksheet = $PHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();

        $ct = 0;
        for ($i = 2; $i <= $highestRow; $i++) {
            if (!$EntityManager->isOpen()) {
                $EntityManager = $EntityManager->create($EntityManager->getConnection(), $EntityManager->getConfiguration());
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

            $EntityManager->persist($Fichero);
            $EntityManager->flush();
            $ct++;

        }

        $CargaFichero->setNumeroRegistros($ct);
        $EntityManager->persist($CargaFichero);
        $EntityManager->flush();

        return true;
    }

    /**
     * @param CargaFichero $CargaFichero
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     * @throws Exception
     */
    public function cargaEncargos($CargaFichero, $ServicioLog, $ficheroLog)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $FicheroAll = $this->getDoctrine()->getManager()->getRepository("AppBundle:Fichero")->findBy(["cargaFichero" => $CargaFichero]);

        $ct = 0;
        /** @var Fichero $Fichero */
        foreach ($FicheroAll as $Fichero) {
            $Encargo = $EntityManager->getRepository("AppBundle:Encargo")->findByNumero($Fichero->getNumeroEncargo());
            $nuevo = false;
            if (!$Encargo) {
                $this->crearEncargo($Fichero, $ServicioLog, $ficheroLog);
            } else {
                if ($Encargo->isBloqueado()) {
                    $ServicioLog->setMensaje(" **** Encargo= " . $Encargo->getNumero() . " Bloqueado ");
                    $ServicioLog->escribeLog($ficheroLog);
                    continue;
                }

                $EncargoANT = clone $Encargo;
                $EncargoNew = $this->actualizaEncargo($Encargo, $Fichero, $ServicioLog, $ficheroLog);
                if ($EncargoANT === $EncargoNew) {
                    null;
                } else {
                    $diff = [];
                    if (get_class($Encargo) == get_class($EncargoNew)) {
                        $o1Properties = (new ReflectionObject($EncargoANT))->getProperties();
                        $o2Reflected = new ReflectionObject($EncargoNew);
                        $ServicioLog->setMensaje(" **** Encargo= " . $Encargo->getNumero() . " MODIFICACIONES: ");
                        $ServicioLog->escribeLog($ficheroLog);
                        foreach ($o1Properties as $o1Property) {
                            $o2Property = $o2Reflected->getProperty($o1Property->getName());
                            // Mark private properties as accessible only for reflected class
                            $o1Property->setAccessible(true);
                            $o2Property->setAccessible(true);
                            $oldValue = $o1Property->getValue($EncargoANT);
                            $newValue = $o2Property->getValue($EncargoNew);
                            if ($oldValue != $newValue) {
                                $diff[$o1Property->getName()] = [
                                    'OLD_VALUE' => $oldValue,
                                    'NEW_VALUE' => $newValue];
                                $this->imprimeLog($o1Property, $oldValue, $newValue, $ServicioLog, $ficheroLog);
                            }
                        }
                    }
                }
            }
            $ct++;
        }

        $CargaFichero->setNumeroRegistrosCargados($ct);
        $EntityManager->persist($CargaFichero);
        $EntityManager->flush();

        return true;
    }

    /**
     * @param ReflectionProperty $o1Property
     * @param ReflectionProperty $oldValue
     * @param ReflectionProperty $newValue
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     */
    public function imprimeLog($o1Property, $oldValue, $newValue, $ServicioLog, $ficheroLog)
    {
        try {
            $mensaje = ' Propiedad ->' . $o1Property->getName();
            $msj1 = " Valor Anterior : ";
            $msj2 = " Valor Nuevo : ";
            if (is_a($oldValue, "DateTime")) {
                is_null($oldValue) ? $valorAnterior = "" : $valorAnterior = $oldValue->format('d-m-Y');
                is_null($newValue) ? $valorNuevo = "" : $valorNuevo = $newValue->format('d-m-Y');
            } else {
                $valorAnterior = $oldValue;
                $valorNuevo = $newValue;
            }

            $mensaje = $mensaje . $msj1 . " [" . $oldValue . "]" . $msj2 . "[" . $valorNuevo . "]";
            $ServicioLog->setMensaje($mensaje);
            $ServicioLog->escribeLog($ficheroLog);
            return true;
        } catch (Exception $e) {
            $ServicioLog->setMensaje("error al escribir ". $mensaje);
            $ServicioLog->escribeLog($ficheroLog);
            return false;
        }
    }

    /**
     * @param Encargo $Encargo
     * @param Fichero $Fichero
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return Encargo
     * @throws Exception
     */
    public function actualizaEncargo($Encargo, $Fichero, $ServicioLog, $ficheroLog)
    {
        /** @var string $cod1 */
        $cod1 = $Encargo->getEstadoActual()->getCodigo();
        /** @var string $cod2 */
        $cod2 = $Fichero->getEstadoActual();
        $EntityManager = $this->getDoctrine()->getManager();
        if ($cod1 == $cod2) {
            null;
        } else {
            /** @var EstadoEncargo $nuevoEstado */
            $NuevoEstado = $EntityManager->getRepository("AppBundle:EstadoEncargo")->findByCodigo($Fichero->getEstadoActual());
            $anotacion = "Se Cambia a estado : " . $NuevoEstado->getDescripcion();
            $AnotacionEncargo = new AnotacionEncargo();
            $fecha = new DateTime();
            $AnotacionEncargo->setEncargo($Encargo);
            $AnotacionEncargo->setFecha($fecha);
            $AnotacionEncargo->setAnotacion($anotacion);
            /** @var Usuario $Usuario */
            $Usuario = $this->getUser();
            $AnotacionEncargo->setUsuario($Usuario);
            $EntityManager->persist($AnotacionEncargo);
            $EntityManager->flush();
        }

        $Encargo->setTitulo($Fichero->getTitulo());
//        $Encargo->setDescripcion($Fichero->getDescripcion());
        $EstadoActual = $EntityManager->getRepository("AppBundle:EstadoEncargo")->findByCodigo($Fichero->getEstadoActual());
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
        $Criticidad = $EntityManager->getRepository("AppBundle:Criticidad")->findOneBy(["codigo" => $Fichero->getCriticidad()]);
        $Encargo->setCriticidad2($Criticidad);

        $Encargo->setTiempoTotal($Fichero->getTiempoTotal());
        $Encargo->setTiempoResolucion($Fichero->getTiempoResolucion());

        $Aplicacion = $EntityManager->getRepository("AppBundle:Aplicacion")->findByCodigo($Fichero->getAplicacion());
        $Encargo->setAplicacion($Aplicacion);

        $ModuloFuncional = $EntityManager->getRepository("AppBundle:ModuloFuncional")->findByCodigo($Fichero->getModuloFuncional());
        $Encargo->setModuloFuncional($ModuloFuncional);

        $ModuloTecnico = $EntityManager->getRepository("AppBundle:ModuloTecnico")->findByCodigo($Fichero->getModuloTecnico());
        $Encargo->setModuloTecnico($ModuloTecnico);
        $Encargo->setHorasValoradas($Fichero->getHorasValoradas());
        $Encargo->setHorasComprometidas($Fichero->getHorasComprometidas());
        $Encargo->setHorasRealizadas($Fichero->getHorasRealizadas());
        if ($Encargo->getObjetoEncargo()->getTipoCuota()->getId() == 2) {
            $Encargo->setCoste(round($Encargo->getHorasComprometidas() * 37.47, 2));
        }
        if ($Encargo->getObjetoEncargo()->getTipoCuota()->getId() == 3) {
            $Encargo->getCoste() > 0 ? null : $Encargo->setCoste(0);
        }
        $TipoSolucion = $EntityManager->getRepository("AppBundle:TipoSolucion")->findByCodigo($Fichero->getTipoSolucion());
        $Encargo->setTipoSolucion($TipoSolucion);
        $Encargo->setSolucionUsuario($Fichero->getSolucionUsuario());
        $Encargo->setSolucionTecnica($Fichero->getSolucionTecnica());
        $Operacional1 = $EntityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional1());
        $Operacional2 = $EntityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional2());
        $Operacional3 = $EntityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional3());
        $Encargo->setOperacional1($Operacional1);
        $Encargo->setOperacional2($Operacional2);
        $Encargo->setOperacional3($Operacional3);
        $Encargo->setMotivoCancelacion($Fichero->getMotivoCancelacion());
        $EntityManager->persist($Encargo);
        $EntityManager->flush();

        return $Encargo;
    }

    /**
     * @param Fichero $Fichero
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @throws Exception
     */
    public function crearEncargo($Fichero, $ServicioLog, $ficheroLog)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $Encargo = new Encargo();

        $Encargo->setNumero($Fichero->getNumeroEncargo());
        $Encargo->setNmRemedy($Fichero->getNumeroRemedy());
        $ObjetoEncargo = $EntityManager->getRepository("AppBundle:ObjetoEncargo")->findByCodigo($Fichero->getObjetoEncargo());
        $Encargo->setObjetoEncargo($ObjetoEncargo);

        if ($Fichero->getContrato() == null)
            $Contrato = $EntityManager->getRepository("AppBundle:Contrato")->findByCodigo('1099');
        else
            $Contrato = $EntityManager->getRepository("AppBundle:Contrato")->findByCodigo($Fichero->getContrato());

        $Encargo->setContrato($Contrato);
        $Encargo->setNmRemedy($Fichero->getNumeroRemedy());

        if ($ObjetoEncargo->getTipoObjeto()->getId() != 1) {
            $Agrupacion = $EntityManager->getRepository("AppBundle:Agrupacion")->findByCodigo($Fichero->getNumeroAgrupacion());
            if (is_null($Agrupacion)) {
                $ServicioLog->setMensaje(" >>>> Encargo= " . $Encargo->getNumero() . " Agrupación inexistente: " . $Fichero->getNumeroAgrupacion());
                $ServicioLog->escribeLog($ficheroLog);
            } else {
                $Encargo->setAgrupacion($Agrupacion);
            }
        }

        $Encargo->setTitulo($Fichero->getTitulo());
        $Encargo->setDescripcion(null);
        $EstadoActual = $EntityManager->getRepository("AppBundle:EstadoEncargo")->findByCodigo($Fichero->getEstadoActual());
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
        $Criticidad = $EntityManager->getRepository("AppBundle:Criticidad")->findOneBy(["codigo" => $Fichero->getCriticidad()]);
        $Encargo->setCriticidad2($Criticidad);

        $Encargo->setTiempoTotal($Fichero->getTiempoTotal());
        $Encargo->setTiempoResolucion($Fichero->getTiempoResolucion());

        $Aplicacion = $EntityManager->getRepository("AppBundle:Aplicacion")->findByCodigo($Fichero->getAplicacion());
        $Encargo->setAplicacion($Aplicacion);

        $ModuloFuncional = $EntityManager->getRepository("AppBundle:ModuloFuncional")->findByCodigo($Fichero->getModuloFuncional());
        $Encargo->setModuloFuncional($ModuloFuncional);

        $ModuloTecnico = $EntityManager->getRepository("AppBundle:ModuloTecnico")->findByCodigo($Fichero->getModuloTecnico());
        $Encargo->setModuloTecnico($ModuloTecnico);
        $Encargo->setHorasValoradas($Fichero->getHorasValoradas());
        $Encargo->setHorasComprometidas($Fichero->getHorasComprometidas());
        $Encargo->setHorasRealizadas($Fichero->getHorasRealizadas());
        if ($Encargo->getObjetoEncargo()->getTipoCuota()->getId() == 2) {
            $Encargo->setCoste(round($Encargo->getHorasComprometidas() * 37.47, 2));
        }
        if ($Encargo->getObjetoEncargo()->getTipoCuota()->getId() == 3) {
            $Encargo->getCoste() > 0 ? null : $Encargo->setCoste(0);
        }
        $TipoSolucion = $EntityManager->getRepository("AppBundle:TipoSolucion")->findByCodigo($Fichero->getTipoSolucion());
        $Encargo->setTipoSolucion($TipoSolucion);
        $Encargo->setSolucionUsuario($Fichero->getSolucionUsuario());
        $Encargo->setSolucionTecnica($Fichero->getSolucionTecnica());
        $Operacional1 = $EntityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional1());
        $Operacional2 = $EntityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional2());
        $Operacional3 = $EntityManager->getRepository("AppBundle:Operacional")->findByCodigo($Fichero->getOperacional3());
        $Encargo->setOperacional1($Operacional1);
        $Encargo->setOperacional2($Operacional2);
        $Encargo->setOperacional3($Operacional3);
        $Encargo->setMotivoCancelacion($Fichero->getMotivoCancelacion());
        $EntityManager->persist($Encargo);
        $EntityManager->flush();

        /**
         * Generación de Anotaciones para los Planificables y Adaptaciones Menores
         */
        if ($Encargo->getObjetoEncargo()->getTipoObjeto()->getId() != 1 or
            $Encargo->getObjetoEncargo()->getCodigo() == 'ENP') {
            $AnotacionEncargo = new AnotacionEncargo();
            $fecha = new DateTime();
            $AnotacionEncargo->setEncargo($Encargo);
            $AnotacionEncargo->setFecha($fecha);
            $AnotacionEncargo->setAnotacion("Creación del Encargo");
            /** @var Usuario $Usuario */
            $Usuario = $this->getUser();
            $AnotacionEncargo->setUsuario($Usuario);
            $EntityManager->persist($AnotacionEncargo);
            $EntityManager->flush();
        }
        $ServicioLog->setMensaje(" >>>> Encargo= " . $Encargo->getNumero() . " Creado  <<<< ");
        $ServicioLog->escribeLog($ficheroLog);
        return;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function cargaFicheroRemedyAction(Request $request)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $ImportarForm = $this->createForm(ImportarType::class);
        $ImportarForm->handleRequest($request);

        if ($ImportarForm->isSubmitted()) {
            /** @var UploadedFile $fichero */
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
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();

                    $FicheroLog = new FicheroLog();
                    $fechaProceso = new DateTime();
                    $FicheroLog->setFechaProceso($fechaProceso);

                    $ServicioLog = $this->get('app.escribelog');
                    $ServicioLog->setLogger('CARGA FICHERO Remedy: ID= ' . $CargaFichero->getId());
                    $ficheroLog = 'FicheroLog-' . $CargaFichero->getId() . ".log";

                    $ServicioLog->setMensaje("Comienza carga fichero remedy: " . $file);
                    $ServicioLog->escribeLog($ficheroLog);

                    $this->cargaRemedy($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog);
                    $ServicioLog->setMensaje("Finaliza carga fichero remedy: " . $file . " Registros Totales :" . $CargaFichero->getNumeroRegistros());
                    $ServicioLog->escribeLog($ficheroLog);
                    $FicheroLog->setNombreFichero($ServicioLog->getFileName());
                    $EntityManager->persist($FicheroLog);
                    $EntityManager->flush();

                    $CargaFichero->setFicheroLog($FicheroLog);
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();
                    $params = ["CargaFichero" => $CargaFichero];
                    return $this->render("cargaFichero/finProceso.html.twig", $params);

                } catch (Exception $e) {
                    $status = "***ERROR EN CARGA DE FICHERO **: " . $file_name;
                    $this->sesion->getFlashBag()->add("status", $status);
                    $params = [];
                    return $this->render("cargaFichero/carga.html.twig", $params);
                }
            }
        }
        $params = ["form" => $ImportarForm->createView()];
        return $this->render("cargaFichero/carga.html.twig", $params);
    }

    /**
     * @param CargaFichero $CargaFichero
     * @param Spreadsheet $PHPExcel
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function cargaRemedy($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog)
    {
        $EntityManager = $this->getDoctrine()->getManager();

        $objWorksheet = $PHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();

        $ct = 0;
        for ($i = 2; $i <= $highestRow; $i++) {
            /** @var EntityManager $EntityManager */
            if (!$EntityManager->isOpen()) {
                $EntityManager = $EntityManager->create($EntityManager->getConnection(), $EntityManager->getConfiguration());
            }
            $headingsArray = $objWorksheet->rangeToArray('A' . $i . ':Q' . $i, null, true, true, true);
            $headingsArray = $headingsArray[$i];

            $Remedy = $EntityManager->getRepository("AppBundle:Remedy")->findOneBy(["numero" => $headingsArray["B"]]);

            if (is_null($Remedy)) {
                $Remedy = new Remedy();
            }
            /** @var Aplicacion $Aplicacion */
            $Aplicacion = $EntityManager->getRepository("AppBundle:Aplicacion")->findOneBy(["codigo" => $headingsArray["J"]]);

            if (is_null($Aplicacion)) {
                $ServicioLog->setMensaje(" **** Ticket :  " . $headingsArray["B"] . " aplicación no tratada : " . $headingsArray["J"]);
                $ServicioLog->escribeLog($ficheroLog);
                continue;
            }

            $Centro = $EntityManager->getRepository("AppBundle:Centro")->findOneBy(["descripcion" => $headingsArray["G"]]);
            if (is_null($Centro)) {
                $Centro = new Centro();
                $Centro->setDescripcion($headingsArray["G"]);
                $EntityManager->persist($Centro);
                $EntityManager->flush();
                $ServicioLog->setMensaje(" **** Generado Centro= " . $Centro->getDescripcion());
                $ServicioLog->escribeLog($ficheroLog);
            }

            $UsuarioRemedy = $EntityManager->getRepository("AppBundle:UsuarioRemedy")->findOneBy(["login" => $headingsArray["M"]]);

            if (is_null($UsuarioRemedy)) {
                $UsuarioRemedy = $EntityManager->getRepository("AppBundle:UsuarioRemedy")->findOneBy(["apellidos" => $headingsArray["H"]]);
                if (is_null($UsuarioRemedy)) {
                    $UsuarioRemedy = new UsuarioRemedy();
                    $UsuarioRemedy->setLogin($headingsArray["M"]);
                    $UsuarioRemedy->setApellidos($headingsArray["H"]);
                    $UsuarioRemedy->setNombre($headingsArray["I"]);
                    $UsuarioRemedy->setCentro($Centro);
                    $EntityManager->persist($UsuarioRemedy);
                    $EntityManager->flush();
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

            $MesAll = $EntityManager->getRepository("AppBundle:Mes")
                ->createQueryBuilder('u')
                ->where(':fecha between u.fechaInicio and u.fechaFin')
                ->setParameter('fecha', $Remedy->getFechaModificacion())
                ->getQuery()->getResult();
            $Mes = $MesAll[0];


            $headingsArray["P"] == null ? $Remedy->setFechaCierre(null) : $Remedy->setFechaCierre(DateTime::createFromFormat('d/m/Y H:i:s', $headingsArray["P"]));

            $Remedy->setLogin($headingsArray["M"]);
            $Remedy->setDescripcionProblema($headingsArray["N"]);
            $Remedy->setMes($Mes);
            $EntityManager->persist($Remedy);
            $EntityManager->flush();
            $ServicioLog->setMensaje(" **** Generado Ticket . " . $Remedy->getNumero() . " Estado: " . $Remedy->getEstado());
            $ServicioLog->escribeLog($ficheroLog);
            $ct++;

            $EncargoAll = $EntityManager->getRepository("AppBundle:Encargo")->findBy(["nmRemedy" => $Remedy->getNumero()]);

            if ($Remedy->getNumero() == '10-6108244') {
                dump($Remedy);
                dump($EncargoAll);
                die();
            }

            foreach ($EncargoAll as $Encargo) {
                $EncargoRemedy = $EntityManager->getRepository("AppBundle:EncargoRemedy")->findOneBy(["encargo" => $Encargo, "remedy" => $Remedy]);
                if (is_null($EncargoRemedy)) {
                    $EncargoRemedy = new EncargoRemedy();
                    $EncargoRemedy->setEncargo($Encargo);
                    $EncargoRemedy->setRemedy($Remedy);
                    $EntityManager->persist($EncargoRemedy);
                    $EntityManager->flush();
                }
            }
        }

        $CargaFichero->setNumeroRegistros($ct);
        $EntityManager->persist($CargaFichero);
        $EntityManager->flush();
        return true;
    }

    /**
     * @param Request $request
     * @return Response
     */

    public function cargaSeguimientoAction(Request $request)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $ImportarForm = $this->createForm(ImportarType::class);
        $ImportarForm->handleRequest($request);

        if ($ImportarForm->isSubmitted()) {
            /** @var UploadedFile $fichero */
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
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();

                    $FicheroLog = new FicheroLog();
                    $fechaProceso = new DateTime();
                    $FicheroLog->setFechaProceso($fechaProceso);

                    $ServicioLog = $this->get('app.escribelog');
                    $ServicioLog->setLogger('CARGA EXCEL SEGUIEMIENTO  : ID= ' . $CargaFichero->getId());
                    $ficheroLog = 'FicheroLog-' . $CargaFichero->getId() . '.log';

                    $ServicioLog->setMensaje("Comienza carga fichero: " . $file);
                    $ServicioLog->escribeLog($ficheroLog);

                    $this->actualizaSeguimiento($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog);

                    $ServicioLog->setMensaje("Finalizada carga Seguiemiento " . $file . " Encargos Actualizados Totales :" . $CargaFichero->getNumeroRegistros());
                    $ServicioLog->escribeLog($ficheroLog);
                    $FicheroLog->setNombreFichero($ServicioLog->getFileName());
                    $EntityManager->persist($FicheroLog);
                    $EntityManager->flush();

                    $CargaFichero->setFicheroLog($FicheroLog);
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();

                    $params = ["CargaFichero" => $CargaFichero];
                    return $this->render("cargaFichero/finProceso.html.twig", $params);

                } catch (Exception $e) {
                    $status = "***ERROR EN CARGA DE FICHERO **: " . $file_name;
                    $this->sesion->getFlashBag()->add("status", $status);
                    $params = [];
                    return $this->render("cargaFichero/query.html.twig", $params);
                }
            }
        }
        $params = ["form" => $ImportarForm->createView()];
        return $this->render("cargaFichero/carga.html.twig", $params);
    }

    /**
     * @param CargaFichero $CargaFichero
     * @param Spreadsheet $PHPExcel
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     * @throws Exception
     */
    public function actualizaSeguimiento($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog)
    {

        $EntityManager = $this->getDoctrine()->getManager();

        $objWorksheet = $PHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $fecha = new DateTime();
        $ct = 0;
        for ($i = 2; $i <= $highestRow; $i++) {
            /** @var EntityManager $EntityManager */
            if (!$EntityManager->isOpen()) {
                $EntityManager = $EntityManager->create($EntityManager->getConnection(), $EntityManager->getConfiguration());
            }
            $headingsArray = $objWorksheet->rangeToArray('A' . $i . ':J' . $i, null, true, true, true);
            $headingsArray = $headingsArray[$i];

            /** @var Encargo $Encargo */
            $Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($headingsArray["D"]);
            if (is_null($Encargo)) {
                $ServicioLog->setMensaje(' **** Error Encargo id=' . $headingsArray["D"] . ' NO ENCONTRADO **** ');
                $ServicioLog->escribeLog($ficheroLog);
                continue;
            }

            if ($headingsArray["J"] != "") {
                /** @var AnotacionEncargo $AnotacionEncargo */
                $AnotacionEncargo = new AnotacionEncargo();
                $AnotacionEncargo->setEncargo($Encargo);
                $AnotacionEncargo->setUsuario($this->getUser());
                $AnotacionEncargo->setFecha($fecha);
                $AnotacionEncargo->setAnotacion($headingsArray["J"]);
                $EntityManager->persist($AnotacionEncargo);
                $EntityManager->flush();
                $ServicioLog->setMensaje('-Anotación Generada, Encargo Número: ' . $Encargo->getNumero() . ' ' . $Encargo->getTitulo() . ' ' . $AnotacionEncargo->getAnotacion());
                $ServicioLog->escribeLog($ficheroLog);
                $ct++;
            }

        }

        $CargaFichero->setNumeroRegistros($ct);
        $EntityManager->persist($CargaFichero);
        $EntityManager->flush();
        return true;
    }


    /**
     * @param Request $request
     * @return Response
     */

    public function cargaAgrupacionAction(Request $request)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $ImportarForm = $this->createForm(ImportarType::class);
        $ImportarForm->handleRequest($request);

        if ($ImportarForm->isSubmitted()) {
            /** @var UploadedFile $fichero */
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
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();

                    $FicheroLog = new FicheroLog();
                    $fechaProceso = new DateTime();
                    $FicheroLog->setFechaProceso($fechaProceso);

                    $ServicioLog = $this->get('app.escribelog');
                    $ServicioLog->setLogger('CARGA EXCEL SEGUIMIENTO POR AGRUPACIÓN  : ID= ' . $CargaFichero->getId());
                    $ficheroLog = 'FicheroLog-' . $CargaFichero->getId() . '.log';

                    $ServicioLog->setMensaje("Comienza carga fichero: " . $file);
                    $ServicioLog->escribeLog($ficheroLog);
                    $this->actualizaAgrupacion($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog);
                    $ServicioLog->setMensaje("Finalizada carga Seguimiento Agrupación " . $file . " Encargos Actualizados Totales :" . $CargaFichero->getNumeroRegistros());
                    $ServicioLog->escribeLog($ficheroLog);
                    $FicheroLog->setNombreFichero($ServicioLog->getFileName());
                    $EntityManager->persist($FicheroLog);
                    $EntityManager->flush();

                    $CargaFichero->setFicheroLog($FicheroLog);
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();

                    $params = ["CargaFichero" => $CargaFichero];
                    return $this->render("cargaFichero/finProceso.html.twig", $params);

                } catch (Exception $e) {
                    $status = "***ERROR EN CARGA DE FICHERO **: " . $file_name;
                    $this->sesion->getFlashBag()->add("status", $status);
                    $params = [];
                    return $this->render("cargaFichero/query.html.twig", $params);
                }
            }
        }
        $params = ["form" => $ImportarForm->createView()];
        return $this->render("cargaFichero/carga.html.twig", $params);
    }


    /**
     * @param CargaFichero $CargaFichero
     * @param Spreadsheet $PHPExcel
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     * @throws Exception
     */
    public function actualizaAgrupacion($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog)
    {

        /** @var EntityManager $EntityManager */
        $EntityManager = $this->getDoctrine()->getManager();

        $objWorksheet = $PHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $fecha = new DateTime();
        $ct = 0;

        for ($i = 14; $i <= $highestRow; $i++) {
            if (!$EntityManager->isOpen()) {
                $EntityManager = $EntityManager->create($EntityManager->getConnection(), $EntityManager->getConfiguration());
            }
            $headingsArray = $objWorksheet->rangeToArray('A' . $i . ':H' . $i, null, true, true, true);
            $headingsArray = $headingsArray[$i];

            if ($headingsArray["B"] == "") continue;


            /** @var Encargo $Encargo */
            $Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($headingsArray["B"]);
            if (is_null($Encargo)) {
                $ServicioLog->setMensaje(' **** Error Encargo id=' . $headingsArray["D"] . ' NO ENCONTRADO **** ');
                $ServicioLog->escribeLog($ficheroLog);
                continue;
            }

            if ($headingsArray["H"] != "") {
                $AnotacionEncargo = new AnotacionEncargo();
                $AnotacionEncargo->setEncargo($Encargo);
                $AnotacionEncargo->setUsuario($this->getUser());
                $AnotacionEncargo->setFecha($fecha);
                $AnotacionEncargo->setAnotacion($headingsArray["H"]);
                $EntityManager->persist($AnotacionEncargo);
                $EntityManager->flush();
                $ServicioLog->setMensaje('Anotación Generada, Encargo Número: ' . $Encargo->getNumero() . ' ' . $Encargo->getTitulo() . ' ' . $AnotacionEncargo->getAnotacion());
                $ServicioLog->escribeLog($ficheroLog);
                $ct++;
            }

        }

        $CargaFichero->setNumeroRegistros($ct);
        $EntityManager->persist($CargaFichero);
        $EntityManager->flush();
        return true;
    }

    /**
     * @param Request $request
     * @return Response
     */

    public function cargaLineaSeguimientoAction(Request $request)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $ImportarForm = $this->createForm(ImportarType::class);
        $ImportarForm->handleRequest($request);

        if ($ImportarForm->isSubmitted()) {
            /** @var UploadedFile $fichero */
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
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();

                    $FicheroLog = new FicheroLog();
                    $fechaProceso = new DateTime();
                    $FicheroLog->setFechaProceso($fechaProceso);

                    $ServicioLog = $this->get('app.escribelog');
                    $ServicioLog->setLogger('CARGA EXCEL LINEA DE SEGUIEMIENTO  : ID= ' . $CargaFichero->getId());
                    $ficheroLog = 'FicheroLog-' . $CargaFichero->getId() . '.log';

                    $ServicioLog->setMensaje("Comienza carga fichero: " . $file);
                    $ServicioLog->escribeLog($ficheroLog);

                    $this->actualizaLineaSeguimiento($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog);

                    $ServicioLog->setMensaje("Finalizada carga Seguiemiento " . $file . " Encargos Actualizados Totales :" . $CargaFichero->getNumeroRegistros());
                    $ServicioLog->escribeLog($ficheroLog);
                    $FicheroLog->setNombreFichero($ServicioLog->getFileName());
                    $EntityManager->persist($FicheroLog);
                    $EntityManager->flush();

                    $CargaFichero->setFicheroLog($FicheroLog);
                    $EntityManager->persist($CargaFichero);
                    $EntityManager->flush();

                    $params = ["CargaFichero" => $CargaFichero];
                    return $this->render("cargaFichero/finProceso.html.twig", $params);

                } catch (Exception $e) {
                    $status = "***ERROR EN CARGA DE FICHERO **: " . $file_name;
                    $this->sesion->getFlashBag()->add("status", $status);
                    $params = [];
                    return $this->render("cargaFichero/query.html.twig", $params);
                }
            }
        }
        $params = ["form" => $ImportarForm->createView()];
        return $this->render("cargaFichero/carga.html.twig", $params);
    }

    /**
     * @param CargaFichero $CargaFichero
     * @param Spreadsheet $PHPExcel
     * @param EscribeLog $ServicioLog
     * @param string $ficheroLog
     * @return bool
     * @throws Exception
     */
    public function actualizaLineaSeguimiento($CargaFichero, $PHPExcel, $ServicioLog, $ficheroLog)
    {

        /** @var EntityManager $EntityManager */
        $EntityManager = $this->getDoctrine()->getManager();

        $objWorksheet = $PHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $fecha = new DateTime();
        $ct = 0;
        for ($i = 2; $i <= $highestRow; $i++) {
            if (!$EntityManager->isOpen()) {
                $EntityManager = $EntityManager->create($EntityManager->getConnection(), $EntityManager->getConfiguration());
            }
            $headingsArray = $objWorksheet->rangeToArray('A' . $i . ':L' . $i, null, true, true, true);
            $headingsArray = $headingsArray[$i];

            if ($headingsArray["D"] == "") continue;

            /** @var Encargo $Encargo */
            $Encargo = $EntityManager->getRepository("AppBundle:Encargo")->find($headingsArray["D"]);
            if (is_null($Encargo)) {
                $ServicioLog->setMensaje(' **** Error Encargo id=' . $headingsArray["D"] . ' NO ENCONTRADO **** ');
                $ServicioLog->escribeLog($ficheroLog);
                continue;
            }

            if ($headingsArray["L"] != "") {
                $Anotacion = new AnotacionEncargo();
                $Anotacion->setEncargo($Encargo);
                $Anotacion->setUsuario($this->getUser());
                $Anotacion->setFecha($fecha);
                $Anotacion->setAnotacion($headingsArray["L"]);
                $EntityManager->persist($Anotacion);
                $EntityManager->flush();
                $ServicioLog->setMensaje('-Anotación Generada, Encargo Número: ' . $Encargo->getNumero() . ' ' . $Encargo->getTitulo() . ' ' . $Anotacion->getAnotacion());
                $ServicioLog->escribeLog($ficheroLog);
                $ct++;
            }
        }

        $CargaFichero->setNumeroRegistros($ct);
        $EntityManager->persist($CargaFichero);
        $EntityManager->flush();
        return true;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     *
     */

    public function generarFicheroSecoAction(Request $request)
    {
        $EntityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(FicheroSecoType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $idMes = $_POST["formFicheroSeco"]["mes"];
            $tipoObjeto = $_POST["formFicheroSeco"]["tipoObjeto"];
            $Mes = $EntityManager->getRepository("AppBundle:Mes")->find($idMes);
            $fechaInicio = $Mes->getFechaInicio()->format('d') . '%2F' .
                $Mes->getFechaInicio()->format('m') . '%2F' .
                $Mes->getFechaInicio()->format('Y');
            $fechaFin = $Mes->getFechaFin()->format('d') . '%2F' .
                $Mes->getFechaFin()->format('m') . '%2F' .
                $Mes->getFechaFin()->format('Y');

            return $this->llamadaSeco($fechaInicio, $fechaFin, $tipoObjeto);
        }

        $params = ["form" => $form->createView()];
        return $this->render("cargaFichero/genera.html.twig", $params);
    }

    /**
     * @param string $fechaInico
     * @param string $fechaFin
     * @param integer $tipoObjeto
     * @return RedirectResponse
     */
    public function llamadaSeco($fechaInico, $fechaFin, $tipoObjeto)
    {
        $url = "http://intranet.madrid.org/seco/html/web/CmmaEncargoMto.icm?ESTADO_MENU=5" .
            "&orden=ASC" .
            "&ordenacion=cdCmmaEncargo" .
            "&radProveedorSel=todos" .
            "&radUsuarioICMSel=todos" .
            "&exportarExcel=S" .
            "&exportarExcelFijo=SI" .
            "&primeraVez=N" .
            "&tipoEncargo2=S" .
            "&semilla=92084" .
            "&semillaAnterior=92084" .
            "&opc_PRE=PRE" .
            "&opc_PVA=PVA" .
            "&opc_PAC=PAC" .
            "&opc_EJE=EJE" .
            "&opc_PVE=PVE" .
            "&opc_VEP=VEP" .
            "&opc_RPL=RPL" .
            "&opc_EEP=EEP" .
            "&opc_CAN=CAN" .
            "&opc_FIN=FIN" .
            "&opc_CRR=CRR" .
            "&opc_Activos=1" .
            "&cdCmmaCriticidadEncargo=0" .
            "&cdCmmaCriticidadEncargo=1" .
            "&cdCmmaCriticidadEncargo=2" .
            "&cdCmmaCriticidadEncargo=3" .
            "&contadorCriticidad=4" .
            "&radPrioridades=A" .
            "&radPrioridades=M" .
            "&radPrioridades=B" .
            "&contadorPrioridad=3" .
            "&fcIniEstadoDesde=" . $fechaInico .
            "&fcIniEstadoHasta=" . $fechaFin;
        if ($tipoObjeto == 3) {
            $url = $url .
                "&cdCmmaTObjetoEncargo1=PLA" .
                "&cdCmmaTObjetoEncargo2=NPL";
        }
        if ($tipoObjeto == 2) {
            $url = $url .
                "&cdCmmaTObjetoEncargo1=NPL";
        }
        if ($tipoObjeto == 1) {
            $url = $url .
                "&cdCmmaTObjetoEncargo1=PLA";
        }
        if ($tipoObjeto == 4) {
            $url = $url .
                "&cdCmmaTObjetoEncargo1=NPL" .
                "&cdCmmaObjetoEncargo1=ENP";

        }
        return $this->redirect($url);

    }


}