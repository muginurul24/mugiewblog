{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ \App\Models\SiteSetting::current()->site_name }}</title>
        <link>{{ route('home') }}</link>
        <description>Artikel Laravel, cloud, DevOps, AI engineering, dan investasi teknologi.</description>
        <language>id-ID</language>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        @foreach ($articles as $article)
            <item>
                <title><![CDATA[{{ $article->title }}]]></title>
                <link>{{ $article->url() }}</link>
                <guid isPermaLink="true">{{ $article->url() }}</guid>
                <pubDate>{{ $article->published_at?->toRfc2822String() }}</pubDate>
                <author>{{ $article->author?->email }} ({{ $article->author?->name }})</author>
                <category><![CDATA[{{ $article->category?->name }}]]></category>
                <description><![CDATA[{!! $article->excerpt !!}]]></description>
            </item>
        @endforeach
    </channel>
</rss>
