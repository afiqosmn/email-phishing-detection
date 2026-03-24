<?php

namespace App\Services;

use ZBateson\MailMimeParser\MailMimeParser;

class EmailParserService
{
    public function parse(string $gmailRaw): array
    {
        // Gmail raw is base64url
        $decoded = base64_decode(strtr($gmailRaw, '-_', '+/'));
        if ($decoded === false) {
            throw new \Exception('Failed to decode Gmail raw message');
        }

        $stream = fopen('php://memory', 'r+');
        if ($stream === false) {
            throw new \Exception('Failed to open memory stream for parsing');
        }

        fwrite($stream, $decoded);
        rewind($stream);

        try {
            $parser = new MailMimeParser();
            $message = $parser->parse($stream, true);

            $textBody = trim(
                $message->getTextContent()
                ?: strip_tags($message->getHtmlContent())
            );

            return [
                'subject' => $message->getHeaderValue('subject') ?? '(No Subject)',
                'from'    => $this->extractEmail($message->getHeaderValue('from')),
                'date'    => $message->getHeaderValue('date'),
                'body'    => $textBody,
                'has_html'=> $message->getHtmlContent() !== null,
            ];
        } finally {
            // Close stream to avoid resource leak
            fclose($stream);
        }
    }

    private function extractEmail(?string $from): ?string
    {
        if (!$from) return null;

        if (preg_match('/<(.+?)>/', $from, $m)) {
            return strtolower(trim($m[1]));
        }

        return strtolower(trim($from));
    }
}
