<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController 
{
    #[Route('/recette', name: 'app_recipe_index')]
    public function index(Request $request): Response

    {
    //  return new Response("<h1>Bienvenue dans la page des recettes</h1>") ;
     return $this->render('recipe/index.html.twig');
    }

    // #[Route('/recette/{slug}-{id}', name: 'app_recipe_show')]
    #[Route(path: '/recette/{slug}-{id}', name: 'app_recipe_show', requirements : ['id'=> '\d+', 'slug'=> '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id ) : Response
    {

        // $slug="security feauture";
        return $this->render('recipe/show.html.twig', [
                'id' => $id,
                'slug' => $slug,
                'user' => [
                    "firstname" => 'Vito',
                    "lastname" => 'Tart'
                ]  
        ]);


    //  dd($request);
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

