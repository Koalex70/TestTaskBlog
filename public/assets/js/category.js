(function () {
    'use strict';

    const pageRoot = document.getElementById('category-page');
    if (!pageRoot) {
        return;
    }

    const categorySlug = pageRoot.dataset.categorySlug || '';
    const postsGrid = document.getElementById('category-posts-grid');
    const sortContainer = document.getElementById('category-sort');
    const countNode = document.getElementById('category-count');

    let paginationNode = document.getElementById('category-pagination');
    let emptyNode = document.getElementById('category-empty');
    let currentRequestController = null;
    let currentSort = pageRoot.dataset.initialSort || 'date_desc';
    let currentPage = parseInt(pageRoot.dataset.initialPage || '1', 10);
    const perPage = Math.max(1, parseInt(pageRoot.dataset.perPage || '10', 10));

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function buildPostCard(post) {
        const postHref = '/post/' + escapeHtml(post.slug) + '?from_category=' + escapeHtml(categorySlug);
        return (
            '<article class="post-card">' +
            '<a href="' + postHref + '" class="post-card__image-link">' +
            '<img src="' + escapeHtml(post.image) + '" alt="' + escapeHtml(post.title) + '" class="post-card__image">' +
            '</a>' +
            '<h3 class="post-card__title"><a href="' + postHref + '">' + escapeHtml(post.title) + '</a></h3>' +
            '<p class="post-card__meta">' + escapeHtml(post.date) + ' · ' + post.views_count + ' views</p>' +
            '<p class="post-card__excerpt">' + escapeHtml(post.description || '') + '</p>' +
            '<a class="post-card__read-link" href="' + postHref + '">Continue Reading</a>' +
            '</article>'
        );
    }

    function buildPagination(data) {
        if (data.totalPages <= 1) {
            return '';
        }

        let html = '<nav class="pagination" aria-label="Category pagination" id="category-pagination">';

        if (data.hasPrev) {
            html += '<a class="pagination__link" data-page-link="' + (data.currentPage - 1) + '" href="/category/' + escapeHtml(categorySlug) + '?sort=' + escapeHtml(data.sort) + '&page=' + (data.currentPage - 1) + '">Prev</a>';
        } else {
            html += '<span class="pagination__link is-disabled">Prev</span>';
        }

        data.pageNumbers.forEach(function (pageNumber) {
            if (pageNumber === data.currentPage) {
                html += '<span class="pagination__link is-active">' + pageNumber + '</span>';
            } else {
                html += '<a class="pagination__link" data-page-link="' + pageNumber + '" href="/category/' + escapeHtml(categorySlug) + '?sort=' + escapeHtml(data.sort) + '&page=' + pageNumber + '">' + pageNumber + '</a>';
            }
        });

        if (data.hasNext) {
            html += '<a class="pagination__link" data-page-link="' + (data.currentPage + 1) + '" href="/category/' + escapeHtml(categorySlug) + '?sort=' + escapeHtml(data.sort) + '&page=' + (data.currentPage + 1) + '">Next</a>';
        } else {
            html += '<span class="pagination__link is-disabled">Next</span>';
        }

        html += '</nav>';
        return html;
    }

    function updateSortActive(sort) {
        if (!sortContainer) {
            return;
        }

        const sortLinks = sortContainer.querySelectorAll('[data-sort-link]');
        sortLinks.forEach(function (link) {
            if (link.dataset.sortLink === sort) {
                link.classList.add('is-active');
            } else {
                link.classList.remove('is-active');
            }
        });
    }

    function renderSkeleton() {
        if (!postsGrid) {
            return;
        }

        let html = '';
        for (let i = 0; i < perPage; i += 1) {
            html += (
                '<article class="post-card post-card--skeleton">' +
                '<div class="post-card__image post-card__skeleton-block"></div>' +
                '<div class="post-card__skeleton-line post-card__skeleton-line--title"></div>' +
                '<div class="post-card__skeleton-line post-card__skeleton-line--meta"></div>' +
                '<div class="post-card__skeleton-line post-card__skeleton-line--text"></div>' +
                '<div class="post-card__skeleton-line post-card__skeleton-line--text"></div>' +
                '</article>'
            );
        }

        postsGrid.innerHTML = html;
    }

    function renderData(data) {
        if (countNode) {
            countNode.textContent = data.totalItems + ' articles';
        }

        if (postsGrid) {
            if (Array.isArray(data.posts) && data.posts.length > 0) {
                postsGrid.innerHTML = data.posts.map(buildPostCard).join('');
                if (emptyNode) {
                    emptyNode.remove();
                    emptyNode = null;
                }
            } else {
                postsGrid.innerHTML = '';
                if (!emptyNode) {
                    emptyNode = document.createElement('p');
                    emptyNode.id = 'category-empty';
                    emptyNode.className = 'category-page__empty';
                    emptyNode.textContent = 'No posts in this category yet.';
                    postsGrid.insertAdjacentElement('afterend', emptyNode);
                }
            }
        }

        const newPaginationHtml = buildPagination(data);
        if (paginationNode) {
            if (newPaginationHtml === '') {
                paginationNode.remove();
                paginationNode = null;
            } else {
                paginationNode.outerHTML = newPaginationHtml;
                paginationNode = document.getElementById('category-pagination');
            }
        } else if (newPaginationHtml !== '') {
            pageRoot.insertAdjacentHTML('beforeend', newPaginationHtml);
            paginationNode = document.getElementById('category-pagination');
        }

        updateSortActive(data.sort);
        currentSort = data.sort;
        currentPage = data.currentPage;
    }

    function updateUrl(sort, page) {
        const query = new URLSearchParams({
            sort: sort,
            page: String(page),
        });

        window.history.pushState(
            { sort: sort, page: page },
            '',
            '/category/' + categorySlug + '?' + query.toString()
        );
    }

    function loadPage(sort, page, shouldPushState) {
        if (currentRequestController) {
            currentRequestController.abort();
        }

        currentRequestController = new AbortController();
        renderSkeleton();

        const requestUrl = '/api/category/' + encodeURIComponent(categorySlug) + '?sort=' + encodeURIComponent(sort) + '&page=' + encodeURIComponent(String(page));
        fetch(requestUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
            signal: currentRequestController.signal,
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Request failed');
                }
                return response.json();
            })
            .then(function (payload) {
                if (!payload || payload.status !== 'success' || !payload.data) {
                    throw new Error('Invalid response');
                }

                renderData(payload.data);
                if (shouldPushState) {
                    updateUrl(payload.data.sort, payload.data.currentPage);
                }
                pageRoot.scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(function (error) {
                if (error && error.name === 'AbortError') {
                    return;
                }

                window.alert('Unable to load category page data. Please try again.');
            });
    }

    if (sortContainer) {
        sortContainer.addEventListener('click', function (event) {
            const target = event.target;
            if (!(target instanceof HTMLElement)) {
                return;
            }

            const link = target.closest('[data-sort-link]');
            if (!(link instanceof HTMLAnchorElement)) {
                return;
            }

            event.preventDefault();
            const sort = link.dataset.sortLink || 'date_desc';
            loadPage(sort, 1, true);
        });
    }

    pageRoot.addEventListener('click', function (event) {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const link = target.closest('[data-page-link]');
        if (!(link instanceof HTMLAnchorElement)) {
            return;
        }

        event.preventDefault();
        const page = parseInt(link.dataset.pageLink || '1', 10);
        if (Number.isNaN(page) || page < 1) {
            return;
        }

        loadPage(currentSort, page, true);
    });

    window.addEventListener('popstate', function () {
        const query = new URLSearchParams(window.location.search);
        const sort = query.get('sort') || 'date_desc';
        const page = parseInt(query.get('page') || '1', 10);
        loadPage(sort, Number.isNaN(page) ? 1 : Math.max(1, page), false);
    });
})();
