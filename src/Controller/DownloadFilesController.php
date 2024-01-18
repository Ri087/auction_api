<?php

namespace App\Controller;

use App\Entity\DownloadFiles;
use App\Repository\DownloadFilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DownloadFilesController extends AbstractController
{
    #[Route('/', name: 'app.index')]
    public function index(): void
    {
    }

    #[Route('/api/file', name: 'files.create', methods: ["POST"])]
    public function createFile(
        Request $request,
        DownloadFilesRepository $repository,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator
    ): JsonResponse {
        $newFile = new DownloadFiles();

        $file = $request->files->get("file");

        $newFile->setFile($file);
        $entityManager->persist($newFile);
        $entityManager->flush();

        $realname = $newFile->getRealname();
        $realpath = $newFile->getRealpath();
        $slug = $newFile->getSlug();
        $jsonPicture = [
            "id" => $newFile->getId(),
            "name" => $newFile->getName(),
            "realname" => $realname,
            "realpath" => $realpath,
            "mimetype" => $newFile->getMimeType(),
            "slug" => $slug,
        ];
        $location = $urlGenerator->generate("app.index", [], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonPicture, Response::HTTP_CREATED, ["Location" => $location . $realpath . "/" . $slug]);
    }
}
