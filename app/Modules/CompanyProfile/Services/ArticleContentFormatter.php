<?php

namespace App\Modules\CompanyProfile\Services;

use Illuminate\Support\Str;

class ArticleContentFormatter
{
    /**
     * @return array{
     *     blocks: list<array{
     *         type: string,
     *         text?: string,
     *         id?: string,
     *         level?: int,
     *         items?: list<string>,
     *         title?: string,
     *         description?: string,
     *         titleIcon?: string,
     *         sources?: list<array{text: string, url: string, icon: string}>,
     *     }>,
     *     headings: list<array{id: string, text: string}>,
     *     sources: list<array{label: string, url: string}>
     * }
     */
    public function format(string $content): array
    {
        [$preparedContent, $trustedReferenceBlocks] = $this->extractTrustedReferenceBlocks($content);
        $normalizedContent = $this->normalizeContent($preparedContent);
        $lines = preg_split('/\R/u', $normalizedContent) ?: [];
        $blocks = [];
        $headings = [];
        $sources = [];
        $usedHeadingIds = [];

        for ($index = 0, $lineCount = count($lines); $index < $lineCount;) {
            $line = trim($lines[$index]);

            if ($line === '') {
                $index++;

                continue;
            }

            if (preg_match('/^__TRUSTED_REFERENCE_(\d+)__$/u', $line, $matches) === 1) {
                $referenceIndex = (int) $matches[1];
                $reference = $trustedReferenceBlocks[$referenceIndex] ?? null;
                $index++;

                if (is_array($reference)) {
                    $blocks[] = ['type' => 'trusted_reference', ...$reference];
                }

                continue;
            }

            if ($this->isSourcesHeading($line)) {
                $index++;
                $this->collectSources($lines, $index, $sources);

                continue;
            }

            if (preg_match('/^(#{2,3})\s+(.+)$/u', $line, $matches) === 1) {
                $this->addHeading($blocks, $headings, $usedHeadingIds, $matches[2], strlen($matches[1]));
                $index++;

                continue;
            }

            if ($this->isNumberedHeading($lines, $index)) {
                $heading = preg_replace('/^\d+\.\s+/u', '', $line) ?: $line;
                $this->addHeading($blocks, $headings, $usedHeadingIds, $heading, 2);
                $index++;

                continue;
            }

            if (preg_match('/^\d+\.\s+(.+)$/u', $line) === 1) {
                $items = [];

                while ($index < $lineCount && preg_match('/^\d+\.\s+(.+)$/u', trim($lines[$index]), $matches) === 1) {
                    $items[] = $matches[1];
                    $index++;
                }

                $blocks[] = ['type' => 'ordered_list', 'items' => $items];

                continue;
            }

            if (preg_match('/^-\s+(.+)$/u', $line) === 1) {
                $items = [];

                while ($index < $lineCount && preg_match('/^-\s+(.+)$/u', trim($lines[$index]), $matches) === 1) {
                    $items[] = $matches[1];
                    $index++;
                }

                $blocks[] = ['type' => 'unordered_list', 'items' => $items];

                continue;
            }

            $paragraphLines = [];

            while ($index < $lineCount && trim($lines[$index]) !== '') {
                $paragraphLines[] = trim($lines[$index]);
                $index++;
            }

            $paragraph = implode(' ', $paragraphLines);

            if (count($paragraphLines) === 1 && $blocks !== [] && $this->looksLikeHeading($paragraph)) {
                $this->addHeading($blocks, $headings, $usedHeadingIds, $paragraph, 2);

                continue;
            }

            $blocks[] = ['type' => 'paragraph', 'text' => $paragraph];
        }

        return [
            'blocks' => $blocks,
            'headings' => $headings,
            'sources' => $sources,
        ];
    }

    private function normalizeContent(string $content): string
    {
        if (! str_contains($content, '<')) {
            return trim($content);
        }

        libxml_use_internal_errors(true);

        $document = new \DOMDocument();
        $document->loadHTML('<?xml encoding="utf-8"?><body>'.$content.'</body>', \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD);
        $body = $document->getElementsByTagName('body')->item(0);
        $lines = [];

        if ($body !== null) {
            foreach ($body->childNodes as $childNode) {
                $lines[] = $this->extractNormalizedLines($childNode);
            }
        } else {
            $lines[] = $content;
        }

        libxml_clear_errors();
        $flattenedLines = [];

        foreach ($lines as $lineSet) {
            $flattenedLines = array_merge($flattenedLines, $lineSet);
        }

        if ($flattenedLines === []) {
            return trim($content);
        }

        return trim(preg_replace('/\n{3,}/u', "\n\n", implode("\n", array_map(
            static fn (string $line): string => trim($line),
            $flattenedLines,
        ))));
    }

