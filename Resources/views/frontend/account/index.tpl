{extends file="parent:frontend/account/sidebar.tpl"}

{block name="frontend_account_menu_list"}
    {$smarty.block.parent}
    <li class="navigation--entry">
        <a href="{url module='frontend' controller='DashCenter'}" title="{s name="DashCenter"}{/s}" class="navigation--link{if {controllerName} == 'DashCenter'} is--active{/if}" rel="nofollow">
            {s name="DashCenter"}DashCenter{/s}
        </a>
    </li>
{/block}