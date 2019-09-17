<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\SeguimientoDatatable;
use AppBundle\Entity\Seguimiento;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Form\SeguimientoType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;

/**
 * Class SeguimientoController
 *
 * @package AppBundle\Controller
 */
class SeguimientoController extends Controller
{
	/**
	 * @var Session
	 */
	private $sesion;

	/**
	 * SeguimientoController constructor.
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
		$datatable = $this->get('sg_datatables.factory')->create(SeguimientoDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('seguimiento/query.html.twig', [
			'datatable' => $datatable,
		]);
	}

	/**
	 * @param Request $request
	 * @param int $id
	 * @return RedirectResponse|Response
	 */
	public function editAction(Request $request, $id)
	{

		$Seguimiento = $this->getDoctrine()->getManager()->getRepository("AppBundle:Seguimiento")->find($id);

		$form = $this->createForm(SeguimientoType::class, $Seguimiento);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Seguimiento);
				$this->getDoctrine()->getManager()->flush();
				$status = "AGRUPACIÓN " . $Seguimiento->getCodigo() . " MODIFICADA CORRECTAMENTE";
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Seguimiento->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			}
		}

		$params = ["seguimiento" => $Seguimiento,
			"accion" => "MODIFICACIÓN",
			"form" => $form->createView()];
		return $this->render("seguimiento/edit.html.twig", $params);

	}

	/**
	 * @param Request $request
	 * @return RedirectResponse|Response
	 */
	public function addAction(Request $request)
	{

		$Seguimiento = new Seguimiento();

		$form = $this->createForm(SeguimientoType::class, $Seguimiento);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Seguimiento);
				$this->getDoctrine()->getManager()->flush();
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Seguimiento->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("querySeguimiento");
			}
		}

		$params = ["seguimiento" => $Seguimiento,
			"accion" => "CREACIÓN",
			"form" => $form->createView()];
		return $this->render("seguimiento/edit.html.twig", $params);
	}

}