    /** @return list<string> */
    private function extractNormalizedLines(\DOMNode $node): array
    {
        if ($node instanceof \DOMText) {
            return [trim(preg_replace('/\s+/u', ' ', (string) $node->nodeValue))];
        }

        if (! $node instanceof \DOMElement) {
            return [];
        }

        $tag = strtolower($node->nodeName);
        $text = fn (\DOMNode $childNode): string => preg_replace(
            '/\s+/u',
            ' ',
            trim((string) html_entity_decode((string) $childNode->textContent, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5)),
        ) ?: '';

        return match ($tag) {
            'p', 'div', 'section', 'article', 'header', 'main', 'aside', 'figure', 'figcaption' => [
                $text($node),
                '',
            ],
            'h1', 'h2' => ['## '.$text($node), ''],
            'h3', 'h4', 'h5', 'h6' => ['### '.$text($node), ''],
            'br' => [''],
            'ul' => $this->extractListLines($node, '- '),
            'ol' => $this->extractListLines($node, ''),
            'li' => [$text($node)],
            default => $this->extractDefaultLines($node),
        };
    }

    /** @return list<string> */
    private function extractDefaultLines(\DOMElement $node): array
    {
        $lines = [];

        foreach (iterator_to_array($node->childNodes) as $child) {
            $lines = array_merge($lines, $this->extractNormalizedLines($child));
        }

        return $lines;
    }

    /** @return list<string> */
    private function extractListLines(\DOMElement $listNode, string $bulletPrefix): array
    {
        $items = [];
        $counter = 1;

        foreach ($listNode->getElementsByTagName('li') as $liNode) {
            $itemText = $this->flattenText($liNode);

            if ($itemText === '') {
                continue;
            }

            if ($bulletPrefix === '') {
                $items[] = $counter . '. ' . $itemText;
                $counter++;
            } else {
                $items[] = $bulletPrefix . $itemText;
            }
        }

        return $items;
    }

    private function flattenText(\DOMNode $node): string
    {
        $text = html_entity_decode((string) $node->textContent, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);

        return trim(preg_replace('/\s+/u', ' ', $text)) ?: '';
    }

    /**
     * @param  list<array{type: string, text?: string, id?: string, level?: int, items?: list<string>}>  $blocks
     * @param  list<array{id: string, text: string}>  $headings
     * @param  array<string, int>  $usedHeadingIds
     */
    private function addHeading(array &$blocks, array &$headings, array &$usedHeadingIds, string $text, int $level): void
    {
        $baseId = Str::slug($text) ?: 'bagian';
        $occurrence = ($usedHeadingIds[$baseId] ?? 0) + 1;
        $usedHeadingIds[$baseId] = $occurrence;
        $id = $occurrence === 1 ? $baseId : $baseId.'-'.$occurrence;

        $blocks[] = ['type' => 'heading', 'text' => $text, 'id' => $id, 'level' => $level];
        $headings[] = ['id' => $id, 'text' => $text];
    }

    /** @param list<string> $lines */
    private function isNumberedHeading(array $lines, int $index): bool
    {
        return preg_match('/^\d+\.\s+(.+)$/u', trim($lines[$index])) === 1
            && trim($lines[$index + 1] ?? '') === '';
    }

    private function looksLikeHeading(string $text): bool
    {
        return Str::length($text) <= 90
            && Str::wordCount($text) <= 10
            && ! str_ends_with($text, '.')
            && ! Str::contains($text, ['http://', 'https://']);
    }

    private function isSourcesHeading(string $line): bool
    {
        return Str::startsWith(Str::lower($line), ['sumber tepercaya:', 'trusted sources:']);
    }

