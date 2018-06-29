@extends('layouts.app')

@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">Import Data</div>
                    <div class="panel-body">
                        <div class="col-md-12" id=uploadContainer>
                            <div>
                                <div class="alert alert-danger hidden">
                                    <div class="alert-container"></div>
                                </div>

                                <div class="message"></div>

                                <div class="progress hidden">
                                    <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div>
                                <a id="backButton" class="btn btn-danger hidden" href="{{ url()->previous() }}">Back</a>
                                <button id="fileInput" class="btn btn-default">Select File</button>
                                <button id="importButton" class="btn btn-primary pull-right hidden">Import</button>
                                <a id="nextButton" class="btn btn-primary pull-right hidden">Next</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')                        
<script type="text/javascript" src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/moxie.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/jquery.plupload.queue/jquery.plupload.queue.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/ladybirdweb/import-export/js/importer.js') }}"></script>
<script>
    $(function() {
        importer( {
            url: '{{ route( $route ) }}',
            chunkSize: '50kb',
        } );
    });
</script>
@endsection
