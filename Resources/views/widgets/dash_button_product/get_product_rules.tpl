<div class="panel">
    <form action="{url action=saveProductRules module=widgets controller=DashButtonProduct}" method="post">
        <input type="hidden" name="productid" value="{$position->getId()}">
        <h2 class="panel--title is--underline block-group is--align-center">
            <span class="left">Regel</span> - <span class="right">Konfiguration</span>
        </h2>
        <div class="is--wide block-group panel--body">
            {foreach $rules as $identifier => $ruleClass}
                <div class="block-group panel--body" style="width: 100%;">
                    <label for="{$identifier}" class="left">{s name="DashButtonProductLabel$identifier"}{$identifier}{/s}</label>
                    <input name="rule[{$identifier}]" type="text" id="{$identifier}"
                           value="{$configuredRules[{$identifier}].config}" class="right">
                </div>
            {/foreach}
            <div class="submit block">
                <button type="submit" name="Submit"
                        class="register--submit btn is--primary is--large is--icon-right">
                    {s name="DashButtonProductSave"}Speichern{/s}
                    <i class="icon--arrow-right"></i>
                </button>
            </div>
        </div>
    </form>
</div>