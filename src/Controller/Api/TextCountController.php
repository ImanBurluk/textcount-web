<?php
namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

final class TextCountController
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];
        $text = (string)($payload['text'] ?? '');

        if ($text === '') {
            return new JsonResponse(['error' => 'text is required'], 400);
        }

        // Быстро и стабильно через бинарник пакета
        $proc = new Process([PHP_BINARY, './vendor/bin/textcount', $text]);
        $proc->mustRun();
        $length = (int)trim($proc->getOutput());

        return new JsonResponse(['ok' => true, 'data' => ['length' => $length]]);
    }
}
