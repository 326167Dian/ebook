<?php

namespace App\Support;

use DOMDocument;
use DOMDocumentFragment;
use DOMElement;
use DOMNode;
use DOMText;

class ContentLinkifier
{
    private const URL_PATTERN = '/(https?:\/\/[^\s<>"\']+|www\.[^\s<>"\']+)/i';

    private const TRAILING_PUNCTUATION = '.,;:!?)]}';

    private const SKIP_TAGS = ['a', 'script', 'style'];

    public static function linkify(string $html): string
    {
        $html = trim($html);

        if ($html === '' || !preg_match(self::URL_PATTERN, $html)) {
            return $html;
        }

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $container = $dom->getElementsByTagName('div')->item(0);

        if (!$container) {
            return $html;
        }

        self::walk($dom, $container);

        $result = '';
        foreach ($container->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return $result;
    }

    private static function walk(DOMDocument $dom, DOMNode $node): void
    {
        $child = $node->firstChild;

        while ($child !== null) {
            $next = $child->nextSibling;

            if ($child instanceof DOMText) {
                self::linkifyTextNode($dom, $child);
            } elseif ($child instanceof DOMElement && !in_array(strtolower($child->tagName), self::SKIP_TAGS, true)) {
                self::walk($dom, $child);
            }

            $child = $next;
        }
    }

    private static function linkifyTextNode(DOMDocument $dom, DOMText $textNode): void
    {
        $text = $textNode->nodeValue;

        if (!preg_match(self::URL_PATTERN, $text)) {
            return;
        }

        $parts = preg_split(self::URL_PATTERN, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false || count($parts) <= 1) {
            return;
        }

        $fragment = $dom->createDocumentFragment();

        foreach ($parts as $index => $part) {
            if ($part === '') {
                continue;
            }

            if ($index % 2 === 1) {
                self::appendLink($dom, $fragment, $part);
            } else {
                $fragment->appendChild($dom->createTextNode($part));
            }
        }

        $textNode->parentNode->replaceChild($fragment, $textNode);
    }

    private static function appendLink(DOMDocument $dom, DOMDocumentFragment $fragment, string $url): void
    {
        $trailing = '';
        while ($url !== '' && str_contains(self::TRAILING_PUNCTUATION, substr($url, -1))) {
            $trailing = substr($url, -1) . $trailing;
            $url = substr($url, 0, -1);
        }

        if ($url === '') {
            $fragment->appendChild($dom->createTextNode($trailing));
            return;
        }

        $href = stripos($url, 'www.') === 0 ? 'https://' . $url : $url;

        $link = $dom->createElement('a');
        $link->setAttribute('href', $href);
        $link->setAttribute('target', '_blank');
        $link->setAttribute('rel', 'noopener noreferrer');
        $link->appendChild($dom->createTextNode($url));

        $fragment->appendChild($link);

        if ($trailing !== '') {
            $fragment->appendChild($dom->createTextNode($trailing));
        }
    }
}
