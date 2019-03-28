<?php
/**
 * Created by PhpStorm.
 * User: chrys
 * Date: 28/03/19
 * Time: 07:18
 */

namespace App\Bundle\Admin\Controller;

use App\Shared\Entity\SkDiscipline;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Shared\Form\SkDisciplineFormType;

class SkDisciplineController extends Controller
{
    /**
     * @return \App\Shared\Repository\SkEntityManager|object
     */
    public function getEntityService()
    {
        return $this->get('sk.repository.entity');
    }

    public function getUserConnected()
    {
        return $this->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function indexAction()
    {
        $discipline_list =  $this->getEntityService()->getAllList(SkDiscipline::class);
        return $this->render('@Admin/SkDiscipline/index.html.twig', array('discipline_list'=>$discipline_list));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $_user_connected = $this->getUserConnected();

        $_discipline = new SkDiscipline();
        $_form = $this->createForm(SkDisciplineFormType::class, $_discipline);
        $_form->handleRequest($request);

        if ($_form->isSubmitted() && $_form->isValid()) {

            $_name = $request->request->get("sk_discipline_form")["name"];
            $_description = $request->request->get("sk_discipline_form")["description"];

            $_discipline->setName($_name)
                ->setDescription($_description)
                ->setEtsNom($_user_connected->getEtsNom())
                //->setEtsAdresse($_user_connected->getEtsAddresse())
                //->setEtsResponsable($_user_connected->getEtsResponsable())
                //->setEtsPhone($_user_connected->getEtsPhone())
                //->setEtsEmail($_user_connected->getEtsEmail())
                //->setEtsLogo($_user_connected->getEtsLogo())
                ;

            // Afaka decommentena rehefa tsy null intsony ireo

            try {
                $this->getEntityService()->saveEntity($_discipline, 'new');
                $this->getEntityService()->setFlash('success', 'Ajout discipline avec success');
            } catch (\Exception $exception) {
                $exception->getMessage();
            }

            return $this->redirectToRoute('discipline_index');
        }
        return $this->render('@Admin/SkDiscipline/add.html.twig', array('form'=>$_form->createView()));

    }

    /**
     * @param Request $request
     * @param SkDiscipline $_discipline
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request, SkDiscipline $_discipline)
    {
        $_form = $this->createForm(SkDisciplineFormType::class, $_discipline);
        $_form->handleRequest($request);

        if ($_form->isSubmitted() && $_form->isValid()) {

            $_name = $request->request->get("sk_discipline_form")["name"];
            $_description = $request->request->get("sk_discipline_form")["description"];

            $_discipline->setName($_name)
                ->setDescription($_description);

            try {
                $this->getEntityService()->saveEntity($_discipline, 'new');
                $this->getEntityService()->setFlash('success', 'Discipline modifié avec succes');
            } catch (\Exception $exception) {
                $exception->getMessage();
            }

            return $this->redirectToRoute('discipline_index');
        }
        return $this->render('@Admin/SkDiscipline/edit.html.twig', array('form'=>$_form->createView()));

    }

    /**
     * @param SkDiscipline $_discipline
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function deleteAction(SkDiscipline $_discipline)
    {
        $_discipline_delete = $this->getEntityService()->deleteEntity($_discipline, '');
        if ($_discipline_delete === true) {
            try {
                $this->getEntityService()->setFlash('success', 'suppression discipline réussie');
            } catch (\Exception $exception) {
                $this->getEntityService()->setFlash('error', 'un erreur se produite');
                $exception->getMessage();
            }
        }

        return $this->redirectToRoute('discipline_index');
    }
}