(function ($) {
    'use strict';

    function readConfig() {
        const body = document.body;

        return {
            csrfToken: body.dataset.csrfToken || '',
            urls: {
                migrate: body.dataset.urlMigrate || '',
                seed: body.dataset.urlSeed || '',
                clear: body.dataset.urlClear || '',
            },
        };
    }

    function showResult(status, message) {
        const $box = $('#result-box');
        const successClass = 'is-success';
        const errorClass = 'is-error';

        $box.removeClass(successClass + ' ' + errorClass);
        $box.addClass(status === 'success' ? successClass : errorClass);
        $box.text(message);
    }

    function postAction(url, csrfToken) {
        return $.ajax({
            method: 'POST',
            url: url,
            dataType: 'json',
            data: { _token: csrfToken },
        });
    }

    function bindAction(selector, url, csrfToken, options) {
        $(selector).on('click', function () {
            if (options.confirmText && !window.confirm(options.confirmText)) {
                return;
            }

            postAction(url, csrfToken)
                .done(function (response) {
                    showResult(response.status, response.message);
                })
                .fail(function (xhr) {
                    const response = xhr.responseJSON || { status: 'error', message: 'Request failed' };
                    showResult(response.status, response.message);
                });
        });
    }

    $(function () {
        const config = readConfig();

        bindAction('#run-migrations', config.urls.migrate, config.csrfToken, {});
        bindAction('#seed-database', config.urls.seed, config.csrfToken, {});
        bindAction('#clear-database', config.urls.clear, config.csrfToken, {
            confirmText: 'Clear all blog data?',
        });
    });
})(jQuery);
