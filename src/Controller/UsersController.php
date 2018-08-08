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
 * @Route("/api/v1/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/login", methods={"POST"})
     */
    public function login(Request $request): Response
    {
        if ($request->getContentType() !== 'json') {
            return new JsonResponse(['reason' => 'Invalid data type'], 400);
        }

        $decoder = new Encoder\JsonDecode();
        $data = $decoder->decode($request->getContent(), Encoder\JsonEncoder::FORMAT);

        $user = $this->getDoctrine()->getRepository(Entity\User::class)->findOneBy([
            'username' => $data->username,
            'password' => $data->password,
        ]);

        if (!$user) {
            return new JsonResponse(['reason' => 'User was not found'], 400);
        }

        if (!$user->hasToken()) {
            $user->createToken();
        }

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        $response = new JsonResponse(['token' => $user->getToken()]);

        return $response;
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

        exit(__FUNCTION__);

        return new Response('', 200);
    }

    protected function checkUser(Request $request)
    {
        /** @var Entity\User $user */
        $user = $this->getDoctrine()->getRepository(Entity\User::class)->findOneByToken(
            $request->headers->get('x-access-token')
        );

        if (!$user) {
            return new JsonResponse(['reason' => 'User was not found'], 400);
        }
    }
}