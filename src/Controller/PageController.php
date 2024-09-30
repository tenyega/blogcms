<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_page')]
    public function index(PostRepository $pr, RouterInterface $router): Response
    {
        $posts = $pr->findAll();
        $router = $router->getRouteCollection();
        return $this->render('page/index.html.twig', [
            'router' => $router
        ]);
    }
    #[Route('/p/{slug}', name: 'app_post_show')]
    public function show(PostRepository $pr, string $slug): Response
    {
        $post = $pr->findOneBy(['slug' => $slug]);

        return $this->render('page/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/new', name: 'app_post_new')]
    public function new(EntityManagerInterface $em, Request $request): Response
    {
        $category = new Category();
        $post = new Post();
        $em->persist($category);
        $post->setAuthor($this->getUser())
            ->setCategory($category);
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($post);
            $em->flush();
        }
        return $this->render('page/new.html.twig', [
            'postForm' => $form,
        ]);
    }
}
