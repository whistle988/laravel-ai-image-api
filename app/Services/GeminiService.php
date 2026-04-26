<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;

class GeminiService
{
    public function generatePromptFromImage(UploadedFile $image): string
    {
        set_time_limit(120);

        $imageData = base64_encode(file_get_contents($image->getPathname()));
        $mimeType = $image->getMimeType();

        //$client = new Client();
        $client = new Client([
            'proxy' => 'socks5h://127.0.0.1:12334',
            'timeout' => 120,
            'connect_timeout' => 30,
        ]);


        $response = $client->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent',
            [
                'headers' => [
                    'x-goog-api-key' => config('services.gemini.key'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => 'Analyze this image and generate a detailed descriptive prompt that could be used to recreate a similar image with AI image generation tools.',
                                ],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $imageData,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['candidates'][0]['content']['parts'][0]['text']
            ?? 'No prompt generated.';
    }
}
