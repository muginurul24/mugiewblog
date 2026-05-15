<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterSubscriptionController extends Controller
{
    public function confirm(Request $request, NewsletterSubscriber $subscriber, string $token): RedirectResponse
    {
        abort_unless(hash_equals((string) $subscriber->verification_token, $token), 403);

        $subscriber->confirm();

        return redirect()
            ->route('home')
            ->with('newsletter', 'Newsletter sudah aktif.');
    }

    public function unsubscribe(Request $request, NewsletterSubscriber $subscriber, string $token): RedirectResponse
    {
        abort_unless(hash_equals((string) $subscriber->verification_token, $token), 403);

        $subscriber->unsubscribe();

        return redirect()
            ->route('home')
            ->with('newsletter', 'Anda sudah keluar dari newsletter.');
    }
}
