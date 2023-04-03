<?php

namespace App\Controller;

use App\Service\CompanyMatcher;

class FormController extends Controller
{
    public function index(): void
    {
        $this->render('form.twig');
    }

    public function submit(): void
    {
        $matcher = new CompanyMatcher($this->db());
        $matcher->match($_POST);
        $matchedCompanies = $matcher->results();

        $this->render('results.twig', [
            'matchedCompanies' => $matchedCompanies
        ]);
    }
}