    /**
     * @param  list<string>  $lines
     * @param  list<array{label: string, url: string}>  $sources
     */
    private function collectSources(array $lines, int &$index, array &$sources): void
    {
        for ($lineCount = count($lines); $index < $lineCount; $index++) {
            $line = trim($lines[$index]);

            if ($line === '') {
                continue;
            }

            $line = preg_replace('/^-\s+/u', '', $line) ?: $line;

            if (preg_match('/^(.*?):\s*(https?:\/\/\S+)$/u', $line, $matches) !== 1) {
                continue;
            }

            $url = rtrim($matches[2], '.,');

            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                continue;
            }

            $sources[] = [
                'label' => trim($matches[1]),
                'url' => $url,
            ];
        }
    }

    /** @return array{0: string, 1: list<array{title: string, description: string, titleIcon: string, sources: list<array{text:string,url:string,icon:string}>}>} */
    private function extractTrustedReferenceBlocks(string $content): array
    {
        if (! str_contains($content, '<')) {
            return [$content, []];
        }

        libxml_use_internal_errors(true);

        $document = new \DOMDocument();
        if (! $document->loadHTML('<?xml encoding="utf-8"?><body>'.$content.'</body>', \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD)) {
            libxml_clear_errors();

            return [$content, []];
        }

        $body = $document->getElementsByTagName('body')->item(0);

        if ($body === null) {
            libxml_clear_errors();

            return [$content, []];
        }

        $referenceNodes = [];
        $xpath = new \DOMXPath($document);
        $query = $xpath->query('.//div[contains(concat(" ", normalize-space(@class), " "), " reference-card ")]', $body);

        if ($query === false) {
            libxml_clear_errors();

            return [$content, []];
        }

        foreach ($query as $node) {
            if ($node instanceof \DOMElement) {
                $referenceNodes[] = $node;
            }
        }

        if ($referenceNodes === []) {
            libxml_clear_errors();

            return [$content, []];
        }

        $trustedReferenceBlocks = [];

        foreach (array_reverse($referenceNodes) as $node) {
            $referenceData = $this->extractTrustedReferenceData($node);

            if ($referenceData === null) {
                continue;
            }

            $index = (string) count($trustedReferenceBlocks);
            $trustedReferenceBlocks[$index] = $referenceData;
            $placeholder = "__TRUSTED_REFERENCE_{$index}__";

            if ($node->parentNode !== null) {
                $node->parentNode->replaceChild($document->createTextNode("\n{$placeholder}\n"), $node);
            }
        }

        $convertedContent = '';

        foreach (iterator_to_array($body->childNodes) as $child) {
            $convertedContent .= $document->saveHTML($child);
        }

        libxml_clear_errors();

        return [$convertedContent, $trustedReferenceBlocks];
    }

    /** @return array{title: string, description: string, titleIcon: string, sources: list<array{text:string,url:string,icon:string}>}|null */
    private function extractTrustedReferenceData(\DOMElement $referenceCard): ?array
    {
        $document = $referenceCard->ownerDocument;
        if ($document === null) {
            return null;
        }

        $xpath = new \DOMXPath($document);
        $text = static function (? \DOMNode $node): string {
            if ($node === null) {
                return '';
            }

            return trim(preg_replace('/\s+/u', ' ', (string) $node->textContent)) ?: '';
        };
        $iconClass = static function (? \DOMNode $node): string {
            if (! $node instanceof \DOMElement) {
                return '';
            }

            if (preg_match('/\b(icon-\[[^\]]+\])\b/u', $node->getAttribute('class')) === 1) {
                return trim((string) preg_replace('/^.*?(icon-\[[^\]]+\]).*$/u', '$1', $node->getAttribute('class')));
            }

            foreach ($node->getElementsByTagName('span') as $span) {
                if (! $span instanceof \DOMElement) {
                    continue;
                }

                if (preg_match('/\b(icon-\[[^\]]+\])\b/u', $span->getAttribute('class')) === 1) {
                    return trim((string) preg_replace('/^.*?(icon-\[[^\]]+\]).*$/u', '$1', $span->getAttribute('class')));
                }
            }

            return '';
        };

        $descriptionNode = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " reference-titles ")]//*[self::p][1]', $referenceCard)->item(0);
        $titleIconNode = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " reference-icon ")]', $referenceCard)->item(0);
        $titleNode = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " reference-titles ")]//*[self::h4][1]', $referenceCard)->item(0);

        if ($titleNode === null) {
            return null;
        }

        $sources = [];
        $sourceNodes = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " reference-item ")]', $referenceCard);

        if ($sourceNodes instanceof \DOMNodeList) {
            foreach ($sourceNodes as $sourceNode) {
                if (! $sourceNode instanceof \DOMElement) {
                    continue;
                }

                $url = $sourceNode->getAttribute('href');
                if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                    continue;
                }

                $sourceTextNode = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " item-text ")]', $sourceNode)->item(0);
                $sourceIconNode = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " item-icon ")]', $sourceNode)->item(0);

                $sourceText = $text($sourceTextNode);
                $sourceIcon = $text($sourceIconNode);

                if ($sourceText === '') {
                    continue;
                }

                $sources[] = [
                    'text' => $sourceText,
                    'url' => $url,
                    'icon' => $sourceIcon === '' ? '🔖' : $sourceIcon,
                ];
            }
        }

        $referenceTitleIcon = $iconClass($titleIconNode) ?: 'icon-[tabler--books]';

        return [
            'title' => $text($titleNode),
            'description' => $text($descriptionNode) !== '' ? $text($descriptionNode) : 'Sumber primer yang digunakan sebagai dasar penulisan dan verifikasi informasi.',
            'titleIcon' => $referenceTitleIcon,
            'sources' => $sources,
        ];
    }
}
