<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AdminController extends AbstractController
{
    /**
     * @Route("/admin-register", name="api_admin-register", methods={"POST"})
     */
    public function register(EntityManagerInterface $om, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $admin = new Admin();
        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $passwordConfirmation = $request->request->get("password_confirmation");


        $errors = [];
        if ($password != $passwordConfirmation) {
            $errors[] = "Password does not match the password confirmation.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Password should be at least 6 characters.";
        }
        if (!$errors) {
            $encodedPassword = $passwordEncoder->encodePassword($admin, $password);
            $admin->setEmail($email);
            $admin->setPassword($encodedPassword);
            try {
                $om->persist($admin);
                $om->flush();

                return $this->json([
                    'admin' => $admin
                ]);
            } catch (UniqueConstraintViolationException $e) {
                $errors[] = "The email provided already has an account!";
            } catch (\Exception $e) {
                $errors[] = "Unable to save new user at this time.";
            }
        }

        return $this->json([
            'errors' => $errors
        ], 400);
    }

    /**
     * @Route("/admin-login", name="api_admin-login", methods={"POST"})
     */
    public function login()
    {
        return $this->json(['result' => true]);
    }

    /**
     * @Route("/admin-profile", name="api_admin-profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile()
    {
        return $this->json([
            'admin' => $this->getUser()
        ]);
    }

    /**
     * @Route("/admin-edit/{id}", name="api_admin_edit", methods={"POST"})
     */
    public function editAdminAction($id, EntityManagerInterface $om, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $admin = $entityManager->getRepository(Admin::class)->find($id);

        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $passwordConfirmation = $request->request->get("password_confirmation");

        $errors = [];
        if ($password != $passwordConfirmation) {
            $errors[] = "Password does not match the password confirmation.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Password should be at least 6 characters.";
        }
        if (!$errors) {
            $encodedPassword = $passwordEncoder->encodePassword($admin, $password);
            $admin->setEmail($email);
            $admin->setPassword($encodedPassword);
            try {
                $om->persist($admin);
                $om->flush();

                return $this->json([
                    'admin' => $admin
                ]);
            } catch (UniqueConstraintViolationException $e) {
                $errors[] = "The email provided already has an account!";
            } catch (\Exception $e) {
                $errors[] = "Unable to save new user at this time.";
            }
        }

        return $this->json([
            'errors' => $errors
        ], 400);
    }

    /**
     * @Route("/adminuser-edit/{id}", name="api_adminuser_edit", methods={"POST"} )
     */
    public function editAdminUserAction(int $id, EntityManagerInterface $om, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $passwordConfirmation = $request->request->get("password_confirmation");


        $errors = [];
        if ($password != $passwordConfirmation) {
            $errors[] = "Password does not match the password confirmation.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Password should be at least 6 characters.";
        }
        if (!$errors) {
            $encodedPassword = $passwordEncoder->encodePassword($user, $password);
            $user->setEmail($email);
            $user->setPassword($encodedPassword);
            try {
                $om->persist($user);
                $om->flush();

                return $this->json([
                    'user' => $user
                ]);
            } catch (UniqueConstraintViolationException $e) {
                $errors[] = "The email provided already has an account!";
            } catch (\Exception $e) {
                $errors[] = "Unable to save new user at this time.";
            }
        }


        return $this->json([
            'errors' => $errors
        ], 400);

    }

    /**
     * @Route("/", name="api_home")
     */
    public function home()
    {
        return $this->json(['result' => true]);
    }


}
