{extends file='layout/base.tpl'}

{block name="content"}
<main class="app-container home-page">
    {if $sections|@count > 0}
    {foreach $sections as $section}
        <section class="category-section">
            <div class="category-section__header">
                <h2 class="category-section__title">{$section.title|escape}</h2>
                <a class="category-section__all-link" href="/category/{$section.slug|escape}">View All</a>
            </div>

            <div class="posts-grid">
                {foreach $section.posts as $post}
                    <article class="post-card">
                        <a href="/post/{$post.slug|escape}" class="post-card__image-link">
                            <img src="{$post.image|escape}" alt="{$post.title|escape}" class="post-card__image">
                        </a>
                        <h3 class="post-card__title">
                            <a href="/post/{$post.slug|escape}">{$post.title|escape}</a>
                        </h3>
                        <p class="post-card__meta">{$post.date|escape}</p>
                        <p class="post-card__excerpt">{$post.excerpt|escape}</p>
                        <a class="post-card__read-link" href="/post/{$post.slug|escape}">Continue Reading</a>
                    </article>
                {/foreach}
            </div>
        </section>
    {/foreach}
    {else}
    <p class="home-empty-state">No categories with articles yet.</p>
    {/if}
</main>
{/block}
