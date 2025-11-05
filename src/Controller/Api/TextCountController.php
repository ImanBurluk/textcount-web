<?php
namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Imanburluk\TextCount\TextCount;            // фасад
use Imanburluk\TextCount\Factory\TextCountFactory; // фабрика (необязательно, но явно)

final class TextCountController
{
    public function __invoke(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];
        $text = (string)($payload['text'] ?? '');

        if ($text === '') {
            return new JsonResponse(['error' => 'text is required'], 400);
        }

        // Явно укажем фабрику (выберет mb_strlen при наличии mbstring)
        TextCount::setFactory(new TextCountFactory());

        // Основной подсчёт
        $length = (new TextCount())->count($text);

        // (опционально) если нужно строго «мультибайтово» под конкретную кодировку:
        // $lengthUtf8 = (new TextCount())->countMb($text, 'UTF-8');

        return new JsonResponse([
            'ok'   => true,
            'data' => [
                'length' => $length,
                // 'length_utf8' => $lengthUtf8, // если понадобится
            ],
        ]);
    }
}
