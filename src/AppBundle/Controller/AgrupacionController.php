<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\AgrupacionDatatable;
use AppBundle\Entity\Agrupacion;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Form\AgrupacionType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;

/**
 * Class AgrupacionController
 *
 * @package AppBundle\Controller
 */
class AgrupacionController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * AgrupacionController constructor.
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
	public function queryAction(Request $request) {
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(AgrupacionDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('agrupacion/query.html.twig', array(
			'datatable' => $datatable,
		));
	}

	/**
	 * @param Request $request
	 * @param $seguiemiento_id
	 * @return JsonResponse|Response
	 * @throws Exception
	 */
	public function queryBySeguimientoAction(Request $request, $id) {
		$Seguimiento = $this->getDoctrine()->getManager()->getRepository("AppBundle:Seguimiento")->find($id);
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(AgrupacionDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$qb = $datatableQueryBuilder->getQb();
			$qb->andWhere('seguimiento = :seguimiento');
			$qb->setParameter('seguimiento', $Seguimiento);

			return $responseService->getResponse();
		}

		return $this->render('agrupacion/query.html.twig', array(
			'datatable' => $datatable,
		));
	}

	/**
	 * @param Request $request
	 * @param                                           $id
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, $id) {
		
		$Agrupacion = $this->getDoctrine()->getManager()->getRepository("AppBundle:Agrupacion")->find($id);
		
		$form = $this->createForm(AgrupacionType::class, $Agrupacion);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Agrupacion);
				$this->getDoctrine()->getManager()->flush();
				$status = "AGRUPACIÓN " . $Agrupacion->getCodigo(). " MODIFICADA CORRECTAMENTE";
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Agrupacion->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			}
		}

		$params = ["agrupacion" => $Agrupacion,
			"accion" => "MODIFICACIÓN",
			"form" => $form->createView()];
		return $this->render("agrupacion/edit.html.twig", $params);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request) {

		$Agrupacion = new Agrupacion();

		$form = $this->createForm(AgrupacionType::class, $Agrupacion);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Agrupacion);
				$this->getDoctrine()->getManager()->flush();
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Agrupacion->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryAgrupacion");
			}
		}

		$params = ["agrupacion" => $Agrupacion,
			"accion" => "CREACIÓN",
			"form" => $form->createView()];
		return $this->render("agrupacion/edit.html.twig", $params);
	}

}
