<?php

namespace App\Support;

class HtmlWordTruncator
{
    /**
     * Keep HTML formatting and stop after $wordLimit words.
     * Longer content ends with "...".
     */
    public static function truncate($html, $wordLimit = 60)
    {
        $html = (string) $html;
        $wordLimit = (int) $wordLimit;

        if (trim($html) === '' || $wordLimit < 1) {
            return '';
        }

        $spaced = preg_replace('/</', ' <', $html);
        $plain = trim(preg_replace(
            '/\s+/u',
            ' ',
            html_entity_decode(strip_tags($spaced), ENT_QUOTES, 'UTF-8')
        ));

        if ($plain === '') {
            return $html;
        }

        $words = preg_split('/\s+/u', $plain, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($words) || count($words) <= $wordLimit) {
            return $html;
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $wrapped = '<div id="html-word-truncator-root">' . $html . '</div>';
        $loaded = $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $wrapped,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            return self::plainFallback($words, $wordLimit);
        }

        $xpath = new \DOMXPath($dom);
        $root = $xpath->query('//*[@id="html-word-truncator-root"]')->item(0);
        if (!$root) {
            return self::plainFallback($words, $wordLimit);
        }

        $remaining = $wordLimit;
        $truncated = false;
        self::truncateNode($root, $remaining, $truncated);

        if ($truncated) {
            self::appendEllipsis($root);
        }

        $htmlOut = '';
        foreach ($root->childNodes as $child) {
            $htmlOut .= $dom->saveHTML($child);
        }

        return $htmlOut;
    }

    private static function truncateNode(\DOMNode $node, &$remaining, &$truncated)
    {
        if ($remaining <= 0) {
            $truncated = true;
            return;
        }

        if ($node->nodeType === XML_TEXT_NODE) {
            self::truncateText($node, $remaining, $truncated);
            return;
        }

        $children = array();
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($remaining <= 0) {
                $truncated = true;
                if ($child->parentNode) {
                    $child->parentNode->removeChild($child);
                }
                continue;
            }

            self::truncateNode($child, $remaining, $truncated);
        }
    }

    private static function truncateText(\DOMText $node, &$remaining, &$truncated)
    {
        $parts = preg_split('/(\s+)/u', $node->textContent, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($parts)) {
            return;
        }

        $out = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            if (preg_match('/^\s+$/u', $part)) {
                if ($remaining > 0) {
                    $out .= $part;
                }
                continue;
            }

            if ($remaining <= 0) {
                $truncated = true;
                break;
            }

            $out .= $part;
            $remaining--;
        }

        $node->textContent = $out;
    }

    private static function appendEllipsis(\DOMNode $node)
    {
        $last = self::lastTextNode($node);
        if ($last) {
            $last->textContent = rtrim($last->textContent) . '...';
            return;
        }

        if ($node->ownerDocument) {
            $node->appendChild($node->ownerDocument->createTextNode('...'));
        }
    }

    private static function lastTextNode(\DOMNode $node)
    {
        if ($node->nodeType === XML_TEXT_NODE && trim($node->textContent) !== '') {
            return $node;
        }

        if (!$node->hasChildNodes()) {
            return null;
        }

        $children = array();
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        for ($i = count($children) - 1; $i >= 0; $i--) {
            $found = self::lastTextNode($children[$i]);
            if ($found) {
                return $found;
            }
        }

        return null;
    }

    private static function plainFallback(array $words, $wordLimit)
    {
        $slice = array_slice($words, 0, $wordLimit);

        return htmlspecialchars(implode(' ', $slice), ENT_QUOTES, 'UTF-8') . '...';
    }
}
