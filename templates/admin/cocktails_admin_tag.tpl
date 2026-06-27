<{if $tagRows > 0}>
    <div class="outer">
        <div class="floatcenter0"><div id="pagenav"><{$pagenav|default:false}></div></div>
        <table class="outer" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <th class="left"><{$selectorid}></th>
                <th class="left"><{$selectorname}></th>
                <th class="left"><{$selectoruses}></th>
                <th class="center width5"><{$smarty.const._AM_COCKTAILS_FORM_ACTION}></th>
            </tr>
            <{foreach item=tagArray from=$tagArrays}>
                <tr class="<{cycle values="odd,even"}>">
                    <td class='left'><{$tagArray.id}></td>
                    <td class='left'><{$tagArray.name}></td>
                    <td class='left'><{$tagArray.uses}></td>
                    <td class="center width5"><{$tagArray.edit_delete}></td>
                </tr>
            <{/foreach}>
        </table>
    </div>
    <br><br>
<{else}>
    <div class="errorMsg"><{$smarty.const._AM_COCKTAILS_NO_RECORDS}></div>
<{/if}>
