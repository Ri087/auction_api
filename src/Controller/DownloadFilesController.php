<?php

namespace App\Controller;

use App\Entity\DownloadFiles;
use App\Repository\DownloadFilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DownloadFilesController extends AbstractController
{
    #[Route('/', name: 'app.index')]
    public function index(): void
    {
    }

    #[Route('/api/download/files', name: 'download.files', methods: ['POST'])]
    public function createFile(Request $request, DownloadFilesRepository $repository, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {

        $newFile = new DownloadFiles();

        // $file = $request->files->get("file");

        $entityManager->persist($newFile);
        $entityManager->flush();
        $realname = $newFile->getRealName();
        $realpath = $newFile->getRealPath();
        $slug = $newFile->getSlug();
        $jsonPicture = [
            "id" => $newFile->getId(),
            "name" => $newFile->getName(),
            "realName" => $realname,
            "realPath" => $realpath,
            "publicPath" => $newFile->getPublicPath(),
            "mineType" => $newFile->getMineType(),
            "slug" => $slug,
        ];



        $location = $urlGenerator->generate('app.index', [], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($newFile, JsonResponse::HTTP_CREATED, ["Location" => $location . $realpath . "/" . $slug]);
    }
}
