<?php

namespace App\Controller;

use App\Form\TagFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchFormController extends AbstractController
{
    #[Route('/search/form', name: 'search_form')]
    public function searchForm(Request $request)
    {
        $form = $this->createForm(TagFormType::class);

        return $this->render('partials/_search_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
