<?php

namespace App\Services;

class CommentSpamDetector
{
    /**
     * @param  array{name: string, email: string, content: string, website?: string|null, ip?: string|null, user_agent?: string|null}  $payload
     */
    #[\NoDiscard]
    public function isSpam(array $payload): bool
    {
        $content = mb_strtolower($payload['content']);
        $name = mb_strtolower($payload['name']);
        $email = mb_strtolower($payload['email']);
        $website = mb_strtolower((string) ($payload['website'] ?? ''));
        $userAgent = mb_strtolower((string) ($payload['user_agent'] ?? ''));

        if (filled($website)) {
            return true;
        }

        $linkCount = preg_match_all('/https?:\/\//i', $content) ?: 0;

        if ($linkCount > 2) {
            return true;
        }

        $blockedTerms = [
            'casino',
            'crypto giveaway',
            'free money',
            'loan instant',
            'slot gacor',
            'viagra',
        ];

        foreach ($blockedTerms as $term) {
            if (str_contains($content, $term) || str_contains($name, $term) || str_contains($email, $term)) {
                return true;
            }
        }

        if (preg_match('/(.)\1{8,}/u', $content) === 1) {
            return true;
        }

        return str_contains($userAgent, 'curl') || str_contains($userAgent, 'python-requests');
    }
}
