<?php

namespace App\Controller;

use App\Entity\GnPost;
use App\Repository\GnPostCategoryRepository;
use App\Service\CookieIA;
use App\Service\DataPost;
use App\Service\Translation;
use App\Service\UserDeviceInfo;
use Statickidz\GoogleTranslate;
use App\Repository\GnPostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IAController extends AbstractController
{
    private HttpClientInterface $httpClientInterface;
    public function __construct(HttpClientInterface $httpClientInterface)
    {
        $this->httpClientInterface = $httpClientInterface;
    }

     /**
     * @Route("/ia", name="app_ia")
     */
    public function index(
        UserDeviceInfo $userDeviceInfo,
        CookieIA $cookieIA,
        DataPost $dataPost,
        GnPostRepository $gnPostRepository,
        GnPostCategoryRepository $gnPostCategoryRepository,
        PaginatorInterface $paginatorInterface,
        Request $request
    ): Response {
        // bin2hex("da-ia");
        // GET USER DEVICE INFO
        // $clientIP = $userDeviceInfo->getIp();
        $clientIP = "102.42.48.56";
        // $clientCountry = $userDeviceInfo->getCountryCode();
        $clientCountry = "MG";
        if ($this->getUser()) {
            // IF USER CONNECTED
            
        } else {
            if ($cookieIA->check("da-ia")) {
                if (!is_array(json_decode($cookieIA->get("da-ia")))) {
                    $cookieIA->remove("da-ia");
                    return $this->redirectToRoute("app_ia");
                }
                $viewedPost = array_reverse($dataPost->getDataPosts());
                $foundPosts = [];
                $categoriesViewdPosts = [];
                for ($i=0; $i < count($viewedPost); $i++) {
                    if($gnPostRepository->find($viewedPost[$i])) {
                        $categoriesViewdPosts = array_merge($categoriesViewdPosts, $gnPostRepository->find($viewedPost[$i])->getCategories()->getValues());
                    }
                }
                $categoriesViewdPosts =array_unique($categoriesViewdPosts);
                foreach ($categoriesViewdPosts as $categoryGnPost) {
                    $foundPosts = array_merge($foundPosts, $gnPostRepository->get($categoryGnPost));
                }
                shuffle($foundPosts);
                $posts = $paginatorInterface->paginate(
                    $foundPosts,
                    $request->query->get("page", 1),
                    10
                );
            } else {
                $posts = $paginatorInterface->paginate(
                    $gnPostRepository->getLatest(),
                    $request->query->get("page", 1),
                    10
                );
            }
        }
        $recentPosts = $gnPostRepository->getRecentPosts(20);
        return $this->render('ia/index.html.twig', compact('posts', 'recentPosts'));
    }

    /**
     * @Route("/ia/read/{id}", name="app_ia_read")
     */
    public function read(GnPost $gnPost, DataPost $dataPost, GnPostRepository $gnPostRepository, CookieIA $cookieIA, Request $request, Translation $translation): Response 
    {
        if (!$cookieIA->check("da-ia")) {
            $cookieIA->add("da-ia","[" . $gnPost->getId() . "]");
            goto done;
        } else {
            $dataPost->addDataPosts($gnPost->getId());
        }
        done:
        $lang = $request->query->get("lang");
        if($lang) {
            $gnPost->setPostTitle($translation->translateWithGoogleTranslate("en", "fr", $gnPost->getPostTitle()));
            $gnPost->setPostTitle($translation->translateWithGoogleTranslate("fr", $lang, $gnPost->getPostTitle()));
            
            $gnPost->setPostContent($translation->translateWithGoogleTranslate("en", "fr", $gnPost->getPostContent()));
            $gnPost->setPostContent($translation->translateWithGoogleTranslate("fr", $lang, $gnPost->getPostContent()));
        }
        $recentPosts = $gnPostRepository->getRecentPosts(20);
        return $this->render('ia/read.html.twig', [
            'post' => $gnPost,
            'recentPosts' => $recentPosts
        ]);
    } 
    
    
    #[Route('/translation', name: 'app_translation')]
    public function translate(Translation $translation): Response 
    {

        echo '<form> entrez le texte : <input type="text" name="mot"/><input type="submit" value="translate"/></form>';

        echo "<hr/>";

        $text =  $_GET["mot"];
        echo "Original : " . $text . "<hr/>";

        $fromLang = "fr";
        $toLang = "en";

        echo "Après traduction : " . $translation->translateWithGoogleTranslate($fromLang, $toLang, $text);
        // echo "Après traduction : " . $translation->translate($fromLang, $toLang, $text);

        die();
        return $this->render('ia/read.html.twig');
    }
}
