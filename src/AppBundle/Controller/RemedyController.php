<?php

namespace AppBundle\Controller;

use AppBundle\Datatables\CentroDatatable;
use AppBundle\Datatables\RemedyDatatable;
use AppBundle\Datatables\UsuarioRemedyDatatable;
use AppBundle\Entity\Remedy;
use AppBundle\Form\CentroType;
use AppBundle\Form\UsuarioRemedyType;
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

	/**
	 * @param Request $request
	 * @return JsonResponse|Response
	 * @throws Exception
	 */
	public function queryCentrosAction(Request $request)
	{
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(CentroDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		$params = ['datatable' => $datatable];
		return $this->render('usuariosRemedy/query.html.twig', $params);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse|Response
	 * @throws Exception
	 */
	public function queryUsuarioRemedyAction(Request $request)
	{
		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(UsuarioRemedyDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$datatableQueryBuilder->buildQuery();
			return $responseService->getResponse();
		}

		$params = ['datatable' => $datatable];
		return $this->render('usuariosRemedy/queryUsuarios.html.twig', $params);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return JsonResponse|Response
	 * @throws Exception
	 */

	public function queryUsuarioRemedyByCentroAction(Request $request, $id)
	{
		$Centro = $this->getDoctrine()->getManager()->getRepository("AppBundle:Centro")->find($id);

		$isAjax = $request->isXmlHttpRequest();
		$datatable = $this->get('sg_datatables.factory')->create(UsuarioRemedyDatatable::class);
		$datatable->buildDatatable();

		if ($isAjax) {
			$responseService = $this->get('sg_datatables.response');
			$responseService->setDatatable($datatable);
			$datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
			$qb = $datatableQueryBuilder->getQb();
			$qb->andWhere('centro = :centro');
			$qb->setParameter('centro', $Centro);

			return $responseService->getResponse();
		}

		$params = ['datatable' => $datatable];
		return $this->render('usuariosRemedy/queryUsuarios.html.twig', $params);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return RedirectResponse|Response
	 */
	public function editUsuarioRemedyAction(Request $request, $id)
	{
		$EntityManager = $this->getDoctrine()->getManager();

		$UsuarioRemedy = $EntityManager->getRepository("AppBundle:UsuarioRemedy")->find($id);

		$form = $this->createForm(UsuarioRemedyType::class, $UsuarioRemedy);
		$form->handleRequest($request);

		$Incidencias = $EntityManager->getRepository("AppBundle:Remedy")->findBy(["usuarioRemedy" => $UsuarioRemedy]);

		if ($form->isSubmitted()) {
			$this->getDoctrine()->getManager()->persist($UsuarioRemedy);
			$this->getDoctrine()->getManager()->flush();
			$status = 'USUARIO REMEDY ' . $UsuarioRemedy->getApellidos() . ', ' . $UsuarioRemedy->getNombre() . ' MODIFICADO CORRECTAMENTE ';
			$this->sesion->getFlashBag()->add("status", $status);
			return $this->redirectToRoute("queryUsuarioRemedy");
		}

		$params = ["encargo" => $UsuarioRemedy,
			"accion" => "MODIFICACIÓN",
			"incidencias" => $Incidencias,
			"usuarioRemedy"=> $UsuarioRemedy,
			"form" => $form->createView()];
		return $this->render("usuariosRemedy/edit.html.twig", $params);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return RedirectResponse|Response
	 */
	public function deleteUsuarioRemedyAction(Request $request, $id)
	{
		$EntityManager = $this->getDoctrine()->getManager();
		$UsuarioRemedy = $EntityManager->getRepository("AppBundle:UsuarioRemedy")->find($id);
		$status = 'USUARIO REMEDY ' . $UsuarioRemedy->getApellidos() . ', ' . $UsuarioRemedy->getNombre() . ' ELIMINADO CORRECTAMENTE ';
		$this->getDoctrine()->getManager()->remove($UsuarioRemedy);
		$this->getDoctrine()->getManager()->flush();
		$this->sesion->getFlashBag()->add("status", $status);
		return $this->redirectToRoute("queryUsuarioRemedy");
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return RedirectResponse|Response
	 */
	public function editCentroAction(Request $request, $id)
	{
		$EntityManager = $this->getDoctrine()->getManager();

		$Centro = $EntityManager->getRepository("AppBundle:Centro")->find($id);

		$form = $this->createForm(CentroType::class, $Centro);
		$form->handleRequest($request);

		$UsuariosRemedy = $EntityManager->getRepository("AppBundle:UsuarioRemedy")->findBy(["centro" => $Centro]);

		if ($form->isSubmitted()) {
			$this->getDoctrine()->getManager()->persist($Centro);
			$this->getDoctrine()->getManager()->flush();
			$status = 'CENTRO REMEDY ' . $Centro->getDescripcion(). ' MODIFICADO CORRECTAMENTE ';
			$this->sesion->getFlashBag()->add("status", $status);
			return $this->redirectToRoute("queryCentros");
		}

		$params = ["centro" => $Centro,
			"accion" => "MODIFICACIÓN",
			"usuariosRemedy"=> $UsuariosRemedy,
			"form" => $form->createView()];
		return $this->render("usuariosRemedy/editCentro.html.twig", $params);
	}

}


