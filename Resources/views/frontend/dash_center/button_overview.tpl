{extends file='frontend/dash_center/index.tpl'}

{block name="frontend_account_index_welcome_content"}
{/block}

{block name="frontend_account_index_info"}
    <div class="product--table">
        <div class="panel has--border">
            <div class="panel--body is--rounded">
                <div class="table--header block-group">
                    <div class="panel--th column--product block">{s name="DashButtonOverviewHeaderProdukt"}Produkt{/s}</div>
                    <div class="panel--th column--quantity block">{s name="DashButtonOverviewHeaderMenge"}Menge{/s}</div>
                    <div class="panel--th column--unit-price block">{s name="DashButtonOverviewHeaderButtonCode"}Button Code{/s}</div>
                    <div class="panel--th column--unit-price block">{s name="DashButtonOverviewHeaderAktionen"}Aktionen{/s}</div>
                </div>
                {foreach $buttons as $button}
                    {$products = $button->getProducts()}
                    <div class="table--tr row--product {if $button@last}is--last-row{/if}">
                        <div class="panel--td column--product block">
                            {foreach $products as $product}
                                <p>{s name="DashButtonOverviewTableOrdernumber"}Bestellnummer: {/s}{$product->getOrdernumber()}</p>
                            {/foreach}
                        </div>
                        <div class="panel--td column--quantity block">
                            {foreach $products as $product}
                                <p>{$product->getQuantity()}</p>
                            {/foreach}
                        </div>
                        <div class="panel--td column--unit-price block">
                            <p>{$button->getButtonCode()}</p>
                        </div>
                        <div class="panel--td column--unit-price block is--align-center">
                            <a href="{url action=editButton buttoncode=$button->getButtonCode() }"
                               class="btn is--small column--actions-link"><i class="icon--pencil"></i></a>
                        </div>
                    </div>
                {/foreach}
                <div class="table--add-product add-product--form ">
                    <a href="{url action=registerButton}" class="btn">
                        {s name="DashButtonOverviewRegister"}Neuen Button registrieren{/s}
                    </a>
                </div>
            </div>
        </div>
    </div>
{/block}