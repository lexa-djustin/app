<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity;

/**
 *
 * @Route("/api/v1/products")
 */
class ProductСontroller extends AbstractController
{
    /**
     * @Route("/product", methods={"POST"})
     */
    public function add(Request $request): Response
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse(['reason' => 'Invalid data type'], 400);
        }

        /** @var Entity\User $user */
        $user = $this->getDoctrine()->getRepository(Entity\User::class)->findOneByToken(
            $request->headers->get('x-access-token')
        );

        if (!$user) {
            return new Response('', 401);
        }

        $decoder = new Encoder\JsonDecode();
        $data = (array) $decoder->decode($request->getContent(), Encoder\JsonEncoder::FORMAT);
        $product = Entity\Product::fromArray($data);

        $this->getDoctrine()->getManager()->persist($product);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 200);;
    }

    /**
     * @Route("/product/{id}", methods={"PUT"})
     */
    public function update(Request $request): Response
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse(['reason' => 'Invalid data type'], 400);
        }


        var_dump($request->query->get('id'));exit();
        /** @var Entity\User $user */
        $product = $this->getDoctrine()->getRepository(Entity\Product::class)->find(
            $request->request->get('id')
        );

        $decoder = new Encoder\JsonDecode();
        $data = (array) $decoder->decode($request->getContent(), Encoder\JsonEncoder::FORMAT);
        $product = Entity\Product::fromArray($data);

        $product->modify($data);

        $this->getDoctrine()->getManager()->persist($product);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 200);;
    }

    /**
     * @Route("/me", methods={"GET"})
     */
    public function me(Request $request): Response
    {
        /** @var Entity\User $user */
        $user = $this->getDoctrine()->getRepository(Entity\User::class)->findOneByToken(
            $request->headers->get('x-access-token')
        );

        if (!$user) {
            return new JsonResponse(['reason' => 'User was not found'], 400);
        }

        return new JsonResponse($user);
    }

    /**
     * @Route("/logout", methods={"GET"})
     */
    public function logout(Request $request): Response
    {
        /** @var Entity\User $user */
        $user = $this->getDoctrine()->getRepository(Entity\User::class)->findOneByToken(
            $request->headers->get('x-access-token')
        );

        if (!$user) {
            return new JsonResponse(['reason' => 'User was not found'], 400);
        }

        $user->clearToken();

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 200);
    }
}