{extends file='frontend/dash_center/index.tpl'}

{block name="frontend_account_index_welcome_content"}
{/block}

{block name="frontend_account_index_info"}
    <div class="panel content block has--border is--rounded">
        <form action="{url action=saveButton}" method="post" class="panel register--form">
            <div class="panel edit--button">
                <h2 class="panel--title is--underline">
                    {s name="DashButtonEditTitle"}Dash-Button verwalten{/s}
                </h2>
                <div class="panel--body is--wide block-group">
                    <div class="register--buttoncode block">
                        <input type="hidden" name="buttonid" value="{$button->getId()}">
                        <input type="hidden" name="buttoncode" value="{$button->getButtonCode()}">
                        <div class="block-group panel--body">
                            <label for="buttoncode">{s name="DashButtonEditButtonCodeLabel"}Button Code{/s}</label>
                            <input name="buttoncode" type="text" id="buttoncode"
                                   disabled="disabled"
                                   readonly="readonly" aria-readonly="true"
                                   placeholder="{$button->getButtonCode()}"
                                   value="{$button->getButtonCode()}">
                        </div>
                        <div class="block-group panel--body">
                            <label for="quantity">{s name="DashButtonEditQuantityLabel"}Button Code{/s}</label>
                            <input name="quantity" type="number" id="quantity"
                                   placeholder="{s name="DashButtonEditQuantityLabel"}Menge{/s}"
                                   value="{$button->getQuantity()}">
                        </div>
                        <div class="block-group panel--body ordernumber--container">
                            <label for="ordernumber">{s name="DashButtonEditOrdernumberLabel"}Button Code{/s}</label>
                            <input name="ordernumber" type="text" id="ordernumber" data-product-suggest="true"
                                   placeholder="{s name="DashButtonEditOrdernumberLabel"}Bestellnummer{/s}"
                                   value="{$button->getOrdernumber()}">
                            <div class="suggest--container is--hidden"></div>
                        </div>
                        <p>&nbsp;</p>
                    </div>
                    <div class="submit block">
                        <button type="submit" name="Submit"
                                class="register--submit btn is--primary is--large is--icon-right">
                            {s name="DashButtonEditSave"}Speichern{/s}
                            <i class="icon--arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <p>&nbsp;</p>
    <div class="panel content block has--border is--rounded">
        <h2 class="panel--title is--underline">
            {s name="DashButtonLogTitle"}Dash-Button Log{/s}
        </h2>
        <div class="panel--body is--wide block-group">
            <table>
                <tr>
                    <th width="180px">Datum</th>
                    <th width="100px">Type</th>
                    <th width="150px">Meldung</th>
                </tr>
                {foreach $logs as $log}
                    <tr>
                        <td>{$log['log_date']}</td>
                        <td>{$log['type']}</td>
                        <td>{$log['message']}</td>
                    </tr>
                {/foreach}
            </table>
        </div>
    </div>
{/block}