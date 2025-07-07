<!-- AIzaSyDaZ4bVxwt4Rgox5lj9FOLOv3DGuyAQXFw -->
 <?php

$apiKey = 'AIzaSyDaZ4bVxwt4Rgox5lj9FOLOv3DGuyAQXFw';

$question = "Who is Donald Trump?";

// Google Gemini API endpoint
$apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

$requestData = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => $question
                ]
            ]
        ]
    ]
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);

$response = curl_exec($ch);

if (curl_error($ch)) {
    echo "cURL Error: " . curl_error($ch) . "\n";
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "HTTP Error: " . $httpCode . "\n";
    echo "Response: " . $response . "\n";
    exit;
}

$responseData = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSON Decode Error: " . json_last_error_msg() . "\n";
    echo "Raw Response: " . $response . "\n";
    exit;
}

if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    echo "Question: " . $question . "\n\n";
    echo "Answer from Google Gemini:\n";
    echo "========================\n";
    echo $responseData['candidates'][0]['content']['parts'][0]['text'] . "\n";
} else {
    echo "Error: Could not extract answer from response\n";
    echo "Full Response: " . print_r($responseData, true) . "\n";
}