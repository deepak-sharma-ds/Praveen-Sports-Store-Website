@php
    $seo_blog_article_schema = null;
    $seo_route = request()->route();

    if ($seo_route?->getName() === 'shop.article.view') {
        $seo_blog_slug = trim((string) $seo_route->parameter('blog_slug', ''));

        if ($seo_blog_slug !== '') {
            $seo_blog = app('Webbycrown\BlogBagisto\Repositories\BlogRepository')->findOneByField('slug', $seo_blog_slug);

            if ($seo_blog) {
                $seo_channel = core()->getCurrentChannel();
                $seo_publisher_name = trim((string) config('app.name', 'ANA Sports'));
                $seo_content = trim(strip_tags((string) ($seo_blog->content ?? '')));
                $seo_meta_description = trim((string) ($seo_blog->meta_description ?? ''));
                $seo_image_path = trim((string) ($seo_blog->src ?? ''));

                $seo_blog_article_schema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => trim((string) ($seo_blog->name ?? '')),
                    'description' => $seo_meta_description !== ''
                        ? $seo_meta_description
                        : \Illuminate\Support\Str::limit($seo_content, 160),
                    'image' => $seo_image_path !== ''
                        ? asset('storage/' . ltrim($seo_image_path, '/'))
                        : asset('images/default-blog.jpg'),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $seo_publisher_name,
                    ],
                    'publisher' => [
                        '@type' => 'Organization',
                        'name' => $seo_publisher_name,
                        'logo' => [
                            '@type' => 'ImageObject',
                            'url' => $seo_channel->logo_url
                                ?? $seo_channel->favicon_url
                                ?? asset('images/favicon.ico'),
                        ],
                    ],
                    'mainEntityOfPage' => [
                        '@type' => 'WebPage',
                        '@id' => url()->current(),
                    ],
                ];

                if ($seo_blog->published_at) {
                    // $seo_blog_article_schema['datePublished'] = optional(\Carbon\Carbon::parse($seo_blog->published_at))->toIso8601String();
                    $seo_blog_article_schema['datePublished'] = \Carbon\Carbon::parse($seo_blog->published_at)->toIso8601String();
                }

                if ($seo_blog->updated_at) {
                    // $seo_blog_article_schema['dateModified'] = optional(\Carbon\Carbon::parse($seo_blog->updated_at))->toIso8601String();
                    $seo_blog_article_schema['dateModified'] = \Carbon\Carbon::parse($seo_blog->updated_at)->toIso8601String();
                }
            }
        }
    }
@endphp

@if ($seo_blog_article_schema)
    <script type="application/ld+json">
        {!! json_encode($seo_blog_article_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endif
