<{if $block.recipes|default:false}>
    <ul class="cocktails-block-list">
        <{foreach item=recipe from=$block.recipes}>
            <li class="cocktails-block-item">
                <{if $recipe.image_url|default:'' != ''}><a class="cocktails-block-thumb" href="<{$recipe.url|escape:'html'}>"><img loading="lazy" src="<{$recipe.image_url|escape:'html'}>" alt="<{$recipe.title|escape}>"></a><{/if}>
                <div class="cocktails-block-meta">
                    <a href="<{$recipe.url|escape:'html'}>"><{$recipe.title|escape}></a>
                    <span class="cocktails-block-rating"><{$recipe.rating_html}></span>
                </div>
            </li>
        <{/foreach}>
    </ul>
<{else}>
    <p class="cocktails-empty"><{$smarty.const._MB_COCKTAILS_NOITEMS|default:'No cocktails available.'|escape}></p>
<{/if}>
