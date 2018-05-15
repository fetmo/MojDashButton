{extends file='frontend/dash_center/index.tpl'}

{block name="frontend_account_index_welcome_content"}
{/block}

{block name="frontend_account_index_info"}
    <div class="panel content block has--border is--rounded">
        <form action="{url action=saveButton}" method="post" class="panel register--form">
            <div class="panel register--personal">
                <h2 class="panel--title is--underline">
                    {s name="DashButtonRegisterTitle"}Neuen Dash-Button registrieren{/s}
                </h2>
                <div class="panel--body is--wide block-group">
                    <div class="register--buttoncode block">
                        <input name="buttoncode" type="text" required="required"
                               aria-required="true" id="buttoncode"
                               placeholder="{s name="DashButtonRegisterPlaceholder"}Button Code*{/s}"
                               value="{$buttoncode}" class="register--field is--required {if $hasError}has--error{/if}">
                        <p>
                            {if $hasError}
                                {s name="DashButtonRegisterError"}Bitte prüfen Sie ihren Button-Code! Entweder ist dieser Code schon in Verwendung oder falsch eingetragen.{/s}
                            {/if}
                        </p>
                    </div>
                    <div class="submit block">
                        <button type="submit" name="Submit"
                                class="register--submit btn is--primary is--large is--icon-right">
                            {s name="DashButtonRegisterSave"}Speichern{/s}
                            <i class="icon--arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
{/block}