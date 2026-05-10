<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$pageTitle|default:'Blog'|escape}</title>
    <link rel="stylesheet" href="/assets/css/common.min.css">
    {if isset($extraCss) && $extraCss|@count > 0}
        {foreach $extraCss as $href}
            <link rel="stylesheet" href="{$href|escape}">
        {/foreach}
    {/if}
    {block name="head"}{/block}
</head>
<body class="app-page {if isset($bodyClass)}{$bodyClass|escape}{/if}">
{include file='partials/header.tpl'}

{block name="content"}{/block}

{include file='partials/footer.tpl'}

{if isset($extraJs) && $extraJs|@count > 0}
    {foreach $extraJs as $src}
        <script src="{$src|escape}" defer></script>
    {/foreach}
{/if}
{block name="scripts"}{/block}
</body>
</html>
