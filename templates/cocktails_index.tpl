<{include file="db:cocktails_header.tpl"}>

<section class="cocktails-hero">
    <div class="cocktails-hero-inner">
        <p class="cocktails-eyebrow"><{$smarty.const._MD_COCKTAILS_TAGLINE|default:''|escape}></p>
        <h1><{$smarty.const._MD_COCKTAILS_TITLE|default:'Cocktails'|escape}></h1>
        <p class="cocktails-hero-copy"><{$smarty.const._MD_COCKTAILS_INDEX_INTRO|default:''|escape}></p>
        <div class="cocktails-hero-actions">
            <a class="cocktails-btn cocktails-btn-primary" href="<{$cocktails_url|escape:'html'}>/index.php?op=browse"><{$smarty.const._MD_COCKTAILS_BROWSE|escape}></a>
            <{if $can_submit}><a class="cocktails-btn cocktails-btn-ghost" href="<{$cocktails_url|escape:'html'}>/recipe.php?op=edit"><{$smarty.const._MD_COCKTAILS_SUBMIT|escape}></a><{/if}>
        </div>
        <div class="cocktails-hero-stats">
            <div><strong><{$cocktails_stats.recipes}></strong><span><{$smarty.const._MD_COCKTAILS_ALL_RECIPES|escape}></span></div>
            <div><strong><{$cocktails_stats.categories}></strong><span><{$smarty.const._MD_COCKTAILS_CATEGORIES|escape}></span></div>
            <div><strong><{$cocktails_stats.ingredients}></strong><span><{$smarty.const._MD_COCKTAILS_INGREDIENTS|escape}></span></div>
        </div>
    </div>
</section>

<{if $categories|default:false}>
    <nav class="cocktails-chips">
        <{foreach item=cat from=$categories}>
            <a class="cocktails-chip" href="<{$cat.url|escape:'html'}>"<{if $cat.color}> style="border-color:<{$cat.color|escape}>"<{/if}>><{$cat.title|escape}></a>
        <{/foreach}>
    </nav>
<{/if}>

<{if $featured|default:false}>
    <section class="cocktails-section">
        <h2 class="cocktails-section-title"><{$smarty.const._MD_COCKTAILS_FEATURED|escape}></h2>
        <div class="cocktails-grid">
            <{foreach item=recipe from=$featured}><{include file="db:cocktails_card.tpl"}><{/foreach}>
        </div>
    </section>
<{/if}>

<section class="cocktails-section">
    <h2 class="cocktails-section-title"><{$smarty.const._MD_COCKTAILS_TOPRATED|escape}></h2>
    <{if $top_rated|default:false}>
        <div class="cocktails-grid">
            <{foreach item=recipe from=$top_rated}><{include file="db:cocktails_card.tpl"}><{/foreach}>
        </div>
    <{else}>
        <p class="cocktails-empty"><{$smarty.const._MD_COCKTAILS_NO_RECIPES|escape}></p>
    <{/if}>
</section>

<{if $newest|default:false}>
    <section class="cocktails-section">
        <h2 class="cocktails-section-title"><{$smarty.const._MD_COCKTAILS_NEWEST|escape}></h2>
        <div class="cocktails-grid">
            <{foreach item=recipe from=$newest}><{include file="db:cocktails_card.tpl"}><{/foreach}>
        </div>
    </section>
<{/if}>

<{include file="db:cocktails_footer.tpl"}>
