<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(0);

require_once('../includes/db.php');

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

// Check if a file was uploaded
if (!isset($_FILES['image']) || !isset($_POST['farmer_email'])) {
    echo json_encode(['error' => 'No image file uploaded or farmer email not provided']);
    exit;
}

$file = $_FILES['image'];
$farmer_email = $_POST['farmer_email'];
$apiKey = $_ENV['GEMINI_API_KEY'];

if (!$apiKey) {
    echo json_encode(['error' => 'API key not found in environment variables']);
    exit;
}

// Function to make API request to Gemini Vision
function analyzeImage($imagePath, $apiKey) {
    try {
        $imageData = base64_encode(file_get_contents($imagePath));
        
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;
        
        $data = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => "Analyze this plant image for diseases. Provide a detailed response in the following format:

1. Disease Name and Brief Description (in simple Indian English)
2. Precautions to prevent spread
3. Natural and cultural remedies
4. Recommended medicines or treatments
5. Additional important information for farmers

If the plant appears healthy, just state that it's healthy with a brief explanation."
                        ],
                        [
                            "inline_data" => [
                                "mime_type" => "image/jpeg",
                                "data" => $imageData
                            ]
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.4,
                "topK" => 32,
                "topP" => 1,
                "maxOutputTokens" => 2048
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception('API request failed with status code: ' . $httpCode . '. Response: ' . $response);
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response: ' . json_last_error_msg());
        }

        // Process the response text to extract structured information
        $text = $decodedResponse['candidates'][0]['content']['parts'][0]['text'];
        
        // Parse the response into sections
        $lines = explode("\n", $text);
        $result = [
            'result' => '',
            'confidence' => 85,
            'disease_details' => [
                'precautions' => [],
                'remedies' => [],
                'medicines' => [],
                'additional_info' => []
            ]
        ];

        $currentSection = 'description';
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (strpos($line, 'Precautions') !== false) {
                $currentSection = 'precautions';
                continue;
            } elseif (strpos($line, 'Natural and cultural remedies') !== false || strpos($line, 'Remedies') !== false) {
                $currentSection = 'remedies';
                continue;
            } elseif (strpos($line, 'Recommended medicines') !== false || strpos($line, 'Treatments') !== false) {
                $currentSection = 'medicines';
                continue;
            } elseif (strpos($line, 'Additional') !== false) {
                $currentSection = 'additional';
                continue;
            }

            // Remove bullet points and numbers
            $line = preg_replace('/^[\d\.\-\*]+\s*/', '', $line);
            
            switch ($currentSection) {
                case 'description':
                    $result['result'] .= $line . "\n";
                    break;
                case 'precautions':
                    $result['disease_details']['precautions'][] = $line;
                    break;
                case 'remedies':
                    $result['disease_details']['remedies'][] = $line;
                    break;
                case 'medicines':
                    $result['disease_details']['medicines'][] = $line;
                    break;
                case 'additional':
                    $result['disease_details']['additional_info'][] = $line;
                    break;
            }
        }

        return $result;
    } catch (Exception $e) {
        throw new Exception('Analysis failed: ' . $e->getMessage());
    }
}

try {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed with error code: ' . $file['error']);
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG and PNG are allowed.');
    }

    // Create uploads directory if it doesn't exist
    $uploadDir = __DIR__ . '/../uploads/plant_diseases/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate unique filename
    $filename = uniqid() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to save uploaded file.');
    }

    // Analyze the image
    $result = analyzeImage($uploadPath, $apiKey);

    // Save to database
    $stmt = $connection->prepare("INSERT INTO plant_disease_detection (farmer_email, disease_name, description, confidence_score, image_path) VALUES (?, ?, ?, ?, ?)");
    $diseaseName = 'Unknown';
    if (strpos(strtolower($result['result']), 'healthy') !== false) {
        $diseaseName = 'Healthy';
    } else {
        // Try to extract disease name from the first line or sentence
        $lines = explode('.', $result['result']);
        foreach ($lines as $line) {
            if (strpos(strtolower($line), 'disease') !== false) {
                $diseaseName = trim($line);
                break;
            }
        }
    }
    $relativePath = 'uploads/plant_diseases/' . $filename;
    $stmt->bind_param("sssds", $farmer_email, $diseaseName, $result['result'], $result['confidence'], $relativePath);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to save detection result to database: ' . $stmt->error);
    }

    // Return the analysis result
    echo json_encode([
        'success' => true,
        'result' => $result['result'],
        'confidence' => $result['confidence'],
        'disease_details' => $result['disease_details'],
        'image_path' => $relativePath
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
