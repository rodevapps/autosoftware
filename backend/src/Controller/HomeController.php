<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private $dir,
    ) {}

    #[Route('/', name: 'app_root')]
    #[Route('/api', name: 'app_api')]
    #[Route('/api/save-message', name: 'app_index')]
    public function index(Request $request): JsonResponse
    {
        if ($request->getMethod() != "POST") {
            return $this->json([
                'message' => 'Bad request method. Only POST is allowed!',
                'status' => 'error',
            ]);
        } else {
            $response = json_decode($request->getContent(), true);

            $message = json_encode($response);

            $uuid = uniqid();

            $message = "{$uuid};{$message};" . time() . "\r\n";

            file_put_contents($this->dir . '/db.csv', $message, FILE_APPEND | LOCK_EX);

            return $this->json([
                'uuid' => $uuid,
                'status' => 'success',
            ]);
        }
    }

    #[Route('/api/show-messages', name: 'app_show')]
    public function show(Request $request): JsonResponse
    {
        $content = [];

        $handle = fopen($this->dir . '/db.csv', "r");

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $data = str_getcsv(trim($line), ';');

                $data[1] = json_decode($data[1], true);

                $content [] = $data;
            }
        
            fclose($handle);
        } else {
            return $this->json([
                'message' => 'Messages database empty! Add some data and try again.',
                'status' => 'error',
            ]);
        }

        $uuid = $request->query->get('uuid');
        $sort = $request->query->get('sort');
        $column = $request->query->get('column');

        if ($uuid !== null && $uuid !== "") {
            $c = array_filter($content, function($v, $k) use ($uuid) {
                return $v[0] === $uuid;
            }, ARRAY_FILTER_USE_BOTH);

            $content = $c;
        }

        if ($sort !== null) {            
            usort($content, function (array $a, array $b) use($sort, $column) {
                if ($sort == -1) {
                    if ($column === "time") {
                        return $b[2] <=> $a[2];
                    } else {
                        return $b[0] <=> $a[0];
                    }
                } else {
                    if ($column === "time") {
                        return $a[2] <=> $b[2];
                    } else {
                        return $a[0] <=> $b[0];
                    }
                }
            });
        }

        return new JsonResponse($content);
    }
}
