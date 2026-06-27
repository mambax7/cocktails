<{if $ingredientRows > 0}>
    <div class="outer">
        <form name="select" action="ingredient.php?op=" method="POST"
              onsubmit="if(window.document.select.op.value =='') {return false;} else if (window.document.select.op.value =='delete') {return deleteSubmitValid('ingredient_id[]');} else if (isOneChecked('ingredient_id[]')) {return true;} else {alert('<{$smarty.const._AM_COCKTAILS_SELECTED_ERROR}>'); return false;}">
            <input type="hidden" name="confirm" value="1">
            <div class="floatleft">
                <select name="op">
                    <option value=""><{$smarty.const._AM_COCKTAILS_SELECT}></option>
                    <option value="delete"><{$smarty.const._AM_COCKTAILS_SELECTED_DELETE}></option>
                </select>
                <input class="formButton" type="submit" name="submitselect" value="<{$smarty.const._SUBMIT}>">
            </div>
            <div class="floatcenter0"><div id="pagenav"><{$pagenav|default:false}></div></div>

            <table class="outer" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <th align="center" width="5%"><input name="allbox" id="allbox" onclick="xoopsCheckAll('select', 'allbox');" type="checkbox" value="Check All"></th>
                    <th class="left"><{$selectorid}></th>
                    <th class="left"><{$selectorname}></th>
                    <th class="left"><{$selectortype}></th>
                    <th class="left"><{$selectorabv}></th>
                    <th class="left"><{$selectordescription}></th>
                    <th class="left"><{$selectorweight}></th>
                    <th class="left"><{$selectoronline}></th>
                    <th class="center width5"><{$smarty.const._AM_COCKTAILS_FORM_ACTION}></th>
                </tr>
                <{foreach item=ingredientArray from=$ingredientArrays}>
                    <tr class="<{cycle values="odd,even"}>">
                        <td align="center" style="vertical-align:middle;"><input type="checkbox" name="ingredient_id[]" value="<{$ingredientArray.id}>"></td>
                        <td class='left'><{$ingredientArray.id}></td>
                        <td class='left'><{$ingredientArray.name}></td>
                        <td class='left'><{$ingredientArray.type}></td>
                        <td class='left'><{$ingredientArray.abv}></td>
                        <td class='left'><{$ingredientArray.description}></td>
                        <td class='left'><{$ingredientArray.weight}></td>
                        <td class='left'><{$ingredientArray.online}></td>
                        <td class="center width5"><{$ingredientArray.edit_delete}></td>
                    </tr>
                <{/foreach}>
            </table>
        </form>
    </div>
    <br><br>
<{else}>
    <div class="errorMsg"><{$smarty.const._AM_COCKTAILS_NO_RECORDS}></div>
<{/if}>
