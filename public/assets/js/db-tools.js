(function ($) {
    'use strict';

    let hideResultTimeout = null;

    function readConfig() {
        const body = document.body;

        return {
            appEnv: body.dataset.appEnv || 'prod',
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

        if (hideResultTimeout) {
            clearTimeout(hideResultTimeout);
        }

        hideResultTimeout = setTimeout(function () {
            $box.removeClass(successClass + ' ' + errorClass);
            $box.text('');
        }, 10000);
    }

    function postAction(url, csrfToken) {
        return $.ajax({
            method: 'POST',
            url: url,
            dataType: 'json',
            data: { _token: csrfToken },
        });
    }

    function buildHttpErrorMessage(xhr, appEnv) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        if (appEnv === 'dev') {
            const statusPart = xhr.status ? 'HTTP ' + xhr.status : 'network error';
            const details = xhr.responseText ? String(xhr.responseText).slice(0, 220) : '';

            return details
                ? 'Request failed (' + statusPart + '): ' + details
                : 'Request failed (' + statusPart + ').';
        }

        return 'Request failed';
    }

    function setButtonsLoadingState(isLoading, activeSelector) {
        const buttons = ['#run-migrations', '#seed-database', '#clear-database'];
        buttons.forEach(function (selector) {
            const $button = $(selector);
            if (isLoading) {
                $button.data('original-text', $button.text());
                $button.prop('disabled', true);
                if (selector === activeSelector) {
                    $button.text('Processing...');
                }
            } else {
                const originalText = $button.data('original-text');
                if (originalText) {
                    $button.text(originalText);
                }
                $button.prop('disabled', false);
            }
        });
    }

    function bindAction(selector, url, config, options) {
        $(selector).on('click', function () {
            if (options.confirmText && !window.confirm(options.confirmText)) {
                return;
            }

            setButtonsLoadingState(true, selector);

            postAction(url, config.csrfToken)
                .done(function (response) {
                    showResult(response.status, response.message);
                })
                .fail(function (xhr) {
                    showResult('error', buildHttpErrorMessage(xhr, config.appEnv));
                })
                .always(function () {
                    setButtonsLoadingState(false, selector);
                });
        });
    }

    $(function () {
        const config = readConfig();

        bindAction('#run-migrations', config.urls.migrate, config, {});
        bindAction('#seed-database', config.urls.seed, config, {});
        bindAction('#clear-database', config.urls.clear, config, {
            confirmText: 'Clear all blog data?',
        });
    });
})(jQuery);
