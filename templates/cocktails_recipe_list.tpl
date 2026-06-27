<{include file="db:cocktails_header.tpl"}>

<section class="cocktails-section">
    <form class="cocktails-filterbar" method="get" action="<{$cocktails_url|escape:'html'}>/index.php">
        <input type="hidden" name="op" value="browse">
        <input class="cocktails-filter-search" type="search" name="q" value="<{$filters.q|escape}>" placeholder="<{$smarty.const._MD_COCKTAILS_SEARCH|escape}>">
        <select name="cat">
            <option value="0"><{$smarty.const._MD_COCKTAILS_ALL_CATEGORIES|escape}></option>
            <{foreach key=cid item=ctitle from=$categories}>
                <option value="<{$cid}>"<{if $filters.cat == $cid}> selected<{/if}>><{$ctitle|escape}></option>
            <{/foreach}>
        </select>
        <select name="difficulty">
            <option value="0"><{$smarty.const._MD_COCKTAILS_ALL_DIFFICULTY|escape}></option>
            <{foreach key=did item=dlabel from=$difficulties}>
                <option value="<{$did}>"<{if $filters.difficulty == $did}> selected<{/if}>><{$dlabel|escape}></option>
            <{/foreach}>
        </select>
        <select name="sort">
            <{foreach key=skey item=slabel from=$sort_options}>
                <option value="<{$skey}>"<{if $filters.sort == $skey}> selected<{/if}>><{$slabel|escape}></option>
            <{/foreach}>
        </select>
        <button class="cocktails-btn cocktails-btn-primary" type="submit"><{$smarty.const._MD_COCKTAILS_FILTER|escape}></button>
    </form>

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
