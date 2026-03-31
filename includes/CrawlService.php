<?php
/**
 * CityDirectory — Crawl & Extraction Service
 * Handles interaction with Firecrawl or similar AI crawling services.
 */

class CrawlService {
    private string $api_key;
    private string $base_url = 'https://api.firecrawl.dev/v1';

    public function __construct(?string $api_key = null) {
        $this->api_key = $api_key ?? config('firecrawl_api_key') ?? '';
    }

    /**
     * Crawl a single URL and return structured data.
     */
    public function scrape(string $url): array {
        if (empty($this->api_key)) {
            // Mock data for demonstration if no API key
            return $this->getMockData($url);
        }

        // Real Firecrawl Scrape implementation
        $ch = curl_init($this->base_url . '/scrape');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'url' => $url,
            'formats' => ['json'],
            'jsonOptions' => [
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => ['type' => 'string'],
                        'description' => ['type' => 'string'],
                        'address' => ['type' => 'string'],
                        'phone' => ['type' => 'string'],
                        'website' => ['type' => 'string'],
                        'facebook' => ['type' => 'string'],
                        'category' => ['type' => 'string'],
                        'hours' => ['type' => 'string']
                    ]
                ]
            ]
        ]));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            throw new Exception("Crawl failed with code $http_code: " . $response);
        }

        $data = json_decode($response, true);
        return $data['data']['json'] ?? [];
    }

    /**
     * Mock data generator for testing/demo.
     */
    private function getMockData(string $url): array {
        sleep(2); // Simulate network lag
        
        // Try to guess from URL
        $name = 'New Business';
        if (strpos($url, 'tampakan') !== false) $name = 'Tampakan Local Hub';
        
        return [
            [
                'name' => $name,
                'description' => 'Automatically discovered business from ' . $url,
                'address' => 'Poblacion, Tampakan, South Cotabato',
                'phone' => '0912-345-6789',
                'website' => $url,
                'facebook' => 'https://facebook.com/tampakanhub',
                'category' => 'General Business',
                'hours' => 'Mon-Fri: 8am-5pm'
            ],
            [
                'name' => 'Demo Service Pro',
                'description' => 'Professional services identified in the area.',
                'address' => 'Barangay Liberty, Tampakan',
                'phone' => '0999-000-1111',
                'website' => '',
                'facebook' => '',
                'category' => 'Services',
                'hours' => '24/7'
            ]
        ];
    }
}
