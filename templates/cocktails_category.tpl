<{include file="db:cocktails_header.tpl"}>

<section class="cocktails-section">
    <div class="cocktails-section-heading" <{if $category.color}>style="border-color:<{$category.color|escape}>"<{/if}>>
        <h1 class="cocktails-section-title"><{$category.title|escape}></h1>
        <{if $category.description|default:'' != ''}><div class="cocktails-section-desc"><{$category.description}></div><{/if}>
    </div>

    <p class="cocktails-resultcount"><{$smarty.const._MD_COCKTAILS_RESULTS|default:'%s recipes found'|sprintf:$result_count}></p>

    <{if $recipes|default:false}>
        <div class="cocktails-grid">
            <{foreach item=recipe from=$recipes}><{include file="db:cocktails_card.tpl"}><{/foreach}>
        </div>
    <{else}>
        <p class="cocktails-empty"><{$smarty.const._MD_COCKTAILS_NO_RECIPES|escape}></p>
    <{/if}>

    <{if $pagenav|default:'' != ''}><nav class="cocktails-pagination"><{$pagenav}></nav><{/if}>
</section>

<{include file="db:cocktails_footer.tpl"}>
