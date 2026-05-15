<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Str;

final class ArticleContent
{
    /**
     * @return array{html: string, toc: array<int, array{id: string, title: string, level: int}>}
     */
    #[\NoDiscard]
    public static function prepare(string $html): array
    {
        if (blank($html)) {
            return ['html' => '', 'toc' => []];
        }

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="article-content-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('article-content-root');

        if (! $root instanceof DOMElement) {
            return ['html' => $html, 'toc' => []];
        }

        $toc = [];
        $usedIds = [];

        foreach ($root->getElementsByTagName('*') as $node) {
            if (! $node instanceof DOMElement || ! in_array($node->tagName, ['h2', 'h3'], true)) {
                continue;
            }

            $title = trim($node->textContent);

            if ($title === '') {
                continue;
            }

            $id = self::uniqueId(Str::slug($title), $usedIds);
            $node->setAttribute('id', $id);

            $toc[] = [
                'id' => $id,
                'title' => $title,
                'level' => (int) substr($node->tagName, 1),
            ];
        }

        return ['html' => self::innerHtml($root), 'toc' => $toc];
    }

    /**
     * @param  array<string, true>  $usedIds
     */
    #[\NoDiscard]
    private static function uniqueId(string $baseId, array &$usedIds): string
    {
        $baseId = $baseId !== '' ? $baseId : 'section';
        $id = $baseId;
        $suffix = 2;

        while (isset($usedIds[$id])) {
            $id = "{$baseId}-{$suffix}";
            $suffix++;
        }

        $usedIds[$id] = true;

        return $id;
    }

    #[\NoDiscard]
    private static function innerHtml(DOMElement $element): string
    {
        return collect(iterator_to_array($element->childNodes))
            ->map(fn (DOMNode $node): string => $element->ownerDocument?->saveHTML($node) ?: '')
            ->implode('');
    }
}
