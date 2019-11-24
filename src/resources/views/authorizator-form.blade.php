<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Authorization</div>
                <div class="card-body">
                    <authorizator-form
                            :allowed-channels='{!! json_encode($allowedChannels) !!}'
                    >
                        You have to verify this action.
                    </authorizator-form>
                </div>
            </div>
        </div>
    </div>
</div>
