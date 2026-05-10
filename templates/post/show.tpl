{extends file='layout/base.tpl'}

{block name="content"}
<main class="app-container post-page">
    {if isset($breadcrumbs) && $breadcrumbs|@count > 0}
        <nav class="breadcrumbs" aria-label="Breadcrumb">
            {foreach $breadcrumbs as $crumb}
                {if $crumb.url}
                    <a class="breadcrumbs__link" href="{$crumb.url|escape}">{$crumb.label|escape}</a>
                {else}
                    <span class="breadcrumbs__current">{$crumb.label|escape}</span>
                {/if}
            {/foreach}
        </nav>
    {/if}

    <article class="post-article">
        <header class="post-article__header">
            <h1 class="post-article__title">{$post.title|escape}</h1>
            <p class="post-article__meta">
                {$post.date|escape} · {$post.views_count} views
            </p>
        </header>

        {if $categories|@count > 0}
            <nav class="post-categories" aria-label="Article categories">
                <span class="post-categories__label">Categories:</span>
                {foreach $categories as $cat}
                    <a class="post-categories__link" href="/category/{$cat.slug|escape}">{$cat.name|escape}</a>{if !$cat@last}, {/if}
                {/foreach}
            </nav>
        {else}
            <p class="post-page__empty">This article is not linked to any category.</p>
        {/if}

        <figure class="post-article__figure">
            <img src="{$post.image|escape}" alt="{$post.title|escape}" class="post-article__image" width="900" height="480" loading="eager">
        </figure>

        {if $post.description}
            <p class="post-article__lead">{$post.description|escape}</p>
        {/if}

        <div class="post-article__content">
            {$post.content_html nofilter}
        </div>
    </article>

    <section class="post-related" aria-labelledby="related-heading">
        <h2 class="post-related__title" id="related-heading">Related articles</h2>
        {if $relatedPosts|@count > 0}
            <div class="posts-grid">
                {foreach $relatedPosts as $rel}
                    <article class="post-card">
                        <a href="/post/{$rel.slug|escape}{if isset($fromCategorySlug) && $fromCategorySlug neq ''}?from_category={$fromCategorySlug|escape}{/if}" class="post-card__image-link">
                            <img src="{$rel.image|escape}" alt="{$rel.title|escape}" class="post-card__image" loading="lazy">
                        </a>
                        <h3 class="post-card__title">
                            <a href="/post/{$rel.slug|escape}{if isset($fromCategorySlug) && $fromCategorySlug neq ''}?from_category={$fromCategorySlug|escape}{/if}">{$rel.title|escape}</a>
                        </h3>
                        <p class="post-card__meta">{$rel.date|escape} · {$rel.views_count} views</p>
                        <p class="post-card__excerpt">{$rel.description|escape}</p>
                        <a class="post-card__read-link" href="/post/{$rel.slug|escape}{if isset($fromCategorySlug) && $fromCategorySlug neq ''}?from_category={$fromCategorySlug|escape}{/if}">Continue Reading</a>
                    </article>
                {/foreach}
            </div>
        {else}
            <p class="post-page__empty">No related articles found.</p>
        {/if}
    </section>
</main>
{/block}
