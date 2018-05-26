{extends file='frontend/dash_center/index.tpl'}

{block name="frontend_account_index_welcome_content"}
{/block}

{block name="frontend_account_index_info"}
    <div class="product--table">
        <div class="panel has--border">
            <form action="{url action=confirm}" method="post" class="panel--body is--rounded">
                <div class="table--header block-group">
                    <div class="panel--th column--product block">{s name="DashButtonBasketOverviewHeaderOrdernumber"}Bestellnummer{/s}</div>
                    <div class="panel--th column--unit-price block">{s name="DashButtonBasketOverviewHeaderButtonCode"}Button Code{/s}</div>
                    <div class="panel--th column--quantity block">{s name="DashButtonBasketOverviewHeaderMenge"}Menge{/s}</div>
                    <div class="panel--th column--unit-price block">{s name="DashButtonBasketOverviewHeaderAktionen"}Aktionen{/s}</div>
                </div>
                {foreach $products as $key => $product}
                    <div class="table--tr row--product {if $product@last}is--last-row{/if}">
                        <input type="hidden" name="products[{$key}][id]" value="{$product['id']}">
                        <input type="hidden" name="products[{$key}][ordernumber]" value="{$product['ordernumber']}">
                        <input type="hidden" name="products[{$key}][dashproductid]" value="{$product['dashproduct_id']}">
                        <input type="hidden" name="products[{$key}][quantity]" value="{$product['quantity']}">
                        <input type="hidden" name="products[{$key}][buttonid]" value="{$product['button_id']}">
                        <div class="panel--td column--product block">
                            <p>{$product['ordernumber']}</p>
                        </div>
                        <div class="panel--td column--unit-price block">
                            <p>{$product['button_code']}</p>
                        </div>
                        <div class="panel--td column--quantity block">
                            <p>{$product['quantity']}</p>
                        </div>
                        <div class="panel--td column--unit-price block is--align-center">
                            <input type="checkbox" name="products[{$key}][checked]" value="1"
                                   title="{s name="DashButtonBasketOverviewAddProduct"}Soll dieses Product erworben werden?{/s}">
                        </div>
                    </div>
                {foreachelse}
                    <div class="table--tr row--product {if $product@last}is--last-row{/if}">
                        {s name="DashButtonBasketNoProduct"}Sie haben noch kein Produkt über einen Dash-Button in ihren Warenkorb gelegt.{/s}
                    </div>
                {/foreach}
                <div class="table--add-product add-product--form ">
                    <button type="submit" class="btn {if $products|@count == 0}is--disabled{/if}"
                            {if $products|@count == 0} disabled="disabled" {/if}>
                        {s name="DashButtonBasketNextStep"}Bestellen{/s}
                    </button>
                </div>
            </form>
        </div>
    </div>
{/block}