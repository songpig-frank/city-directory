<?php
/**
 * API: AI Profile Writer (via OpenRouter)
 */
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$business_name = trim($input['name'] ?? '');
$category = trim($input['category'] ?? '');
$notes = trim($input['notes'] ?? '');

if (empty($business_name)) {
    http_response_code(400);
    echo json_encode(['error' => 'Business name is required']);
    exit;
}

$api_key = config('openrouter_key');
if (empty($api_key)) {
    // For demo/local without key, return a mock response if they didn't provide one
    echo json_encode(['text' => "This is a professional description for {$business_name}, a top-rated {$category} in our community. Known for excellent service and quality! (Connect OpenRouter API key for real AI generation)"]);
    exit;
}

$model = config('openrouter_model') ?: 'google/gemini-2.0-flash-lite-preview-02-05:free';

$prompt = "Write a professional, inviting, and SEO-optimized business description for a directory listing. 
Business Name: {$business_name}
Category: {$category}
Additional Notes: {$notes}

Guidelines:
- Length: 2-3 paragraphs.
- Tone: Friendly, local, and professional.
- Focus: Highlight the value to the community.
- AEO: Use conversational keywords that AI assistants would recommend.
- Avoid: Corporate jargon or generic filler.
- Format: Plain text only.";

$ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json',
    'HTTP-Referer: https://tampakan.com', // Optional
    'X-Title: Tampakan Directory'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'model' => $model,
    'messages' => [
        ['role' => 'system', 'content' => 'You are an expert copywriter for local business directories.'],
        ['role' => 'user', 'content' => $prompt]
    ]
]));

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['error' => 'Connection error: ' . curl_error($ch)]);
    exit;
}
curl_close($ch);

$data = json_decode($response, true);
$ai_text = $data['choices'][0]['message']['content'] ?? 'Sorry, could not generate description.';

echo json_encode(['text' => $ai_text]);
