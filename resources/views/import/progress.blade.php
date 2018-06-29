@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Import Progress</div>

                <div class="panel-body">

                    <div class="progress">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="col-md-12 import-msg-container hidden">
                        <h3>Import process completed</h3>
                        <div id="message">
                            <p class="success"><span></span>&nbsp; Records imported.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(function() {
        var loop = setInterval(function() {
            $.ajax({
                url: '{{ route('ladybirdweb.import.ajax.progress', $id) }}'
            })
            .done(function(response) {
                $('.progress-bar').width(response.progress + '%');
                $('.progress-bar').attr('aria-valuenow', response.progress);
                $('.progress-bar').html(response.progress + '%');

                if ( response.progress == 100 ) {
                    clearInterval( loop );
                    $('#message .success span').html(response.imported);
                    $('.import-msg-container').removeClass('hidden');
                }
            });
        }, 1000);
    });
</script>
@endsection
