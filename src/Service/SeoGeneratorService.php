<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SeoGeneratorService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $anthropicApiKey,
    ) {
    }

    /**
     * Generate SEO metadata for all locales using Claude AI.
     *
     * @param string $entityType  e.g. 'blog-post', 'lab-project', 'page', 'totem'
     * @param array  $locales     e.g. [['code' => 'hr', 'label' => 'Hrvatski'], ...]
     * @param array  $content     keyed by locale: ['hr' => ['title' => '...', 'body' => '...'], ...]
     * @param string $siteName    e.g. 'Go2Digital'
     *
     * @return array  ['translations' => ['hr' => [...], 'en' => [...]], 'ogType' => '...']
     */
    public function generate(string $entityType, array $locales, array $content, string $siteName = 'Go2Digital'): array
    {
        if (!$this->anthropicApiKey) {
            throw new \RuntimeException('ANTHROPIC_API_KEY is not configured.');
        }

        $localeList = implode(', ', array_map(fn($l) => "{$l['code']} ({$l['label']})", $locales));
        $localeCodes = array_map(fn($l) => $l['code'], $locales);

        $contentSummary = $this->buildContentSummary($content);

        $jsonStructure = $this->buildExpectedJsonStructure($localeCodes);

        $prompt = <<<PROMPT
You are an expert SEO specialist. Generate optimized SEO metadata for a {$entityType} on the "{$siteName}" website.

**Available languages:** {$localeList}

**Entity content by language:**
{$contentSummary}

**Instructions:**
1. Generate SEO metadata for EACH language listed above.
2. For each language, create:
   - `title`: SEO title, max 60 characters. Compelling, includes primary keyword. Do NOT append the site name.
   - `description`: Meta description, 120-160 characters. Actionable, includes keywords, encourages clicks.
   - `keywords`: 5-8 relevant keywords, comma-separated.
   - `ogTitle`: Open Graph title (can be slightly longer/more engaging than SEO title, max 90 chars).
   - `ogDescription`: Open Graph description (optimized for social sharing, max 200 chars).
   - `twitterTitle`: Twitter card title (concise, max 70 chars).
   - `twitterDescription`: Twitter card description (max 200 chars).
3. Each language's content must be written natively in that language — NOT translated from another.
4. If content for a language is missing or empty, infer from the available languages but write natively.
5. Use the entity type context: blog posts should be informative, lab projects should be innovative/technical, pages should be descriptive.

**Respond with ONLY valid JSON in this exact structure:**
{$jsonStructure}
PROMPT;

        $response = $this->httpClient->request('POST', 'https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $this->anthropicApiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ],
            'json' => [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 2048,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ],
        ]);

        $result = $response->toArray();
        $text = $result['content'][0]['text'] ?? '';

        // Extract JSON from response (handle possible markdown code blocks)
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            $parsed = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && isset($parsed['translations'])) {
                return $parsed;
            }
        }

        throw new \RuntimeException('Failed to parse AI response.');
    }

    private function buildContentSummary(array $content): string
    {
        $parts = [];
        foreach ($content as $locale => $fields) {
            $lines = ["[{$locale}]"];
            foreach ($fields as $key => $value) {
                if (!empty($value)) {
                    // Strip HTML and truncate long content
                    $clean = strip_tags((string) $value);
                    if (mb_strlen($clean) > 500) {
                        $clean = mb_substr($clean, 0, 500) . '...';
                    }
                    $lines[] = "  {$key}: {$clean}";
                }
            }
            if (count($lines) > 1) {
                $parts[] = implode("\n", $lines);
            }
        }

        return $parts ? implode("\n\n", $parts) : '(No content provided)';
    }

    private function buildExpectedJsonStructure(array $localeCodes): string
    {
        $translations = [];
        foreach ($localeCodes as $code) {
            $translations[] = "    \"{$code}\": { \"title\": \"...\", \"description\": \"...\", \"keywords\": \"...\", \"ogTitle\": \"...\", \"ogDescription\": \"...\", \"twitterTitle\": \"...\", \"twitterDescription\": \"...\" }";
        }

        return "{\n  \"translations\": {\n" . implode(",\n", $translations) . "\n  },\n  \"ogType\": \"website\"\n}";
    }
}
