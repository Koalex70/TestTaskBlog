<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Tools</title>
    <link rel="stylesheet" href="/assets/css/db-tools.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/assets/js/db-tools.js" defer></script>
</head>
<body
    class="db-tools-page"
    data-csrf-token="{$csrfToken|escape:'html'}"
    data-url-migrate="/db-tools/migrate"
    data-url-seed="/db-tools/seed"
    data-url-clear="/db-tools/clear"
>
<main class="db-tools-container">
<h1 class="db-tools-title">DB Tools</h1>
<p class="db-tools-env">Environment: <strong>{$appEnv|escape}</strong></p>

<div class="db-tools-actions">
    <button id="run-migrations" type="button">Run migrations</button>
    <button id="seed-database" type="button">Seed database</button>
    <button id="clear-database" type="button">Clear database</button>
</div>

<div id="result-box" class="db-tools-result"></div>
</main>
</body>
</html>
