<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(UserPasswordEncoderInterface $passwordEncoder): Response
    {
//        dd($passwordEncoder->encodePassword(new User(), 'ctQu7wYu'));
//        $resultTemplate = 'Hello {{ name }}';
//        $template = $this->get('twig')->createTemplate($resultTemplate);
//        $calculatorResult = ['name' => 'Малышок'];
//        return new Response($template->render($calculatorResult));
        return $this->render('main/index.html.twig', []);
    }
}
