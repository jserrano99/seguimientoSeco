<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
		$datatable = $this->get('sg_datatables.factory')->create(\AppBundle\Datatables\EncargoDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('encargo/query.html.twig', array(
			'datatable' => $datatable,
			'agrupacion' => null
		));
	}

	public function queryByAgrupacionAction(Request $request, $idAgrupacion)
	{
		$Agrupacion = $this->getDoctrine()->getManager()->getRepository("AppBundle:Agrupacion")->find($idAgrupacion);

		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(\AppBundle\Datatables\EncargoDatatable::class);
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

		return $this->render('encargo/query.html.twig', array(
			'datatable' => $datatable,
			'agrupacion' => $Agrupacion
		));
	}


	/**
	 * @param $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */

	public function  editAction($id){
		$Encargo = $this->getDoctrine()->getManager()->getRepository("AppBundle:Encargo")->find($id);
		$url = 'http://intranet.madrid.org/seco/html/web/CmmaEncargoConsulta.icm?cdCmmaEncargo='.$Encargo->getNumero();
		return $this->redirect($url);

	}
}
