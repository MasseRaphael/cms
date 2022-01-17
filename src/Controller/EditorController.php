<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

class EditorController extends AbstractController
{
    /**
     * @Route("/editor/{entity_name}", name="editor")
     */
    public function index($entity_name, EntityManagerInterface $entityManager): Response
    {
        $rows = $entityManager->getRepository('App\Entity\\' . $entity_name)->findAll();
        return $this->render('editor/index.html.twig', [
            'liste' => $rows,
            'entityName' => $entity_name,
        ]);
    }

    /**
     * @Route("/editor/{entity_name}/form/{id}", name="editor_form")
     */
    public function form($entity_name, $id=null, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher)
    {
        if($id){
            $row = $entityManager->getRepository('App\Entity\\' . $entity_name)->find($id);
        }else{
            $full_name = 'App\Entity\\' . $entity_name;
            $row = new $full_name;
        }
        $formParam = $this->getParameter('form');
         $form = $this->createForm(FormType::class, $row);
        foreach($formParam[$entity_name]['fields'] as $fieldname => $field){
            $form->add($field['name'], $field['namespace']. $field['type'], $field['option']);
        }
        $form->add('submit', SubmitType::class);
        $form->handleRequest($request);
        $new_row = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            foreach($formParam[$entity_name]['fields'] as $fieldname => $field){
                if ($field['type'] == 'RepeatedType' ){
                    $hashedPassword = $userPasswordHasher->hashPassword(
                        $row,
                        $form->get('Password')->getData()
                    );
                    $row->setPassword($hashedPassword);
                }
            }
            $entityManager->persist($new_row);
            $entityManager->flush();
            return $this->redirectToRoute('editor', ['entity_name' => $entity_name], Response::HTTP_SEE_OTHER);
        }

        return $this->render('editor/form.html.twig', [
            'form' => $form->createView(),
            'entityName' => $entity_name,
        ]);
    }
}
