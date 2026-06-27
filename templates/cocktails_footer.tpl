    <footer class="cocktails-footer">
        <span><{$copyright|default:''}></span>
        <{if $xoops_isadmin}>
            <a href="<{$admin|default:''|escape:'html'}>"><{$smarty.const._MD_COCKTAILS_TITLE|default:'Admin'|escape}> &middot; Admin</a>
        <{/if}>
    </footer>
</div>
