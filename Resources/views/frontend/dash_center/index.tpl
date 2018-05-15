{extends file='parent:frontend/account/index.tpl'}

{block name="frontend_index_left_categories"}
    {block name="frontend_account_sidebar"}
        {include file="frontend/account/sidebar.tpl"}
    {/block}
{/block}

{block name="frontend_account_index_newsletter_settings"}
{/block}

{block name="frontend_account_index_addresses"}
{/block}

{block name="frontend_account_index_info"}
    <div class="account--info account--box panel has--border is--rounded">
        <h2 class="panel--title is--underline">{s name="DashCenterRegistration"}Dash-Button Registration{/s}</h2>
        <div class="panel--body is--wide">
            <p>
                {s name="DashCenterRegistrationText"}Hier können Sie ihren neuen Dash-Button registrieren{/s}
            </p>
        </div>
        <div class="panel--actions is--wide">
            <a href="{url action=registerButton}" title="{s name='DashCenterRegistrationTitle'}Jetzt registrieren{/s}"
               class="btn is--small">
                {s name='DashCenterRegistrationTitle'}{/s}
            </a>
        </div>
    </div>
    <div class="account--payment account--box panel has--border is--rounded">
        <h2 class="panel--title is--underline">{s name="DashCenterOverview"}Dash-Button Übersicht{/s}</h2>
        <div class="panel--body is--wide">
            <p>
                {s name="DashCenterOverviewText"}Hier können Sie ihre Dash-Button verwalten{/s}
            </p>
        </div>
        <div class="panel--actions is--wide">
            <a href="{url action=buttonOverview}" title="{s name='DashCenterOverviewTitle'}Übersicht öffnen{/s}"
               class="btn is--small">
                {s name='DashCenterOverviewTitle'}{/s}
            </a>
        </div>
    </div>
    <div class="account--info account--box panel has--border is--rounded">
        <h2 class="panel--title is--underline">{s name="DashCenterBasket"}Dash-Warenkorb{/s}</h2>
        <div class="panel--body is--wide">
            <p>
                {s name="DashCenterBasketText"}Hier können Sie ihren neuen Dash-Warenkorbeinträge einsehen und eine Bestellung erzeugen.{/s}
            </p>
        </div>
        <div class="panel--actions is--wide">
            <a href="{url controller=DashCenterBasket}" title="{s name='DashCenterBasketTitle'}Jetzt Dash-Warenkorb ansehen{/s}"
               class="btn is--small">
                {s name='DashCenterBasketTitle'}{/s}
            </a>
        </div>
    </div>
{/block}

{block name="frontend_account_index_payment_method"}
{/block}


{block name="frontend_index_body_classes"}
    {$smarty.block.parent} is--ctl-account
{/block}

{block name="frontend_account_index_welcome_content"}
    <div class="panel--body is--wide">
        <p>{s name='DashCenterHeaderInfo'}Dies ist das Dash Center zur Verwaltung von Dash-Buttons. Hier haben Sie die Möglichkeit Ihre Buttons zu registrieren und zu verwalten.{/s}</p>
    </div>
{/block}