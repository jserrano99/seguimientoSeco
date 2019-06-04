<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Proyecto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Form\ProyectoType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\DBALException;

/**
 * Class ProyectoController
 *
 * @package AppBundle\Controller
 */
class ProyectoController extends Controller
{
	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $sesion;

	/**
	 * ProyectoController constructor.
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
	public function queryAction(Request $request) {
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(\AppBundle\Datatables\ProyectoDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		return $this->render('proyecto/query.html.twig', array(
			'datatable' => $datatable,
		));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param                                           $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, $id) {
		
		$Proyecto = $this->getDoctrine()->getManager()->getRepository("AppBundle:Proyecto")->find($id);
		
		$form = $this->createForm(ProyectoType::class, $Proyecto);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Proyecto);
				$this->getDoctrine()->getManager()->flush();
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Proyecto->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryProyecto");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryProyecto");
			}
		}

		$params = ["proyecto" => $Proyecto,
			"accion" => "MODIFICACIÓN",
			"form" => $form->createView()];
		return $this->render("proyecto/edit.html.twig", $params);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function addAction(Request $request) {

		$Proyecto = new Proyecto();

		$form = $this->createForm(ProyectoType::class, $Proyecto);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			try {
				$this->getDoctrine()->getManager()->persist($Proyecto);
				$this->getDoctrine()->getManager()->flush();
			} catch (UniqueConstraintViolationException $ex) {
				$status = " YA EXISTE UNA AGRUPACIÓN CON ESTE CÓDIGO: " . $Proyecto->getCodigo();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryProyecto");
			} catch (DBALException $ex) {
				$status = "ERROR GENERAL=" . $ex->getMessage();
				$this->sesion->getFlashBag()->add("status", $status);
				return $this->redirectToRoute("queryProyecto");
			}
		}

		$params = ["proyecto" => $Proyecto,
			"accion" => "CREACIÓN",
			"form" => $form->createView()];
		return $this->render("proyecto/edit.html.twig", $params);
	}

}
