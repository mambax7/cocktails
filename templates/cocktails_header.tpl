<div class="cocktails-shell">
    <header class="cocktails-topbar">
        <a class="cocktails-brand" href="<{$cocktails_url|default:''|escape:'html'}>/index.php">
            <span class="cocktails-brand-mark">&#127864;</span>
            <span class="cocktails-brand-text"><{$smarty.const._MD_COCKTAILS_TITLE|default:'Cocktails'|escape}></span>
        </a>
        <nav class="cocktails-nav" aria-label="<{$smarty.const._MD_COCKTAILS_TITLE|default:'Cocktails'|escape}>">
            <a href="<{$cocktails_url|escape:'html'}>/index.php?op=browse"><{$smarty.const._MD_COCKTAILS_BROWSE|default:'Browse'|escape}></a>
            <a href="<{$cocktails_url|escape:'html'}>/ingredient.php"><{$smarty.const._MD_COCKTAILS_INGREDIENTS|default:'Ingredients'|escape}></a>
            <a href="<{$cocktails_url|escape:'html'}>/favorites.php"><{$smarty.const._MD_COCKTAILS_MY_FAVORITES|default:'Favorites'|escape}></a>
            <a class="cocktails-nav-cta" href="<{$cocktails_url|escape:'html'}>/recipe.php?op=edit"><{$smarty.const._MD_COCKTAILS_SUBMIT|default:'Submit'|escape}></a>
        </nav>
    </header>
