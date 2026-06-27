<{* Reusable recipe card. Expects a $recipe row shaped by cocktails_recipe_card(). *}>
<article class="cocktails-card">
    <a class="cocktails-card-media" href="<{$recipe.url|escape:'html'}>">
        <{if $recipe.image_url|default:'' != ''}>
            <img loading="lazy" src="<{$recipe.image_url|escape:'html'}>" alt="<{$recipe.title|escape}>">
        <{else}>
            <span class="cocktails-card-noimg">&#127864;</span>
        <{/if}>
        <{if $recipe.featured|default:0}><span class="cocktails-chip cocktails-chip-featured"><{$smarty.const._MD_COCKTAILS_FEATURED|default:'Featured'|escape}></span><{/if}>
        <{if $recipe.is_favorite|default:0}><span class="cocktails-chip cocktails-chip-fav" title="&#9733;">&#10084;</span><{/if}>
    </a>
    <div class="cocktails-card-body">
        <{if $recipe.category|default:'' != ''}><span class="cocktails-card-cat"><{$recipe.category|escape}></span><{/if}>
        <h3 class="cocktails-card-title"><a href="<{$recipe.url|escape:'html'}>"><{$recipe.title|escape}></a></h3>
        <div class="cocktails-card-rating"><{$recipe.rating_html}> <span class="cocktails-card-rcount">(<{$recipe.rating_count}>)</span></div>
        <{if $recipe.summary|default:'' != ''}><p class="cocktails-card-summary"><{$recipe.summary|escape}></p><{/if}>
        <div class="cocktails-card-meta">
            <span class="cocktails-badge <{$recipe.difficulty_class|escape}>"><{$recipe.difficulty_label|escape}></span>
            <{if $recipe.prep_time|default:0 > 0}><span class="cocktails-meta-item">&#9201; <{$recipe.prep_time}>'</span><{/if}>
            <{if $recipe.is_alcoholic|default:1 == 0}><span class="cocktails-badge cocktails-badge-na"><{$smarty.const._MD_COCKTAILS_NONALCOHOLIC|default:'0%'|escape}></span><{/if}>
        </div>
    </div>
</article>
