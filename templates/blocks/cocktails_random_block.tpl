<{if $block.recipes|default:false}>
    <{foreach item=recipe from=$block.recipes}>
        <div class="cocktails-block-random">
            <{if $recipe.image_url|default:'' != ''}><a href="<{$recipe.url|escape:'html'}>"><img class="cocktails-block-random-img" loading="lazy" src="<{$recipe.image_url|escape:'html'}>" alt="<{$recipe.title|escape}>"></a><{/if}>
            <h4 class="cocktails-block-random-title"><a href="<{$recipe.url|escape:'html'}>"><{$recipe.title|escape}></a></h4>
            <div class="cocktails-block-rating"><{$recipe.rating_html}></div>
        </div>
    <{/foreach}>
<{else}>
    <p class="cocktails-empty"><{$smarty.const._MB_COCKTAILS_NOITEMS|default:'No cocktails available.'|escape}></p>
<{/if}>
