@extends('masters.basemaster')
@section('title')
    Show All Campaigns
@stop
@section('scripts')
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="/js/mlu_angular.js"></script>
@stop
@section('stylesheets')
    <link href="/css/tables.css" rel="stylesheet" type="text/css" />
@stop
@section('bodyContent')
    <label>Filters: </label>
    <div ng-app="mluApp" ng-controller="mluController">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-search"></i>
                    <input type="text" class="form-control" placeholder="Filter" ng-model="search" />
                </div>
            </div>
        </div>
        <table>
            <th ng-click='sortColumn("FirstName")' ng-class='sortClass("FirstName")'>First Name</th>
            <th ng-click='sortColumn("LastName")' ng-class='sortClass("LastName")'>Last Name</th>
            <th ng-click='sortColumn("Email")' ng-class='sortClass("Email")'>Email</th>
            @if(\App\Http\Controllers\AuthController::safephishAdminCheck())
                <th ng-click='sortColumn("CompanyName")' ng-class='sortClass("CompanyName")'>Company Name</th>
            @endif
            <tr ng-repeat="x in mlu | orderBy:column:reverse | filter:search">
                <td>[[ x.first_name ]]</td>
                <td>[[ x.last_name ]]</td>
                <td>[[ x.email ]]</td>
                @if(\App\Http\Controllers\AuthController::safephishAdminCheck())
                    <td>[[ x.name ]]</td>
                @endif
                <td><a ng-href='/mailinglist/update/user/[[ x.id ]]'>Edit</a></td>
                <!--<td><a ng-href="#delete">Delete</a></td>-->
            </tr>
        </table>
    </div>
@stop