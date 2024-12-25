<?php
header('Content-Type: application/json');
session_start();

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

function callGLHFAPI($message) {
    $api_key = $_ENV['GLHF_API_KEY'] ?? '';
    if (empty($api_key)) {
        return ['error' => 'API key not configured'];
    }

    $url = 'https://glhf.chat/api/openai/v1/chat/completions';
    
    $data = [
        'model' => 'hf:mistralai/Mistral-7B-Instruct-v0.3',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful farming assistant. Provide concise, practical advice about agriculture, farming techniques, and crop management. Always start with a friendly greeting, then provide a structured response with numbered points for clarity.'
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 500
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error) {
        return ['error' => 'Connection error: ' . $error];
    }

    if ($httpCode !== 200) {
        return ['error' => 'API error: HTTP ' . $httpCode];
    }

    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Invalid response from API'];
    }

    $content = $result['choices'][0]['message']['content'] ?? 'Sorry, I could not process your request.';
    
    return [
        'response' => $content,
        'status' => 'success'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = $input['message'] ?? '';
    
    if (empty($message)) {
        echo json_encode(['error' => 'No message provided']);
        exit;
    }

    $response = callGLHFAPI($message);
    echo json_encode($response);
    exit;
} else {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}
