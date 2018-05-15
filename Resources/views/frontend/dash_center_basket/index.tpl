{extends file='parent:frontend/account/index.tpl'}

{block name="frontend_index_left_categories"}
    {block name="frontend_account_sidebar"}
        {include file="frontend/account/sidebar.tpl"}
    {/block}
{/block}

{block name="frontend_account_index_newsletter_settings"}
{/block}

{block name="frontend_account_index_info"}
    <div class="account--info account--box panel has--border is--rounded">
        <h2 class="panel--title is--underline">{s name="DashCenterBasketHeadline"}Dash-Warenkorb{/s}</h2>
        <div class="panel--body is--wide">
            <p>
                {s name="DashCenterBasketText1"}Hier können Sie ihren neuen Dash-Warenkorbeinträge einsehen und eine Bestellung erzeugen.{/s}
            </p>
        </div>
        <div class="panel--actions is--wide">
            <a href="{url action=overview}" title="{s name='DashCenterBasketTitle'}Jetzt Dash-Warenkorb ansehen{/s}"
               class="btn is--small">
                {s name='DashCenterBasketTitle'}{/s}
            </a>
        </div>
    </div>
{/block}


{block name="frontend_index_body_classes"}
    {$smarty.block.parent} is--ctl-account
{/block}

{block name="frontend_account_index_welcome_content"}
    <div class="panel--body is--wide">
        <p>{s name='DashCenterBasketHeaderInfo'}Dies ist das Dash-Warenkorb Bereich. Hier können Sie ihre Rechnungs- und Lieferadresse verwalten, sowie die Zahlart für Dash-Bestellungen.{/s}</p>
    </div>
{/block}