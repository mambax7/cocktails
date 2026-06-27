<{include file="db:cocktails_header.tpl"}>

<{if $view_mode == 'recipes'}>
    <section class="cocktails-section">
        <div class="cocktails-section-heading">
            <h1 class="cocktails-section-title"><{$smarty.const._MD_COCKTAILS_USES_INGREDIENT|default:'Cocktails using %s'|sprintf:$ingredient.name}></h1>
            <{if $ingredient.description|default:'' != ''}><div class="cocktails-section-desc"><{$ingredient.description}></div><{/if}>
        </div>
        <{if $recipes|default:false}>
            <div class="cocktails-grid">
                <{foreach item=recipe from=$recipes}><{include file="db:cocktails_card.tpl"}><{/foreach}>
            </div>
        <{else}>
            <p class="cocktails-empty"><{$smarty.const._MD_COCKTAILS_NO_RECIPES|escape}></p>
        <{/if}>
    </section>
<{else}>
    <section class="cocktails-section">
        <div class="cocktails-section-heading">
            <h1 class="cocktails-section-title"><{$smarty.const._MD_COCKTAILS_BY_INGREDIENT|escape}></h1>
        </div>
        <{foreach item=group from=$ingredient_groups}>
            <div class="cocktails-ing-group">
                <h2 class="cocktails-ing-grouptitle"><{$group.label|escape}></h2>
                <div class="cocktails-ing-cloud">
                    <{foreach item=ing from=$group.items}>
                        <a class="cocktails-ing-pill" href="<{$ing.url|escape:'html'}>"><{$ing.name|escape}> <span class="cocktails-ing-count"><{$ing.count}></span></a>
                    <{/foreach}>
                </div>
            </div>
        <{/foreach}>
    </section>
<{/if}>

<{include file="db:cocktails_footer.tpl"}>
