<?php

namespace App\Controller;

use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Commentaires;
use App\Form\AjoutArticleFormType;
use App\Form\CommentaireFormType;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ArticlesController
 * @package App/Controller
 * @Route("/", name="accueil")
 */

class ArticlesController extends AbstractController
{
    #[Route('/', name: 'accueil')] 
    //pas sur de ce à quoi sert cette ligne ? j'ai l'impression que c'est une syntaxe plus récente pour les routes que celles qu'il utilise.

    //pour récupérer simplement  tous les articles sans la pagination 
    public function index()
    {
        $articles = $this->getDoctrine()->getRepository(Articles::class)->findBy([],['created_at' => 'desc']);

        return $this->render('articles/index.html.twig',[
            'articles' => $articles,
        ]);
    }

    // //bloc de code utile pour paginator, pour la pagination de l'écran d'accueil. A refaire plus tard.
    // //on utilise $request et $paginator pour faire passer le numéro de page dans l'url 
    // // penser à ajouter les use en haut parce qu'on utilise les classes PaginatorInterface et Request
    // {
    //     // on récupère la liste des tous les articles
    //     $donnees = $this->getDoctrine()->getRepository(Articles::class)->findBy([],['created_at' => 'desc']); 
    //     /*si j'essaie d'expliquer, on utilise la méthode findBy() présente dans le fichier 
    //     ArticlesRepository sur l'entité (=classe) Articles par l'intermédiaire de doctrine depuis ce controller (this)
    //     on récupère tous les articles et on les trie de manière décroissant par date de création */

    //     $nb_articles_page = 5; //variable pour fixer un nombre d'article affiché dans chaque page

    //     $articles = $paginator->paginate(
    //         $donnees, //on passe les donnees (les articles)
    //         $request->query->getInt('page',1), //numéro de la page
    //         /*dans la requete, on cherche le numéro de page 
    //         (1 par défaut)
    //         Donc, si dans l'uri il y a un numéro de page on le récupère, sinon, on considère que c'est la première page*/
    //         $nb_articles_page //nombre d'élément par page

    //     );
    // }
    

    /**
     * @IsGranted("ROLE_USER")
     * @route("/article/nouveau",name="ajout_article") 
     */
    public function ajoutArticle(Request $request){
        $article = new Articles();

        $form = $this->createForm(AjoutArticleFormType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $article->setUsers($this->getUser());
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($article);
            $doctrine->flush();

            $this->addFlash('message', 'Votre article a bien été publié');

            return $this->redirectToRoute('accueil');
        }


        return $this->render('articles/ajout.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }




    /**
     * @route("/article/{slug}",name="article") 
     * //cette route marche pas quand je l'utilise dans un path() dans une vue twig ?
     * //peut être qu'il faut mettre à jour le fichier routes.yaml comme j'ai vu ailleur ?
     */
    public function article($slug, Request $request) {
        $article = $this->getDoctrine()->getRepository(Articles::class)->findOneBy([
            'slug' => $slug
        ]);

        if(!$article){ //rappel ! pour faire un not en php
            throw $this->createNotFoundException("Larticle recherché n'existe pas");
        }

        //on instancie l'entité Commentaires
        $commentaire = new Commentaires();

        //On créé l'objet formulaire
        $form = $this->createForm(CommentaireFormType::class, $commentaire);

        //on récupère les données saisies dans le formulaire
        $form->handleRequest($request);

        // On vérifie si le formulaire a été envoyé et la validité des données
        if($form->isSubmitted() && $form->isValid()){
            //on traite les données quand elles sont saisies et valides
            $commentaire->setArticles($article);

            $commentaire->setCreatedAt(new \DateTime('now'));

            //On instancie Doctrine
            $doctrine = $this->getDoctrine()->getManager();

            //on "hydrate" $commentaire (on met à jour les champs)
            $doctrine->persist($commentaire);

            //On écrit dans la base de données
            $doctrine->flush();
        }


        return $this->render('articles/article.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);

    }

}
