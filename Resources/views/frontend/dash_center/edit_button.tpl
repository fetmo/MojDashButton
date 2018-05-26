{extends file='frontend/dash_center/index.tpl'}

{block name="frontend_account_index_welcome_content"}
{/block}

{block name="frontend_account_index_info"}
    <div class="edit--button">
        <div class="panel content block has--border is--rounded">
            <form action="{url action=saveButton}" method="post" class="panel register--form" data-add-product-position="true" data-productMode="{$button->getProductMode()}">
                <div class="panel">
                    <h2 class="panel--title is--underline block-group">
                        {s name="DashButtonEditTitle"}Dash-Button verwalten{/s}

                        <a href="{url action=removeButtonOverlay buttoncode=$button->getButtonCode()}"
                           data-modalbox="true" data-mode="ajax"
                           data-title="{s name="DashButtonEditDelete"}Dash-Button löschen?{/s}"
                           data-height="200px"
                           class="btn is--small delete--link">
                            {s name="DashButtonEditDelete"}Dash-Button löschen?{/s}
                            <i class="icon--cross"></i>
                        </a>
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
                                <label for="productmode">{s name="DashButtonEditProductModeLabel"}Multiprodukt-Modus aktiv?{/s}</label>
                                <input name="productmode" type="checkbox" id="productmode"
                                       class="checkbox" value="2"
                                       {if $button->getProductMode() === 2}checked="checked" aria-checked="true" {/if}>
                            </div>

                            <div class="dash--products">
                                {$dashproducts = $button->getProducts()}
                                {foreach $dashproducts as $dashproduct}
                                    {$index = {$dashproduct@index} + 1}
                                    <div class="panel has--border dash--product-box">
                                        <h3 class="panel--title is--underline block-group">
                                            {s name="DashButtonEditProductHeadline"}Dash Produkt Nr. #{/s}{$index}
                                            <a href="{url module=widgets controller=DashButtonProduct action=getProductRules productPosition=$dashproduct->getId()}"
                                               data-height="600px"
                                               data-modalbox="true" data-mode="ajax"
                                               data-title="{s name="DashButtonProductCongifureRules"}Dash Produkt konfigurieren{/s}"
                                               class="btn is--small configure--link">
                                                {s name="DashButtonProductCongifureRules"}Dash Produkt konfigurieren{/s}
                                                <i class="icon--pencil"></i>
                                            </a>
                                        </h3>
                                        <div class="panel--body">
                                            <input type="hidden" name="products[{$dashproduct@index}][id]"
                                                   value="{$dashproduct->getId()}">
                                            <div class="block-group panel--body">
                                                <label for="quantity{$index}">{s name="DashButtonEditQuantityLabel"}Menge{/s}</label>
                                                <input name="products[{$dashproduct@index}][quantity]" type="number"
                                                       id="quantity{$index}"
                                                       placeholder="{s name="DashButtonEditQuantityLabel"}Menge{/s}"
                                                       value="{$dashproduct->getQuantity()}">
                                            </div>
                                            <div class="block-group panel--body ordernumber--container" data-product-suggest="true"
                                                 data-searchUrl="{url module=widgets controller=DashProductSearch action=searchProduct}">
                                                <label for="ordernumber{$index}">{s name="DashButtonEditOrdernumberLabel"}Bestellnummer{/s}</label>
                                                <input name="products[{$dashproduct@index}][ordernumber]" type="text"
                                                       id="ordernumber{$index}"
                                                       placeholder="{s name="DashButtonEditOrdernumberLabel"}Bestellnummer{/s}"
                                                       value="{$dashproduct->getOrdernumber()}">
                                                <div class="suggest--container is--hidden"></div>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>

                            <script>
                                window.addtemplate =
                                    '<div class="panel has--border dash--product-box">' +
                                        '<h3 class="panel--title is--underline">{s name="DashButtonEditProductHeadline"}Dash Produkt Nr. #{/s}###INDEX###</h3>' +
                                        '<div class="panel--body">' +
                                            '<div class="block-group panel--body">' +
                                                '<label for="quantity###INDEX###">{s name="DashButtonEditQuantityLabel"}Menge{/s}</label>' +
                                                '<input name="products[###POSITION###][quantity]" type="number" id="quantity###INDEX###"' +
                                                        'placeholder="{s name="DashButtonEditQuantityLabel"}Menge{/s}"' +
                                                        'value="0">' +
                                            '</div>' +
                                            '<div class="block-group panel--body ordernumber--container" data-product-suggest="true"' +
                                                'data-searchUrl="{url module=widgets controller=DashProductSearch action=searchProduct}">' +
                                                '<label for="ordernumber###INDEX###">{s name="DashButtonEditOrdernumberLabel"}Bestellnummer{/s}</label>' +
                                                '<input name="products[###POSITION###][ordernumber]" type="text" id="ordernumber###INDEX###"' +
                                                    'placeholder="{s name="DashButtonEditOrdernumberLabel"}Bestellnummer{/s}"' +
                                                    'value="">' +
                                                '<div class="suggest--container is--hidden"></div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>';
                            </script>

                            <p>&nbsp;</p>
                        </div>
                        <div class="submit block">
                            <button type="submit" name="Submit"
                                    class="register--submit btn is--primary is--large is--icon-right">
                                {s name="DashButtonEditSave"}Speichern{/s}
                                <i class="icon--arrow-right"></i>
                            </button>

                            <span class="btn is--secondary is--large is--icon-left right add--trigger
                                  {if $button->getProductMode() !== 2 && $dashproducts|count > 0}is--hidden{/if}">
                                {s name="DashButtonProductAdd"}Produkt hinzufügen{/s}
                                <i class="icon--plus3"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <p>&nbsp;</p>
        <div class="panel content block has--border is--rounded ">
            <h2 class="panel--title is--underline">
                {s name="DashButtonBasketTitle"}Dash-Warenkorbeinträge{/s}
            </h2>
            <div class="panel--body is--wide block-group">
                <div class="table--header block-group">
                    <div class="panel--th column--one block">{s name="DashButtonBasketHeaderOrdernumber"}Bestellnummer{/s}</div>
                    <div class="panel--th column--two block">{s name="DashButtonBasketHeaderButtonCode"}Button Code{/s}</div>
                    <div class="panel--th column--three block">{s name="DashButtonBasketHeaderMenge"}Menge{/s}</div>
                </div>
                {foreach $products as $key => $product}
                    <div class="table--tr block--group {if $product@last}is--last-row{/if}">
                        <div class="panel--td column--one block">
                            <p>{$product['ordernumber']}</p>
                        </div>
                        <div class="panel--td column--two block">
                            <p>{$product['button_code']}</p>
                        </div>
                        <div class="panel--td column--three block">
                            <p>{$product['quantity']}</p>
                        </div>
                    </div>
                    {foreachelse}
                    <div class="table--tr row--product {if $product@last}is--last-row{/if}">
                        {s name="DashButtonBasketNoProduct"}Sie haben noch kein Produkt über einen Dash-Button in ihren Warenkorb gelegt.{/s}
                    </div>
                {/foreach}
            </div>
        </div>
        <p>&nbsp;</p>
        <div class="panel content block has--border is--rounded">
            <h2 class="panel--title is--underline">
                {s name="DashButtonLogTitle"}Dash-Button Log{/s}
            </h2>
            <div class="panel--body is--wide block-group">
                <table>
                    <tr>
                        <th>Datum</th>
                        <th>Type</th>
                        <th>Meldung</th>
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
    </div>
{/block}