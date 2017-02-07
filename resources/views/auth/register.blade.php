@extends('masters.basemaster')
@section('title')
    Register User
@stop
@section('scripts')
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
@stop
@section('stylesheets')

@stop
@section('bodyContent')
    <h3 style="font-weight: 300">Register User</h3>
    {!! Form::open(array('route'=>'register')) !!}
    <p>
    <p>{!! Form::label('emailText','Email: ') !!}
        {!! Form::text('emailText',null,array('name'=>'emailText')) !!}</p>
    <p>{!! Form::label('confirmEmailText','Confirm Email: ') !!}
        {!! Form::text('confirmEmailText',null,array('name'=>'confirmEmailText')) !!}</p>
    <p>{!! Form::label('usernameText','Username: ') !!}
        {!! Form::text('usernameText',null,array('name'=>'usernameText')) !!}</p>
    <p>{!! Form::label('firstNameText','First Name: ') !!}
        {!! Form::text('firstNameText',null,array('name'=>'firstNameText')) !!}</p>
    <p>{!! Form::label('lastNameText','Last Name: ') !!}
        {!! Form::text('lastNameText',null,array('name'=>'lastNameText')) !!}</p>
    <p>{!! Form::label('middleInitialText','Middle Initial: ') !!}
        {!! Form::text('middleInitialText',null,array('name'=>'middleInitialText','maxlength'=>1)) !!}</p>
    <datalist id="permissionsDatalist">
        @for ($i = 0; $i < count($permissions); $i++)
            <option value="{{ $permissions[$i]->Id }}">
                {{ $permissions[$i]->PermissionType }}</option>
        @endfor
    </datalist>
    <p>{!! Form::label('permissionsText','Permissions: ') !!}
        {!! Form::text('permissionsText',null,array('name'=>'permissionsText','datalist'=>'permissionsDatalist')) !!}</p>
    {!! Form::submit('Register',array('id'=>'submitButton')) !!}
    {!! Form::close() !!}
@stop