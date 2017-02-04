@extends('masters.basemaster')
@section('title')
    Show All Campaigns
@stop
@section('scripts')
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="/js/campaigns_angular.js"></script>
@stop
@section('stylesheets')
    <link href="/css/tables.css" rel="stylesheet" type="text/css" />
@stop
@section('bodyContent')
    <label>Filters: </label>
    <div ng-app="campaignApp" ng-controller="campaignController">
        <button ng-click='buttonFilter("active")'>Active</button>
        <button ng-click='buttonFilter("inactive")'>Inactive</button>
        <button ng-click='buttonFilter("")'>All</button>
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
            <th ng-click='sortColumn("Description")' ng-class='sortClass("Description")'>Description</th>
            <th ng-click='sortColumn("Status")' ng-class='sortClass("Status")'>Status</th>
            <th ng-click='sortColumn("Created")' ng-class='sortClass("Created")'>Created</th>
            <th ng-click='sortColumn("Updated")' ng-class='sortClass("Updated")'>Updated</th>
            <tr ng-repeat="x in campaigns | orderBy:column:reverse | filter:search | filter:buttonSearch:exceptEmpty">
                <td><a ng-href='/campaigns/[[ x.Id ]]'>[[ x.Name ]]</a></td>
                <td>[[ x.Description ]]</td>
                <td>[[ x.Status ]]</td>
                <td>[[ x.created_at ]]</td>
                <td>[[ x.updated_at ]]</td>
            </tr>
        </table>
    </div>
@stop