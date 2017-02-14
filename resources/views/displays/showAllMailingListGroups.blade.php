@extends('masters.basemaster')
@section('title')
    Show All Campaigns
@stop
@section('scripts')
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="/js/groups_angular.js"></script>
@stop
@section('stylesheets')
    <link href="/css/tables.css" rel="stylesheet" type="text/css" />
@stop
@section('bodyContent')
    <label>Filters: </label>
    <div ng-app="groupsApp" ng-controller="groupsController">
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-search"></i>
                    <input type="text" class="form-control" placeholder="Filter" ng-model="search" />
                </div>
            </div>
        </div>
        <table>
            <th ng-click='sortColumn("Name")' ng-class='sortClass("Name")'>Name</th>
            <tr ng-repeat="x in groups | orderBy:column:reverse | filter:search">
                <td>[[ x.name ]]</td>
                <td><a ng-href='/mailinglist/update/group/[[ x.Id ]]'>Edit</a></td>
                <!--<td><a ng-href="#delete">Delete</a></td>-->
            </tr>
        </table>
    </div>
@stop