@extends('masters.basemaster')
@section('title')
    Edit Mailing List Group
@stop
@section('csrf_token')
    <meta name="_token" content="{{ csrf_token() }}" />
@stop
@section('bodyContent')
    {!! Form::open(array('route'=>'postMailingListDepartment')) !!}
    <p>{!! Form::label('nameText','Name: ') !!}
        {!! Form::text('nameText',null,array('name'=>'nameText')) !!}</p>
    <p>{!! Form::label('userSelect','Users: ') !!}
        <select id='userSelect' name='userSelect[]' multiple>
            @for ($i = 0; $i < count($users); $i++)
                <option value="{{ $users[$i]->Id }}">{{ $users[$i]->FirstName }} {{ $users[$i]->LastName }}</option>
            @endfor
        </select></p>
    <br /><br />
    {!! Form::submit('Create Group',array('id'=>'submitButton')) !!}
    {!! Form::close() !!}
@stop