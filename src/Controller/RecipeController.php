<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;  
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Attribute\Route;
use DateTimeImmutable;
use App\Entity\Recipe;
use App\Form\RecipeType;

class RecipeController extends AbstractController 
{ 

    // PAGE RECETTE (HOME)
    #[Route(path: '/recette', name: 'app_recipe_index')]
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em): Response
    {

        // 1. AJOUTER DU CONTENU

        // version classique
        // $recipe = new Recipe();
        // $recipe->setTitle('Omelette');
        // $recipe->setSlug('omelette');
        // $recipe->setContent('Prenez des oeufs, cassez les et ensuite battez les en rajoutant du sel.');
        // $recipe->setDuration(6);
        // $recipe->setCreatedAt(new DateTimeImmutable());
        // $recipe->setUpdatedAt(new DateTimeImmutable());
        // $em->persist($recipe); 
        // $em->flush();

        // version avec l'utilisation de fluent setter
        // $recipe2 = new Recipe;
        // $recipe2->setTitle('Caldeirada de Peixe' )
        //     ->setSlug('caldeirada-de-peixe')
        //     ->setContent('Achetez du poisson et faites une mélange.')
        //     ->setDuration(65)
        //     ->setCreatedAt(new DateTimeImmutable())
        //     ->setUpdatedAt(new DateTimeImmutable());
        // $em->persist($recipe2); 
        // $em->flush();

        $recipes = $repository->findAll();
        // $recipes = $repository->findRecipeDurationLowerThan(60);

        // $recipes = $em ->getRepository(Recipe::class)->findAll();
        // 2eme methode pour findall sans avoir la necessite d'user RecipeRepository $repository, en haut dans la route (on la retire) PLUS PRACTIQUE

        // dd($recipes);


        //2. MODIFICATION
        // modifie la 2eme recette

    //    $recipes[1]->setTitle('Saka maigre');
    //    $recipes[1]->setSlug('saka-maigre');
    //    $recipes[1]->setContent('pour maigrir la solution on ne mange pas du gras');
    //    $em->flush();
        // en lieu de faire comme ca on pourrait utiliser fluent setter


        
        


        //3. SUPRESSION
        // $em->remove($recipes[5]); 
        // // il y a un erreur; il faut donc mettre en commenatire apres; ca fonctionne!!! pourtant si on le mets un commentaire a chaque fois que je refrais la page, il suprimmera la suivante, jusqu'à la fin et la donnera un erreur ... pourtant julien n'est pas sur et peut importe cvar on travaillera pas de cette facon
        // $em->flush(); 


        // FINDS AND SHOWS ALL DATA FROM TABLE RECIPES
        return $this->render('recipe/index.html.twig', [
            'recipesTab' => $recipes
        ]); 
    }


    // PAGE RECETTE (EDIT)----------------------------------------------------------------

    // #[Route(path : '/recette/{id}/edit', name: 'app_recipe_edit')]
    // public function edit(Recipe $recipe) {
    // //   dd($recipe);
    // } 

    #[Route(path : '/recette/{id}/edit', name : 'app_recipe_edit')]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em) : Response{
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        // avec dd on voit q ca marche ; maintenant il faut envoyer vers la bd
        // dd($recipe);
        // on deactive dd; on edit la page; on active dd; on click envoyer; ca change dans le dd mais pas encore dans la bd
        if ($form->isSubmitted() && $form->isValid()){
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->flush();
            $this->addFlash('success', 'Changes were made and stored');
            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]);
        }
        return $this->render('recipe/edit.html.twig',[
            'recipe' => $recipe,
            'monForm' => $form
        ]);
    }


    // PAGE RECETTE (CREATE)-------------------------------------------------------------

    #[Route(path : '/recette/create', name : 'app_recipe_create')]
    public function create(  Request $request, EntityManagerInterface $em) : Response{
        $recipe=new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $recipe->setCreatedAt(new DateTimeImmutable());
            $recipe->setUpdatedAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette' . $recipe->getTitle() . 'a été crée');
            return $this->redirectToRoute('app_recipe_index');
        }
        return $this->render('recipe/create.html.twig',[
            // une methode, une page
           'form_create' => $form
        ]);
    }



     // PAGE RECETTE (DELETE)-------------------------------------------------------------

     #[Route(path : '/recette/{id}/delete', name : 'app_recipe_delete')]
     public function delete(Recipe $recipe, EntityManagerInterface $em) : Response{
        $titre=$recipe ->getTitle();
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('info', 'La recette' . $titre . 'a été suprimée');
        return $this->redirectToRoute('app_recipe_index');
        }



    // PAGE RECETTE (TEMPLATE SHOW)-------------------------------------------------------

    #[Route(path: '/recette/{slug}-{id}', name: 'app_recipe_show', requirements : ['id'=> '\d+', 'slug'=> '[a-z0-9-]+'])]
   
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository ) : Response
    {
        $recipe = $repository->find($id);
        // $slug="security feauture";
        
        if($recipe->getSlug() !== $slug){
            return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId(), 'slug' => $recipe->getSlug()]);
        }
        // tenho q utiisar um slug-id; n posso usar maisculas e o id tem de estar na bd 
        

        return $this->render('recipe/show.html.twig', [
                // 'id' => $id,
                // 'slug' => $slug,
                'user' => [
                    "firstname" => 'Vito',
                    "lastname" => 'Cook'
                ],
                'recipeSolo' => $recipe
        ]);


    // dd($request);
    // dd($request->attributes->get('slug'), $request->attributes->getInt('id'));
    // dd($slug, $id);
   
    //  affiche normal
    // return new Response("<h1>Recette numéro ". $id. " : ".$slug  ."<h1>") ;

    //version sous forme de json en important jsonresponse       
        // return new JsonResponse([
        //     'id' => $id,
        //     'slug' => $slug
        // ]);

    //version sous forme de json        
    //     return $this->json([
    //     'id' => $id,
    //     'slug' => $slug
    // ]);    
    // {
    //     //  return new Response("<h1>Bienvenue dans la page des recettes</h1>") ;
    //      return $this->render('recipe/show.html.twig');
    //     }
    }
}
