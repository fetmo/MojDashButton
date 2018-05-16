<div class="panel">
    <div class="panel--body">
        <p>
            {s name="DashButtonDeleteText"}Wollen Sie den Dash-Button "{$buttoncode}" wirklich löschen?{/s}
        </p>
        <form action="{url action=deleteButton}">
            <button type="submit" name="Submit"
                    class="register--submit btn is--primary is--large is--icon-right">
                {s name="DashButtonDelete"}Bestätigen{/s}
                <i class="icon--arrow-right"></i>
            </button>
        </form>
    </div>

</div>