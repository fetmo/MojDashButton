{extends file='frontend/dash_center/index.tpl'}

{block name="frontend_account_index_welcome_content"}
    {if $basketValid}
        {include file="frontend/_includes/messages.tpl" type="success" content="Der Warenkorb konnte erfolgreich berechnet werden."}
    {else}
        {include file="frontend/_includes/messages.tpl" type="error" content="Der Warenkorb konnte nicht erfolgreich berechnet werden. Es wurden Korrekturen vorgenommen!"}
    {/if}
{/block}

{block name="frontend_account_index_info"}
    <div class="product--table">
        <div class="panel has--border">
            <form action="{url action=finishOrder}" method="post" class="panel--body is--rounded">
                <div class="table--header block-group">
                    <div class="panel--th column--product block">{s name="DashButtonBasketOverviewHeaderOrdernumber"}Bestellnummer{/s}</div>
                    <div class="panel--th column--unit-price block">{s name="DashButtonBasketOverviewHeaderUnitPrice"}Preis{/s}</div>
                    <div class="panel--th column--quantity block">{s name="DashButtonBasketOverviewHeaderMenge"}Menge{/s}</div>
                    <div class="panel--th column--total-price block">{s name="DashButtonBasketOverviewHeaderTotal"}Total{/s}</div>
                </div>
                {foreach $basket as $key => $product}
                    <div class="table--tr row--product {if $product@last}is--last-row{/if}">
                        <input type="hidden" name="products[{$key}][ordernumber]" value="{$product['ordernumber']}">
                        <input type="hidden" name="products[{$key}][dashproductid]" value="{$product['dashproductid']}">
                        <input type="hidden" name="products[{$key}][id]" value="{$product['id']}">
                        <div class="panel--td column--product block">
                            <p>{$product['ordernumber']}</p>
                            {if $product['validation']}
                                <ul style="padding-left:30px; color: red;">
                                    {foreach $product['validation'] as $errors}
                                        <li class="is--red">{$errors.message}</li>
                                    {/foreach}
                                </ul>
                            {/if}
                        </div>
                        <div class="panel--td column--unit-price block">
                            <p>{$product['price_numeric']|currency}</p>
                        </div>
                        <div class="panel--td column--quantity block">
                            <input type="number" value="{$product['purchase_quantity']}" style="width: 100%"
                                   min="{$product['minpurchase']}" max="{$product['maxpurchase']}"
                                   name="products[{$key}][quantity]">
                        </div>
                        <div class="panel--td column--total-price block is--align-center">
                            <p>{$product['total_numeric']|currency}</p>
                        </div>
                    </div>
                {/foreach}
                <div class="table--add-product add-product--form ">
                    <button type="submit" class="btn {if $basket|@count == 0}is--disabled{/if}"
                            {if $basket|@count == 0} disabled="disabled" {/if}>
                        {s name="DashButtonBasketOrder"}Bestellen{/s}
                    </button>
                </div>
            </form>
        </div>
    </div>
{/block}