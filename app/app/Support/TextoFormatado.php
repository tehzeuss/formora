<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class TextoFormatado
{
    public static function limpar(?string $html): string
    {
        $documento = new DOMDocument;
        $anterior = libxml_use_internal_errors(true);
        $documento->loadHTML('<?xml encoding="UTF-8"><div>'.($html ?? '').'</div>', LIBXML_NONET | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($anterior);
        $raiz = $documento->getElementsByTagName('div')->item(0);
        if (! $raiz) {
            return '';
        }
        $renderizar = function (DOMNode $node) use (&$renderizar): string {
            if ($node->nodeType === XML_TEXT_NODE) {
                return htmlspecialchars($node->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
            if (! $node instanceof DOMElement || in_array(strtolower($node->tagName), ['script', 'style', 'iframe', 'object'], true)) {
                return '';
            }
            $conteudo = '';
            foreach ($node->childNodes as $filho) {
                $conteudo .= $renderizar($filho);
            }
            $tag = strtolower($node->tagName);
            if (in_array($tag, ['b', 'strong', 'i', 'em', 'u'], true)) {
                return '<'.$tag.'>'.$conteudo.'</'.$tag.'>';
            }
            if ($tag === 'a') {
                $url = trim($node->getAttribute('href'));
                if (preg_match('~^https?://~i', $url) && filter_var($url, FILTER_VALIDATE_URL)) {
                    return '<a href="'.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').'" target="_blank" rel="noopener noreferrer">'.$conteudo.'</a>';
                }
            }

            return $conteudo;
        };
        $resultado = '';
        foreach ($raiz->childNodes as $filho) {
            $resultado .= $renderizar($filho);
        }

        return $resultado;
    }
}
