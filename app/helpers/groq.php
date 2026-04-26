<?php
/**
 * Groq AI Helper
 * ──────────────
 * Provides function to generate completions via Groq API.
 */

/**
 * Generate a text response using Groq AI.
 *
 * @param string $prompt
 * @param string $apiKey
 * @return string
 */
function groq_generate(string $prompt, string $apiKey): string
{
    if (empty($apiKey)) {
        return "Error: Groq API Key is not configured.";
    }

    $url = "https://api.groq.com/openai/v1/chat/completions";

    $data = [
        "model" => "llama3-8b-8192",
        "messages" => [
            [
                "role" => "user",
                "content" => $prompt
            ]
        ],
        "temperature" => 0.7,
        "max_tokens" => 800
    ];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json\r\nAuthorization: Bearer {$apiKey}\r\n",
            "method"  => "POST",
            "content" => json_encode($data),
            "ignore_errors" => true // To read the body even if non-200
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === false) {
        return "Error: Unable to connect to Groq API.";
    }

    $response = json_decode($result, true);

    if (isset($response['error'])) {
        return "API Error: " . ($response['error']['message'] ?? 'Unknown error');
    }

    return $response['choices'][0]['message']['content'] ?? "Error: Unexpected API response format.";
}
