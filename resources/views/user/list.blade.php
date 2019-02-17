@extends('layouts.app', ['breadcrumb' => $breadcrumb,'header_title' => $header_title,'header_description' => $header_description])

@section('content')
<div id="main-wrapper">
  <div class="row">
      <div class="col-xs-12">

        <div class="box box-primary">
          <!-- /.box-header -->
          <div class="box-body">
            <div id="alertz">
              @if(session('success'))
              <div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 {{ session('success') }}
              </div>
              @endif
            </div>
            <form method="post" action="{{ url('/searchuser') }}" class="form-horizontal">
              {{ csrf_field() }}
              <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group">
                      <label for="name" class="col-sm-3 control-label">Name</label>
                      <div class="col-sm-9">
                        <input type="text" name="name" id="name" class="form-control input-sm" value="{{ $search['name'] }}" placeholder="Name">
                      </div>
                  </div>

                  <div class="form-group">
                      <label for="username" class="col-sm-3 control-label">Username</label>
                      <div class="col-sm-9">
                        <input type="text" name="username" id="username" class="form-control input-sm" value="{{ $search['username'] }}" placeholder="Username">
                      </div>
                  </div>

              </div>
              <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group">
                      <label for="email" class="col-sm-3 control-label">Email</label>
                      <div class="col-sm-9">
                        <input type="text" name="email" id="email" class="form-control input-sm" value="{{ $search['email'] }}" placeholder="Email">
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="phone" class="col-sm-3 control-label">Phone</label>
                      <div class="col-sm-9">
                        <input type="text" name="phone" id="phone" class="form-control input-sm" value="{{ $search['phone'] }}" placeholder="Phone">
                      </div>
                  </div>
                  
              </div>
              <div class="col-lg-4 col-md-6 col-sm-12">
                  <div class="form-group">
                      <label for="birthdate" class="col-sm-3 control-label">Birthdate</label>
                      <div class="col-sm-9">
                        <div class="input-group">
                          <input type="text" id="fdate" name="start" class="input-sm form-control date" rel="start" value="{{ $search['start'] }}"/>
                          <span class="input-group-addon">to</span>
                          <input type="text" id="tdate" name="end" class="input-sm form-control date" rel="end" value="{{ $search['end'] }}" />
                        </div>
                      </div>
                  </div>
                  <div class="pull-right text-center">
                      <button type="submit" style="margin-bottom:10px;" class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                      <a href="{{ route('user') }}" style="margin-bottom:10px;" class="btn btn-default"><i class="fa fa-list-alt"></i> Show All</a>
                      <a href="{{ url('adduser') }}" style="margin-bottom:10px;"  class="btn btn-success "><i class="fa fa-plus"></i> New</a>


                  </div>
              </div>
            </form>
          </div>
        </div>

        <div class="box box-success">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="table-responsive">
              <div class="col-md-12">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    
                    <th>No.</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Birthdate</th>
                    <th>Level</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
              
                  @foreach($data as $key => $datas)
                  <tr>
                    
                    <td><strong>{{ $no++ }}</strong></td>
                    <td>{{ $datas->name }}</td>
                    <td>{{ $datas->username }}</td>
                    <td>{{ $datas->email }}</td>
                    <td>{{ $datas->phone }}</td>
                    <td>{{ \Carbon\Carbon::parse($datas->birthdate)->format('d M Y') }}</td>
                    <td>@if($datas->level==1) Super User @else Editor @endif</td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ url('edituser',$datas->id) }}">
                        <button class="btn btn-success btn-xs ng-scope" type="button" data-toggle="tooltip" title="" data-original-title="Edi Dokument">
                        <i class="fa fa-fw fa-edit"></i>
                        </button>
                        </a>
                        <a onclick="return confirm('Apakah anda yakin akan menghapus ini?')" href="{{ url('deleteuser',$datas->id) }}">
                        <button class="btn btn-danger btn-xs ng-scope" type="button" data-toggle="tooltip" title="" data-original-title="Delete user">
                        <i class="fa fa-fw fa-close"></i>
                        </button>
                        </a>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            <div class="clearfix" style="margin-bottom:20px;">
              <div class="pull-left">
                  Showing <strong>{{ $offset + 1 }}</strong> - <strong>{{ $offset+$limit > $total ? $total : $offset+$limit }}</strong> of <strong>{{ $total }}</strong> data
              </div>
              {!! html_entity_decode($pagination) !!}
            </div>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
  </div>
</div>
@endsection
@section('js')

@stop