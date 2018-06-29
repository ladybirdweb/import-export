@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 col-md-offset">
            <div class="panel panel-default">
                <div class="panel-heading">Map Data With DB Columns</div>

                <div class="panel-body">
                    <div class="col-md-12">
                        <p>Import process will skip the csv header row highlighted in red background.</p>
                    </div>

                    <form action="{{ route($route, $id) }}" method="POST">
                        {{ csrf_field() }}

                        @if ($errors->has('db_column'))
                            <span class="help-block">
                                <strong>{{ $errors->first('db_column') }}</strong>
                            </span>
                        @endif

                        @if ($errors->has('db_column.*'))
                            <span class="help-block">
                                <strong>{{ $errors->first('db_column.*') }}</strong>
                            </span>
                        @endif

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    @foreach ( array_first( $csv_data ) as $csv_header )
                                    <th>
                                        <div class="form-group">
                                            <select class="form-control" id="{{ 'dbCol-' . $loop->index }}" name="db_column[]"@if ( ! in_array( strtolower( $csv_header ), $db_columns) ) {{ ' disabled' }} @endif>
                                                <option value="">Select</option>
                                                @foreach ( $db_columns as $column )
                                                    <option value="{{ $column }}"@if ( strtolower( $csv_header ) == $column ) {{ ' selected ' }} @endif>{{ strtoupper( $column ) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="checkbox-inline"><input type="checkbox" class="ignore-col" name="ignore_col[]" value="{{ $loop->index }}"@if ( ! in_array( strtolower( $csv_header ), $db_columns) ) {{ ' checked' }} @endif>Ignore column</label>
                                        </div>
                                    </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $csv_data as $row )
                                <tr class="@if ( $loop->first ) {{ 'danger' }} @endif">
                                    @foreach ( $row as $data )
                                    <td>{{ $data }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="form-group">
                            <div class="pull-right">
                                <button type="submit" class="btn btn-success">
                                    Confirm Import
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script') 
<script type="text/javascript">
    $(function() {
        $('.ignore-col').click(function() {
            if ( $('#dbCol-' + $(this).val()).attr('disabled') ) {
                $('#dbCol-' + $(this).val()).removeAttr('disabled');
            } else {
               $('#dbCol-' + $(this).val()).attr('disabled', 'disabled');
            }
        });
    });
</script>
@endsection
