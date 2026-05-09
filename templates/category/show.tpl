{extends file='layout/base.tpl'}

{block name="content"}
<main
    class="app-container category-page"
    id="category-page"
    data-category-slug="{$category.slug|escape}"
    data-initial-sort="{$sort|escape}"
    data-initial-page="{$currentPage|escape}"
    data-per-page="{$perPage|escape}"
>
    <section class="category-page__intro">
        <h1 class="category-page__title">{$category.name|escape}</h1>
        <p class="category-page__description">
            {if $category.description}
                {$category.description|escape}
            {else}
                No category description provided.
            {/if}
        </p>
    </section>

    <section class="category-page__toolbar">
        <div class="category-sort" id="category-sort">
            <span class="category-sort__label">Sort:</span>
            <a
                class="category-sort__link {if $sort === 'date_desc'}is-active{/if}"
                data-sort-link="date_desc"
                href="/category/{$category.slug|escape}?sort=date_desc&page=1"
            >
                Newest
            </a>
            <a
                class="category-sort__link {if $sort === 'views_desc'}is-active{/if}"
                data-sort-link="views_desc"
                href="/category/{$category.slug|escape}?sort=views_desc&page=1"
            >
                Most viewed
            </a>
        </div>
        <p class="category-page__count" id="category-count">{$totalItems} articles</p>
    </section>

    {if $posts|@count > 0}
        <div class="posts-grid" id="category-posts-grid">
            {foreach $posts as $post}
                <article class="post-card">
                    <a href="/post/{$post.slug|escape}" class="post-card__image-link">
                        <img src="{$post.image|escape}" alt="{$post.title|escape}" class="post-card__image">
                    </a>
                    <h3 class="post-card__title">
                        <a href="/post/{$post.slug|escape}">{$post.title|escape}</a>
                    </h3>
                    <p class="post-card__meta">
                        {$post.date|escape} · {$post.views_count} views
                    </p>
                    <p class="post-card__excerpt">{$post.description|escape}</p>
                    <a class="post-card__read-link" href="/post/{$post.slug|escape}">Continue Reading</a>
                </article>
            {/foreach}
        </div>
    {else}
        <p class="category-page__empty" id="category-empty">No posts in this category yet.</p>
    {/if}

    {if $totalPages > 1}
        <nav class="pagination" aria-label="Category pagination" id="category-pagination">
            {if $hasPrev}
                <a class="pagination__link" data-page-link="{$currentPage - 1}" href="/category/{$category.slug|escape}?{$prevQuery|escape}">Prev</a>
            {else}
                <span class="pagination__link is-disabled">Prev</span>
            {/if}

            {foreach $pageNumbers as $pageNumber}
                {if $pageNumber === $currentPage}
                    <span class="pagination__link is-active">{$pageNumber}</span>
                {else}
                    <a class="pagination__link" data-page-link="{$pageNumber}" href="/category/{$category.slug|escape}?sort={$sort|escape}&page={$pageNumber}">{$pageNumber}</a>
                {/if}
            {/foreach}

            {if $hasNext}
                <a class="pagination__link" data-page-link="{$currentPage + 1}" href="/category/{$category.slug|escape}?{$nextQuery|escape}">Next</a>
            {else}
                <span class="pagination__link is-disabled">Next</span>
            {/if}
        </nav>
    {/if}
</main>
{/block}
