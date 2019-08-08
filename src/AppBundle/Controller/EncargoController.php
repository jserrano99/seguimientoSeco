<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\EncargoDatatable;
use AppBundle\Form\EncargoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class EncargoController
 *
 * @package AppBundle\Controller
 */
class EncargoController extends Controller
{
	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
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
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
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
		$Encargo = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo")->find($id);

		$form = $this->createForm(EncargoType::class, $Encargo);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$this->getDoctrine()->getManager()->persist($Encargo);
			$this->getDoctrine()->getManager()->flush();
			$status = 'ENCARGO '. $Encargo->getNumero().' MODIFICADO CORRECTAMENTE ';
			$this->sesion->getFlashBag()->add("status", $status);
			return $this->redirectToRoute("queryEncargo");
		}

		$params = ["encargo" => $Encargo,
			"accion" => "MODIFICACIÓN",
			"form" => $form->createView()];
		return $this->render("encargo/edit.html.twig", $params);
	}

}

