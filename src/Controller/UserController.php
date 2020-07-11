<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        try {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $user_form = $request->request->get('user');
            if($user_form) {
                $user_form['password'] = $encoder->encodePassword($user, $user_form['password']);
            }
            $request->request->set('user', $user_form);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('user_index');
            }

            return $this->render('user/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'error' => null
            ]);
            return $this->redirect($pathinfo, $ret['_route'] ?? null, $this->context->getScheme()) + $ret;
        } catch (\Exception $e) {
            $error = str_contains($e->getMessage(), 'SQLSTATE[23000]') ? 'Email already exists.' : $e->getMessage();
            return $this->render('user/new.html.twig', ['form' => $form->createView(), 'error' => $error]);
        }
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @param UserInterface $userLogged
     * @return Response
     */
    public function show(User $user, UserInterface $userLogged): Response
    {
        if($userLogged->getId() !== $user->getId()) return $this->redirectToRoute('user_index');

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param UserInterface $userLogged
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function edit(Request $request, User $user, UserInterface $userLogged, UserPasswordEncoderInterface $encoder): Response
    {
        if($userLogged->getId() !== $user->getId()) return $this->redirectToRoute('user_index');

        try{
            $form = $this->createForm(UserType::class, $user);

            $user_form = $request->request->get('user');

            if($user_form) $user_form['password'] = $encoder->encodePassword($user, $user_form['password']);

            $request->request->set('user', $user_form);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('user_index');
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'error' => null
            ]);
        } catch (\Exception $e) {
            $error = str_contains($e->getMessage(), 'SQLSTATE[23000]') ? 'Email already exists.' : $e->getMessage();
            return $this->render('user/new.html.twig', ['form' => $form->createView(), 'error' => $error]);
        }
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @param UserInterface $userLogged
     * @return Response
     */
    public function delete(Request $request, User $user, UserInterface $userLogged): Response
    {
        if($userLogged->getId() !== $user->getId()) return $this->redirectToRoute('user_index');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
