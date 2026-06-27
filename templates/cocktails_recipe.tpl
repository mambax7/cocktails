<{include file="db:cocktails_header.tpl"}>

<article class="cocktails-detail" data-recipe-id="<{$recipe.id}>">
    <div class="cocktails-detail-head">
        <div class="cocktails-detail-media">
            <{if $recipe.image_url|default:'' != ''}>
                <img src="<{$recipe.image_url|escape:'html'}>" alt="<{$recipe.title|escape}>">
            <{else}>
                <span class="cocktails-card-noimg cocktails-card-noimg-lg">&#127864;</span>
            <{/if}>
        </div>
        <div class="cocktails-detail-intro">
            <{if $recipe.category|default:'' != ''}>
                <a class="cocktails-card-cat" href="<{$recipe.category_url|escape:'html'}>"><{$recipe.category|escape}></a>
            <{/if}>
            <h1><{$recipe.title|escape}></h1>
            <{if $recipe.summary|default:'' != ''}><p class="cocktails-detail-summary"><{$recipe.summary|escape}></p><{/if}>

            <div class="cocktails-detail-rating">
                <span class="cocktails-avg" id="cocktails-avg"><{$recipe.rating_html}></span>
                <span class="cocktails-avg-num"><strong id="cocktails-avg-num"><{$recipe.rating_avg}></strong> / 5 &middot; <span id="cocktails-rate-count"><{$recipe.rating_count}></span> <{$smarty.const._MD_COCKTAILS_AVG_RATING|escape}></span>
            </div>

            <div class="cocktails-detail-actions">
                <{if $can_rate}>
                    <div class="cocktails-rate" data-recipe-id="<{$recipe.id}>" data-rate-url="<{$rate_url|escape:'html'}>" data-user-stars="<{$recipe.user_stars}>" role="radiogroup" aria-label="<{$smarty.const._MD_COCKTAILS_RATE|escape}>">
                        <span class="cocktails-rate-label"><{$smarty.const._MD_COCKTAILS_RATE|escape}>:</span>
                        <button type="button" class="cocktails-rate-star" data-stars="1">&#9733;</button>
                        <button type="button" class="cocktails-rate-star" data-stars="2">&#9733;</button>
                        <button type="button" class="cocktails-rate-star" data-stars="3">&#9733;</button>
                        <button type="button" class="cocktails-rate-star" data-stars="4">&#9733;</button>
                        <button type="button" class="cocktails-rate-star" data-stars="5">&#9733;</button>
                    </div>
                <{/if}>
                <{if $is_logged_in}>
                    <button type="button" class="cocktails-fav<{if $recipe.is_favorite}> is-active<{/if}>" data-recipe-id="<{$recipe.id}>" data-fav-url="<{$cocktails_url|escape:'html'}>/recipe.php" data-label-add="<{$smarty.const._MD_COCKTAILS_FAVORITE_ADD|escape}>" data-label-remove="<{$smarty.const._MD_COCKTAILS_FAVORITE_REMOVE|escape}>">
                        <span class="cocktails-fav-icon">&#10084;</span>
                        <span class="cocktails-fav-label"><{if $recipe.is_favorite}><{$smarty.const._MD_COCKTAILS_FAVORITE_REMOVE|escape}><{else}><{$smarty.const._MD_COCKTAILS_FAVORITE_ADD|escape}><{/if}></span>
                    </button>
                <{/if}>
                <button type="button" class="cocktails-btn cocktails-btn-ghost" onclick="window.print();return false;"><{$smarty.const._MD_COCKTAILS_PRINT|escape}></button>
                <{if $recipe.can_edit}>
                    <a class="cocktails-btn cocktails-btn-ghost" href="<{$cocktails_url|escape:'html'}>/recipe.php?op=edit&amp;id=<{$recipe.id}>"><{$smarty.const._MD_COCKTAILS_EDIT|escape}></a>
                <{/if}>
            </div>
            <{$security_token}>
        </div>
    </div>

    <div class="cocktails-detail-meta">
        <{if $recipe.glass|default:'' != ''}><div class="cocktails-meta-box"><span class="cocktails-meta-k"><{$smarty.const._MD_COCKTAILS_GLASS|escape}></span><span class="cocktails-meta-v"><{$recipe.glass|escape}></span></div><{/if}>
        <div class="cocktails-meta-box"><span class="cocktails-meta-k"><{$smarty.const._MD_COCKTAILS_DIFFICULTY|escape}></span><span class="cocktails-meta-v"><span class="cocktails-badge <{$recipe.difficulty_class|escape}>"><{$recipe.difficulty_label|escape}></span></span></div>
        <{if $recipe.prep_time|default:0 > 0}><div class="cocktails-meta-box"><span class="cocktails-meta-k"><{$smarty.const._MD_COCKTAILS_PREPTIME|escape}></span><span class="cocktails-meta-v"><{$smarty.const._MD_COCKTAILS_MINUTES|default:'%s min'|sprintf:$recipe.prep_time}></span></div><{/if}>
        <div class="cocktails-meta-box"><span class="cocktails-meta-k"><{$smarty.const._MD_COCKTAILS_SERVINGS|escape}></span><span class="cocktails-meta-v"><{$recipe.servings}></span></div>
        <{if $recipe.is_alcoholic == 0}><div class="cocktails-meta-box"><span class="cocktails-badge cocktails-badge-na"><{$smarty.const._MD_COCKTAILS_NONALCOHOLIC|escape}></span></div><{/if}>
    </div>

    <div class="cocktails-detail-body">
        <section class="cocktails-ingredients">
            <h2><{$smarty.const._MD_COCKTAILS_INGREDIENTS|escape}></h2>
            <{if $recipe.ingredients|default:false}>
                <ul class="cocktails-ing-list" data-base-servings="<{$recipe.servings}>">
                    <{foreach item=line from=$recipe.ingredients}>
                        <li<{if $line.is_optional}> class="is-optional"<{/if}>>
                            <a href="<{$line.ingredient_url|escape:'html'}>"><{$line.text|escape}></a>
                            <{if $line.is_optional}> <em><{$smarty.const._MD_COCKTAILS_OPTIONAL|escape}></em><{/if}>
                        </li>
                    <{/foreach}>
                </ul>
            <{/if}>
            <{if $recipe.garnish|default:'' != ''}>
                <p class="cocktails-garnish"><strong><{$smarty.const._MD_COCKTAILS_GARNISH|escape}>:</strong> <{$recipe.garnish|escape}></p>
            <{/if}>
        </section>

        <section class="cocktails-method">
            <h2><{$smarty.const._MD_COCKTAILS_METHOD|escape}></h2>
            <div class="cocktails-method-body"><{$recipe.method}></div>
        </section>
    </div>

    <{if $recipe.tags|default:false}>
        <div class="cocktails-detail-tags">
            <{foreach item=tag from=$recipe.tags}>
                <a class="cocktails-tag" href="<{$tag.url|escape:'html'}>">#<{$tag.name|escape}></a>
            <{/foreach}>
        </div>
    <{/if}>

    <footer class="cocktails-detail-foot">
        <{if $recipe.submitter|default:'' != ''}><span><{$smarty.const._MD_COCKTAILS_BY|default:'by %s'|sprintf:$recipe.submitter}></span><{/if}>
        <span><{$recipe.created|escape}></span>
        <span><{$smarty.const._MD_COCKTAILS_VIEWS|default:'%s views'|sprintf:$recipe.views}></span>
    </footer>
</article>

<{include file="db:cocktails_footer.tpl"}>
