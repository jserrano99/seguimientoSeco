<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\EncargoDatatable;
use AppBundle\Entity\AnotacionEncargo;
use AppBundle\Form\AnotacionEncargoType;
use AppBundle\Form\EncargoType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}

