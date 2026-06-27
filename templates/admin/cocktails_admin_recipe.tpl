<{if $recipeRows > 0}>
    <div class="outer">
        <form name="select" action="recipe.php?op=" method="POST"
              onsubmit="if(window.document.select.op.value =='') {return false;} else if (window.document.select.op.value =='delete') {return deleteSubmitValid('recipe_id[]');} else if (isOneChecked('recipe_id[]')) {return true;} else {alert('<{$smarty.const._AM_COCKTAILS_SELECTED_ERROR}>'); return false;}">
            <input type="hidden" name="confirm" value="1">
            <div class="floatleft">
                <select name="op">
                    <option value=""><{$smarty.const._AM_COCKTAILS_SELECT}></option>
                    <option value="delete"><{$smarty.const._AM_COCKTAILS_SELECTED_DELETE}></option>
                </select>
                <input id="submitUp" class="formButton" type="submit" name="submitselect" value="<{$smarty.const._SUBMIT}>" title="<{$smarty.const._SUBMIT}>">
            </div>
            <div class="floatcenter0"><div id="pagenav"><{$pagenav|default:false}></div></div>

            <table class="outer" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <th align="center" width="5%"><input name="allbox" id="allbox" onclick="xoopsCheckAll('select', 'allbox');" type="checkbox" value="Check All"></th>
                    <th class="left"><{$selectorid}></th>
                    <th class="left"><{$selectorimage}></th>
                    <th class="left"><{$selectortitle}></th>
                    <th class="left"><{$selectorcid}></th>
                    <th class="left"><{$selectorrating}></th>
                    <th class="left"><{$selectoronline}></th>
                    <th class="left"><{$selectorsubmitter}></th>
                    <th class="left"><{$selectorcreated}></th>
                    <th class="left"><{$selectorupdated}></th>
                    <th class="center width5"><{$smarty.const._AM_COCKTAILS_FORM_ACTION}></th>
                </tr>
                <{foreach item=recipeArray from=$recipeArrays}>
                    <tr class="<{cycle values="odd,even"}>">
                        <td align="center" style="vertical-align:middle;"><input type="checkbox" name="recipe_id[]" value="<{$recipeArray.id}>"></td>
                        <td class='left'><{$recipeArray.id}></td>
                        <td class='left'><{$recipeArray.image}></td>
                        <td class='left'><{$recipeArray.title}></td>
                        <td class='left'><{$recipeArray.cid}></td>
                        <td class='left'><{$recipeArray.rating_avg}></td>
                        <td class='left'><{$recipeArray.online}></td>
                        <td class='left'><{$recipeArray.submitter}></td>
                        <td class='left'><{$recipeArray.created}></td>
                        <td class='left'><{$recipeArray.updated}></td>
                        <td class="center width5"><{$recipeArray.edit_delete}></td>
                    </tr>
                <{/foreach}>
            </table>
        </form>
    </div>
    <br><br>
<{else}>
    <div class="errorMsg"><{$smarty.const._AM_COCKTAILS_NO_RECORDS}></div>
<{/if}>
