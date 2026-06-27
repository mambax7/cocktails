<{include file="db:cocktails_header.tpl"}>

<section class="cocktails-section">
    <div class="cocktails-section-heading">
        <h1 class="cocktails-section-title"><{$smarty.const._MD_COCKTAILS_MY_FAVORITES|escape}></h1>
    </div>
    <{if $recipes|default:false}>
        <div class="cocktails-grid">
            <{foreach item=recipe from=$recipes}><{include file="db:cocktails_card.tpl"}><{/foreach}>
        </div>
    <{else}>
        <p class="cocktails-empty"><{$smarty.const._MD_COCKTAILS_NO_FAVORITES|escape}></p>
    <{/if}>
</section>

<{include file="db:cocktails_footer.tpl"}>
