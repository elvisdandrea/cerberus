<!--
    DB Grid Template

    This template renders a table with the content of
    a Model DBGrid with the selected properties

    Author: Elvis D'Andrea
    E-mail: elvis.vista@gmail.com
-->
<div class="table-wrapper">
    <table id="{$id}" class="{$gridClass}">
        <thead>
        {if (!isset($showTitles) || (isset($showTitles) && $showTitles)) }
            {foreach from=$head key="field" item="data"}
                <th id="{$id}_{$data['field']}">{$data['title']}</th>
            {/foreach}
        {/if}
        </thead>
        <tbody>
        {foreach from=$content key="index" item="row"}
            <tr id="{$id}_{$index}"
                    {if ($rowAction != '' && $rowFieldId != '')}
                style="cursor: pointer;"
                onclick="quickLink('{$rowAction}?{$rowFieldId}={$row[$rowFieldId]}')"
                    {/if}>
                {foreach from=$head key="field" item="fieldparams"}
                    <td id="{$id}_{$index}_{$field}">

                        {if ($fieldparams['type'] == 'Text')}
                            {$row[$fieldparams['field']]}
                        {elseif ($fieldparams['type'] == 'Image')}
                            {if ($row[$fieldparams['field']] != '')}
                                <img width="120px" src="{$smarty.const.T_IMGURL}/{$row[$fieldparams['field']]}" />
                            {/if}
                        {elseif ($fieldparams['type'] == 'Date')}
                            {String::formatDateTimeToLoad($row[$fieldparams['field']])}
                        {elseif ($fieldparams['type'] == 'Input')}
                            <input type="text" name="{$id}_{$index}_{$field}" value="{$row[$fieldparams['field']]}">
                        {elseif ($fieldparams['type'] == 'Checkbox')}
                            <input id="{$id}_{$index}_{$field}check" name="{$id}_{$index}_{$field}" type="checkbox" {if ($row[$fieldparams['field']] == '1')}checked="checked"{/if} />
                            <label for="{$id}_{$index}_{$field}check" >{$fieldparams['field']}</label>
                        {elseif ($fieldparams['type'] == 'Select')}
                            <select name="{$id}_{$index}_{$field}">
                                {foreach from=$filedparams['listSource'] key="value" item="caption"}
                                    <option value="{$value}" {if ($value == $row[$fieldparams['field']]) }selected="selected"{/if}>{$caption}</option>
                                {/foreach}
                            </select>
                        {/if}
                        {if ($fieldparams['subtitle'])}
                            <br><small style="font-weight: normal; font-size: 11px; line-height: 10px;">
                            {$row[$fieldparams['subtitle']]}
                        </small>
                        {/if}
                    </td>
                {/foreach}
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>