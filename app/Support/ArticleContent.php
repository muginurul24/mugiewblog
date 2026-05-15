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

        self::prepareMedia($root);
        self::wrapTables($document, $root);

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

    private static function prepareMedia(DOMElement $root): void
    {
        foreach ($root->getElementsByTagName('img') as $image) {
            if (! $image instanceof DOMElement) {
                continue;
            }

            $image->setAttribute('loading', 'lazy');
            $image->setAttribute('decoding', 'async');

            if (! $image->hasAttribute('alt')) {
                $image->setAttribute('alt', '');
            }
        }
    }

    private static function wrapTables(DOMDocument $document, DOMElement $root): void
    {
        foreach (iterator_to_array($root->getElementsByTagName('table')) as $table) {
            if (! $table instanceof DOMElement) {
                continue;
            }

            $parent = $table->parentNode;

            if ($parent instanceof DOMElement && Str::contains($parent->getAttribute('class'), 'article-table-scroll')) {
                continue;
            }

            if (! $parent instanceof DOMNode) {
                continue;
            }

            $wrapper = $document->createElement('div');
            $wrapper->setAttribute('class', 'article-table-scroll');

            $parent->replaceChild($wrapper, $table);
            $wrapper->appendChild($table);
        }
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
