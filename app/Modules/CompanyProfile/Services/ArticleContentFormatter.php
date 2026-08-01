<?php

namespace App\Modules\CompanyProfile\Services;

use Illuminate\Support\Str;

class ArticleContentFormatter
{
    /**
     * @return array{
     *     blocks: list<array{type: string, text?: string, id?: string, level?: int, items?: list<string>}>,
     *     headings: list<array{id: string, text: string}>,
     *     sources: list<array{label: string, url: string}>
     * }
     */
    public function format(string $content): array
    {
        $lines = preg_split('/\R/u', trim($content)) ?: [];
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
}
