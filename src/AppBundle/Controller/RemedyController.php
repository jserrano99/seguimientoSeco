<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\RemedyDatatable;
use AppBundle\Entity\Remedy;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Form\RemedyType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;

/**
 * Class RemedyController
 *
 * @package AppBundle\Controller
 */
class RemedyController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * RemedyController constructor.
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
		$datatable = $this->get('sg_datatables.factory')->create(RemedyDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		$params = ['datatable' => $datatable];
		return $this->render('remedy/query.html.twig', $params);
	}

	/**
	 * @param int $id
	 * @return Response
	 */
	public function imprimirAction($id)
	{
		$format = "pdf";
		$parametros = ["remedyId" => $id];
		$reportUnit = "/reports/incidentesByRemedy";
		return $this->get('yoh.jasper.report')->generate($reportUnit, $parametros, $format);
	}
}
