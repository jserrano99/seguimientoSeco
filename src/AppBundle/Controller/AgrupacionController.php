<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Agrupacion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
	 * @var \Symfony\Component\HttpFoundation\Session\Session
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
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
	 * @throws \Exception
	 */
	public function queryAction(Request $request) {
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(\AppBundle\Datatables\AgrupacionDatatable::class);
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
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param                                           $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
