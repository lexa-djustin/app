<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
class ProductController extends AbstractController
{
    /** @var Helper\User */
    private $userHelper;

    /**
     * ProductController constructor.
     *
     * @param Helper\User $user
     */
    public function __construct(Helper\User $user)
    {
        $this->userHelper = $user;
    }

    /**
     * @Route("/", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function add(Request $request): Response
    {
        try {
            $this->userHelper->checkAndGet();

            $decoder = new Encoder\JsonDecode();
            $data = (array) $decoder->decode($request->getContent(), Encoder\JsonEncoder::FORMAT);
            $product = Entity\Product::fromArray($data);

            $this->getDoctrine()->getManager()->persist($product);
            $this->getDoctrine()->getManager()->flush();
        } catch (Exception\UserWasNotFound $e) {
            return new JsonResponse(['Reason' => $e->getMessage()], $e->getCode());
        }

        return new Response();
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     *
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $this->userHelper->checkAndGet();

            /** @var Entity\Product $product */
            $product = $this->getDoctrine()->getRepository(Entity\Product::class)->find($id);

            if (!$product) {
                return new JsonResponse(['Reason' => 'Product was not found'], 400);
            }

            $decoder = new Encoder\JsonDecode();
            $data = (array) $decoder->decode($request->getContent(), Encoder\JsonEncoder::FORMAT);
            $product->modify($data);

            $this->getDoctrine()->getManager()->persist($product);
            $this->getDoctrine()->getManager()->flush();

            return new Response();
        } catch (Exception\UserWasNotFound $e) {
            return new JsonResponse(['Reason' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @Route("/{id}", methods={"GET"})
     *
     * @param Request $request
     * @param int $id
     *
     * @return Response
     */
    public function receive(Request $request, $id): Response
    {
        try {
            $this->userHelper->checkAndGet();

            /** @var Entity\Product $product */
            $product = $this->getDoctrine()->getRepository(Entity\Product::class)->find($id);

            if (!$product) {
                return new JsonResponse(['Reason' => 'Product was not found'], 400);
            }

            return new JsonResponse($product);
        } catch (Exception\UserWasNotFound $e) {
            return new JsonResponse(['Reason' => $e->getMessage()], $e->getCode());
        }
    }
}