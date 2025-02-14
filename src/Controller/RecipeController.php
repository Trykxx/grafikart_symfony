<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recette', name: 'recipe.index')]
    public function index(Request $request,RecipeRepository $repository): Response
    {
        $recipes = $repository->findWithDurationLowerThan(20);
        // dd($repository->findTotalDuration());

        return $this->render('recipe/index.html.twig',['recipes'=>$recipes]);
    }

    #[Route('/recette/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);

        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show',['slug'=> $recipe->getSlug(),'id'=> $recipe->getId()]);
        }

        return $this->render('recipe/show.html.twig',[
            'recipe'=> $recipe,
        ]);
    }

    #[Route('/recettes/{id}/edit', name: 'recipe.edit')]
    public function edit(Recipe $recipe)
    {
        $form=$this->createForm(RecipeType::class, $recipe);
        return $this->render('recipe/edit.html.twig',['recipe'=>$recipe,'form'=>$form]);
    }
}
