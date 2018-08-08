<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 * @Route("/api/v1")
 */
class IndexСontroller extends AbstractController
{
    /**
     * @Route("/*", methods={"OPTIONS"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function options(Request $request): Response
    {
        return new Response();
    }
}