<div class="alert {$notification.type} alert-success alert-dismissable">
    {if $notification.refresh >= 1}
        <meta http-equiv="Refresh" content="{$notification.refresh}; url={$notification.url}">
    {/if}
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    {$notification.msg}
</div>