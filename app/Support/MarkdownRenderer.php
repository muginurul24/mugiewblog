<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use Highlight\Highlighter;
use Illuminate\Support\Str;

final class MarkdownRenderer
{
    /**
     * @var array<string, string>
     */
    private const LANGUAGE_ALIASES = [
        'blade' => 'xml',
        'html' => 'xml',
        'js' => 'javascript',
        'shell' => 'bash',
        'sh' => 'bash',
        'yml' => 'yaml',
    ];

    #[\NoDiscard]
    public static function render(string $markdown): string
    {
        return self::highlightHtml((string) Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
    }

    #[\NoDiscard]
    public static function highlightHtml(string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="markdown-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('markdown-root');

        if (! $root instanceof DOMElement) {
            return $html;
        }

        foreach (iterator_to_array($root->getElementsByTagName('code')) as $code) {
            if (! $code instanceof DOMElement || ! $code->parentNode instanceof DOMElement) {
                continue;
            }

            if ($code->parentNode->tagName !== 'pre' || Str::contains($code->getAttribute('class'), 'hljs')) {
                continue;
            }

            $language = self::extractLanguage($code);
            $highlighted = self::highlightCode($code->textContent, $language);

            $code->setAttribute('class', trim("hljs language-{$language}"));

            while ($code->firstChild) {
                $code->removeChild($code->firstChild);
            }

            $fragment = $document->createDocumentFragment();
            $fragment->appendXML($highlighted);
            $code->appendChild($fragment);
        }

        return self::innerHtml($root);
    }

    #[\NoDiscard]
    private static function extractLanguage(DOMElement $code): string
    {
        preg_match('/language-([a-z0-9_-]+)/i', $code->getAttribute('class'), $matches);

        return Str::lower($matches[1] ?? 'plaintext');
    }

    #[\NoDiscard]
    private static function highlightCode(string $code, string $language): string
    {
        if ($language === 'caddyfile') {
            return self::highlightCaddyfile($code);
        }

        $highlighter = new Highlighter;
        $resolvedLanguage = self::LANGUAGE_ALIASES[$language] ?? $language;

        if (! in_array($resolvedLanguage, $highlighter->listBundledLanguages(), true)) {
            return e($code);
        }

        return $highlighter->highlight($resolvedLanguage, $code)->value;
    }

    #[\NoDiscard]
    private static function highlightCaddyfile(string $code): string
    {
        $escaped = e($code);
        $escaped = preg_replace('/(#.*)$/m', '<span class="hljs-comment">$1</span>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(&quot;.*?&quot;)/', '<span class="hljs-string">$1</span>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(\{[^}\n]+\})/', '<span class="hljs-variable">$1</span>', $escaped) ?? $escaped;
        $escaped = preg_replace(
            '/^(\s*)(reverse_proxy|encode|header|respond|root|php_fastcgi|file_server|handle|route|redir|tls|log|import)\b/m',
            '$1<span class="hljs-keyword">$2</span>',
            $escaped,
        ) ?? $escaped;
        $escaped = preg_replace('/\b(\d+)\b/', '<span class="hljs-number">$1</span>', $escaped) ?? $escaped;

        return $escaped;
    }

    #[\NoDiscard]
    private static function innerHtml(DOMElement $element): string
    {
        return collect(iterator_to_array($element->childNodes))
            ->map(fn ($node): string => $element->ownerDocument?->saveHTML($node) ?: '')
            ->implode('');
    }
}
