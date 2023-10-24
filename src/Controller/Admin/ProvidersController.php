<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Provider\ProviderSearchForm;
use App\Entity\Provider;
use App\Form\ProviderType;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

final class ProvidersController extends AbstractController
{
    public function __construct(
        private readonly ProviderRepository     $providers,
        private readonly ProviderSearchForm     $searchForm,
        private readonly EntityManagerInterface $em)
    {
    }

    #[Route('/admin/providers', name: 'admin.providers')]
    public function main(Request $request): Response
    {
        return $this->render('admin/providers/main.html.twig', [
            'pagination' => $this->searchForm->search($request)
        ]);
    }

    #[Route('/admin/providers/create', name: 'admin.providers.create')]
    public function create(Request $request): Response
    {
        $provider = new Provider();
        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $provider->setToken(UuidV4::v4()->toBase58());
            $this->em->persist($provider);
            $this->em->flush();

            $this->addFlash('success', 'Сохранено');

            return $this->redirectToRoute('admin.providers');
        }

        return $this->render('admin/providers/form.html.twig', [
            'title' => 'Добавление компании',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/providers/edit?id={id}', name: 'admin.providers.edit')]
    public function edit(int $id, Request $request): Response
    {
        $provider = $this->providers->findOneById($id) ?? throw $this->createNotFoundException();

        $form = $this->createForm(ProviderType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($provider);
            $this->em->flush();

            $this->addFlash('success', 'Сохранено');

            return $this->redirectToRoute('admin.providers.edit', ['id' => $id]);
        }

        return $this->render('admin/providers/form.html.twig', [
            'title' => $provider->getName(),
            'form' => $form->createView(),
        ]);
    }
}