@extends('voyager::master')

@section('css')
<style>
    .chainheader {
        background: #888;
        color: #fff;
        font-weight: bold;
        padding: 5px;
        text-align: center;
    }
    .itemremove {
        position: absolute;
        right: 0;
        background: #e8e2e2;
        top: 0;
        bottom: 0;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        line-height: 1;
        padding: 3px;
        color: #cc0000;
        cursor: pointer;
    }
    .chain-wrapper {
        border-bottom: 1px solid #ccc;
        padding: 5px;
        margin-bottom: 20px;
    }
    .additem-btn{
        margin: 0;
    }
    .chainadditem-block {
        margin-top: 20px;
    }
    .action-message {
        display: inline-block;
        padding: 15px;
    }
    .action-message.success {
        color: green;
    }
    .action-message.error {
        color: #cc0000;
    }
    i.voyager-refresh {
        animation: spin .6s linear infinite;
        display: inline-block;
        line-height: 0;
        padding: 0px 5px 5px 7px;
    }
    
    .chainitem {
        padding: 5px;
        margin-bottom: 9px;
        box-shadow: -1px 2px 6px 1px rgba(0,0,0,0.20);
    }
    .chainitem.active{
        background: #c4ecff;
    }
    .actionmenus a {
        display: inline-block;
        text-align: center;
        cursor: pointer;
    }
    .actionmenus {
        display: inline-flex;
        flex-direction: column;
    }
    a.actionbtn.actionbtn-expand {
        background: #19b5fe;
        display: inline-flex;
        color: #fff;
        font-size: 23px;
        justify-content: center;
        align-items: center;
        line-height: 0;
        height: 100%;
    }
    a.actionbtn.actionbtn-delete {
        border: 1px solid #a54646;
        line-height: 0;
        font-size: 18px;
        color: #b51c1c;
    }
    .inputname-block {
        width: 100%;
        padding: 0 5px 2px 2px;
    }
    .inputname-block input {
        width: 100%;
        margin-top: 5px;
    }
    span.name-lable {
        background-color: #19b5fe;
        color: #fff;
        font-size: 10px;
        margin-bottom: -7px;
        display: block;
        width: 65px;
        margin-top: 5px;
        text-align: center;
    }
    .pricing-type-block input{
        width: auto;
    }
    .pricing-type-block{
        margin-top: 5px;
    }
    .save-btn-bottom {
        background: #fff;
        position: fixed;
        right: 0;
        bottom: 0;
        padding: 0px 20px;
        z-index: 5;
        text-align: center;
        box-shadow: 1px 1px 12px 1px rgba(0,0,0,0.3);
    }
    .chainitem-wrapper {
        position: relative;
        display: flex;
        justify-content: space-between;
    }
</style>
@stop

@section('content')
<div class="container-fluid" ng-app="myApp" ng-controller="myCtrl">
    <script type="text/ng-template" id="your_template">
        <div class="chainitem-wrapper">
            <div class="inputname-block">
                
                <ng-container ng-if="chainitem.id != 'all'">
                    <div>
                        <span class="name-lable">Customer</span>
                        <input ng-model="chainitem.cust_name" class="cust_name" ng-change="nameChange('cust_name',type,chainitem)">
                    </div>
                    <div>
                        <span class="name-lable">Provider</span>
                        <input ng-model="chainitem.prov_name" class="prov_name" ng-change="nameChange('prov_name',type,chainitem)">
                    </div>
                    
                    <div ng-if="type == 'services'">
                        <span class="name-lable">Provider Fee</span>
                        <input ng-model="chainitem['provider_fee']" class="provider_fee">
                    </div>
                    
                    <div ng-if="type == 'properties' || type == 'services'">
                        <span class="name-lable">Pricing Type</span>
                        <div class="pricing-type-block" ng-init="nm = getRandomVal()">
                            <span style="white-space: nowrap;"><input type="radio" ng-model="chainitem['pricetype']" ng-change="nameChange('pricetype',type,chainitem)" name="pricetype_<%= nm %>" value="range"> Range</span>
                            <span style="white-space: nowrap;"><input type="radio" ng-model="chainitem['pricetype']" ng-change="nameChange('pricetype',type,chainitem)" name="pricetype_<%= nm %>" value="fixed"> Fixed</span>
                            <span style="white-space: nowrap;"><input type="radio" ng-model="chainitem['pricetype']" ng-change="nameChange('pricetype',type,chainitem)" name="pricetype_<%= nm %>" value="noprice"> No Price</span>
                            <span style="white-space: nowrap;" ng-if="type == 'properties'"><input type="radio" ng-model="chainitem['pricetype']" ng-change="nameChange('pricetype',type,chainitem)" name="pricetype_<%= nm %>" value="silenced"> Silenced</span>
                        </div>
                    </div>
                    <div ng-if="type == 'services'">
                        <span style="white-space: nowrap;">
                        <input type="checkbox" style="width:auto;" ng-model="chainitem['fixedlocation']" ng-true-value="'1'"
                            ng-false-value="'0'" ng-change="nameChange('fixedlocation',type,chainitem)" name="fixedlocation" > Fixed Location</span>
                    </div>
                    
                </ng-container>
                
                <div  ng-if="chainitem.id == 'all'">
                    <input readonly value="<%= chainitem['cust_name'] %>">
                </div>

            </div>
            <span class="actionmenus">
                <a class="actionbtn actionbtn-expand" ng-if="expand" ng-click="setExpandData(expand, chainitem)";><i class="voyager-angle-right"></i></a>
                <a class="actionbtn actionbtn-delete" ng-click="removeItem(key,type)"><i class="voyager-x"></i></a>
            </span>
        </div>
    </script>
<!--ng-change="nameChange('provider_fee',type,chainitem)"-->

    <h2>Chain Management</h2>
    <div class="row">        
        <div class="col-sm-4"><div class="chainheader">Segment</div></div>
        <div class="col-sm-4"><div class="chainheader">Object</div></div>
        <div class="col-sm-4"><div class="chainheader">Service</div></div>
        <!--<div class="col-sm-3"><div class="chainheader">Location</div></div>-->
    </div>
    <div class='chain-wrapper chain-customer' >
        
        <!--chainid="$chain->id"-->
        <div class="row">
            <div class="col-sm-4">
                <div class="text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#createChainItemModal" ng-click="setModalData('propertytypes')">Create</button>
                </div>
                <div class="chain-main">
                    <div class="chain-block">
                        <div ng-repeat="(key, chainitem) in chainpropertytypes" ng-class="{'active':active.propertytypes == chainitem.property_type_id}" class="chainitem item-propertytypes"> 
                            <div ng-include="'your_template'" ng-init="expand = 'properties';type = 'propertytypes'"></div>
                        </div>
                    </div>
                    <div class="chainadditem-block text-center">
                        <div class="input-group newitemselection">
                            <select class="form-control" ng-model="propertyTypesList_value">
                                <option ng-repeat="(key, propertytype) in propertyTypesList" ng-value="key" 
                                        > <%= propertytype.cust_name %></option>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-default additem-btn form-control" type="button" ng-click="addItem('propertytypes')">Add</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#createChainItemModal" ng-click="setModalData('properties')">Create</button>
                </div>
                <div class="chain-main main-properties" ng-hide="!active.propertytypes">
                    <div class="chain-block">
                        <div ng-repeat="(key, chainitem) in chainproperties"  ng-class="{'active':active.properties == chainitem.property_id}" class="chainitem item-propertytypes"> 
                            <div ng-include="'your_template'" ng-init="expand = 'services';type = 'properties'"></div>
                        </div>
                    </div>
                    <div class="chainadditem-block text-center">
                        <div class="input-group newitemselection">
                            <select class="form-control" ng-model="propertiesList_value">
                                <option ng-repeat="(key, property) in propertiesList" ng-value="key" 
                                    > <%= property.cust_name %></option>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-default additem-btn form-control" type="button" ng-click="addItem('properties')">Add</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#createChainItemModal" ng-click="setModalData('services')">Create</button>
                </div>
                <div class="chain-main main-services" ng-hide="!active.properties">
                    <div class="chain-block">
                        <div ng-repeat="(key, chainitem) in chainservices" class="chainitem item-propertytypes"> 
                            <div ng-include="'your_template'" ng-init="expand = '';type = 'services'"></div>
                        </div>
                    </div>
                    <div class="chainadditem-block text-center">
                        <div class="input-group newitemselection">
                            <select class="form-control" name="servicesList_value" ng-model="servicesList_value">
                                <option ng-repeat="(key, service) in servicesList" ng-value="key"> <%= service.cust_name %></option>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-default additem-btn form-control" type="button" ng-click="addItem('services')">Add</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
<!--            <div class="col-sm-3">
                <div class="text-right">
                    <button class="btn btn-success" data-toggle="modal" data-target="#createChainItemModal" ng-click="setModalData('locations')">Create</button>
                </div>
                <div class="chain-main">
                    <div class="chain-block" >
                        <div ng-repeat="(key, chainitem) in chainlocations" class="chainitem item-propertytypes"> 
                            <div ng-include="'your_template'" ng-init="expand = '';type = 'locations'"></div>
                        </div>
                    </div>
                    <div class="chainadditem-block text-center">
                        <div class="input-group newitemselection">
                            <select class="form-control" ng-model="locationList_value">
                                <option ng-repeat="(key, location) in locationList" ng-value="key" 
                                    > <%= location.cust_name %></option>
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-default additem-btn form-control" type="button" ng-click="addItem('locations')">Add</button>
                            </span>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
        
        <div class="text-right save-btn-bottom">
            <span class="action-message" style="display:none;"></span>
            <button class="btn btn-success" ng-click="saveChain(this)" ng-disabled="saveloader">
                <span class="saveloader" ng-if="saveloader"><i class="voyager-refresh"></i>
                </span>Save Chain</button>
        </div>
    </div>
    
<!-- The Modal -->
<div class="modal" id="createChainItemModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create <%= modalTypeText %></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
          <div>
              <div>
                <label>Name for Customer</label>
                <input name="custname" class="form-control" placeholder="Name for Customer" ng-model="create_cust_name">
              </div><br>
              <div>
                <label>Name for Provider</label>
                <input name="provname" class="form-control" placeholder="Name for Provider" ng-model="create_prov_name">
              </div>
          </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
          <span class="create-btn-bottom">
            <span class="action-message" style="display:none;"></span>
          </span>
          <button class="btn btn-success" ng-click="saveCreateItem()" ng-disabled="createloader">
            <span class="saveloader" ng-if="createloader"><i class="voyager-refresh"></i>
            </span>Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        
      </div>

    </div>
  </div>
</div>
</div>

@stop

@section('javascript')
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.0/angular.min.js"></script>

<script>
    var app = angular.module('myApp', [])
    .config(function($interpolateProvider) {
        $interpolateProvider.startSymbol('<%=');
        $interpolateProvider.endSymbol('%>');
    });
    app.controller('myCtrl', function($scope) {
      $scope.active = {
          propertytypes:0,
          properties:0,
          services:0,
          locations:0,
      };
      $scope.saveloader = false;
      $scope.createloader = false;
      $scope.modalTypeText = '';
      $scope.modalType = '';
      $scope.create_cust_name = "";
      $scope.create_prov_name = "";
      
      $scope.chainpropertytypes = [];
      $scope.chainproperties = [];
      $scope.chainservices = [];
      $scope.chainlocations = [];
      
      $scope.propertyTypesList = [];
      $scope.propertyTypesList_value = 0;
      $scope.propertiesList = [];
      $scope.propertiesList_value = 0;
      $scope.servicesList = [];
      $scope.servicesList_value = 0;
      $scope.locationList = [];
      $scope.locationList_value = 0;
      
      $.ajax({
          url:'{{url("admin/chain/getdata")}}',
          type:'get',
          dataType:'json',
          success: function(data) {
              $scope.chainpropertytypes = data.chainpropertytypes;
              $scope.chainlocations = data.customerCities;
              
              $scope.propertyTypesList = data.data.propertytypes;
              $scope.propertiesList = data.data.properties;
              $scope.servicesList = data.data.services;
//              $scope.servicesList.unshift({
//                    'id':'all',
//                    'service_id':'all',
//                    'cust_name': 'All Services',
//                    'prov_name':'All Services',
//                    'provider_fee': "",
//              })
              $scope.locationList = data.data.cities;
//              $scope.locationList.unshift({
//                    'id':'all',
//                    'city_id':'all',
//                    'cust_name':'All Locations',
//                    'prov_name':'All Locations'
//              });
               $scope.locationList_value = 0;
              
              console.log('$scope.chainpropertytype',$scope.chainpropertytypes)
              $scope.$apply();
          }
      });
      
      $scope.setExpandData = function(type, item) {
            if(type == 'properties'){
                $scope.active.properties = 0;
                $scope.chainproperties = item.properties;
                $scope.active.propertytypes = item.property_type_id;
            }
            if(type == 'services'){
                $scope.chainservices = item.services;
                $scope.active.properties = item.property_id;
            }
      }
      
      $scope.addItem = function(type) {
        if(type == 'propertytypes'){
            let data = $scope.propertyTypesList[$scope.propertyTypesList_value];
            if(!$scope.chainpropertytypes.find(x => x.property_type_id == data.id)){
                $scope.chainpropertytypes.push({
                    'id':'',
                    'property_type_id':data.id,
                    'cust_name':data.cust_name,
                    'prov_name':data.prov_name,
                    'properties':[]
                })
            }
        }

        if(type == 'properties'){
            let data = $scope.propertiesList[$scope.propertiesList_value];
            if(!$scope.chainproperties.find(x => x.property_id == data.id)){
                $scope.chainproperties.push({
                    'id':'',
                    'property_id':data.id,
                    'cust_name':data.cust_name,
                    'prov_name':data.prov_name,
                    'pricetype': data.pricetype || "range",
                    'services':[]
                })
            }
        }

        if(type == 'services'){
            console.log($scope.servicesList_value)
            let data = $scope.servicesList[$scope.servicesList_value];
            if(!$scope.chainservices.find(x => x.service_id == data.id)){
                $scope.chainservices.push({
                    'id':data.id,
                    'service_id':data.id,
                    'cust_name':data.cust_name,
                    'prov_name':data.prov_name,
                    'provider_fee': "0",
                    'fixedlocation': "0",
                })
            }
            
        }
        if(type == 'locations'){
            console.log($scope.locationList_value)
            let data = $scope.locationList[$scope.locationList_value];
            if(!$scope.chainlocations.find(x => x.city_id == data.id)){
                $scope.chainlocations.push({
                    'id':data.id,
                    'city_id':data.id,
                    'cust_name':data.cust_name,
                    'prov_name':data.prov_name
                })
            }
        }
    }
    $scope.removeItem = function(key,type) {
        if(type == 'propertytypes'){
            $scope.chainpropertytypes.splice(key, 1)
        }
        if(type == 'properties'){
            $scope.chainproperties.splice(key, 1)
        }
        if(type == 'services'){
            $scope.chainservices.splice(key, 1)
        }
        if(type == 'locations'){
            $scope.chainlocations.splice(key, 1)
        }
    }
    $scope.saveChain = function() {
        console.log($scope.chainpropertytypes);
        $scope.saveloader = true;
        $.ajax({
          url:'{{url("admin/chain/save")}}',
          type:'post',
          dataType:'json',
          data:{data:$scope.chainpropertytypes, locations:$scope.chainlocations},
          success: function(data) {
                $scope.$apply(function () {
                    $scope.saveloader = false;
               });
              
              $('.save-btn-bottom').find(".action-message").removeClass('success error')
                if(data.success){
                    $('.save-btn-bottom').find(".action-message").html('Chain saved.').addClass('success')
                        .slideDown().delay(3000).slideUp();
                }else{
                    $('.save-btn-bottom').find(".action-message").html('Something went wrong.').addClass('error')
                        .slideDown().delay(3000).slideUp();
                }
          }
      });
    }
    $scope.setModalData = function(type) {
        if(type == "propertytypes"){
            $scope.modalTypeText = "Segment / Property Type";
        }else if(type == "properties") {
            $scope.modalTypeText = "Object / Property";
        }else if(type == "services") {
            $scope.modalTypeText = "Service";
        }else if(type == "locations") {
            $scope.modalTypeText = "Location";
        }
        $scope.modalType = type;
        $scope.create_cust_name = "";
        $scope.create_prov_name = "";
        
    }
    
    $scope.saveCreateItem = function() {
        console.log($scope.create_cust_name,$scope.create_prov_name)
        if(!$scope.create_cust_name || !$scope.create_prov_name){
            $('.create-btn-bottom').find(".action-message").html('Please Enter Names.').addClass('error')
                .slideDown().delay(3000).slideUp();
            return;
        }
        let url = "";
        if($scope.modalType == "propertytypes"){
            url = '{{url("admin/chain/create/propertytype")}}';
        }else if($scope.modalType == "properties") {
            url = '{{url("admin/chain/create/property")}}';
        }else if($scope.modalType == "services") {
            url = '{{url("admin/chain/create/service")}}';
        }else if($scope.modalType == "locations") {
            url = '{{url("admin/chain/create/location")}}';
        }
        console.log($scope.chainpropertytypes);
        $scope.createloader = true;
        $.ajax({
          url: url,
          type:'post',
          dataType:'json',
          data:{cust_name:$scope.create_cust_name, prov_name:$scope.create_prov_name},
          success: function(data) {
              $scope.createloader = false;
                $('.create-btn-bottom').find(".action-message").removeClass('success error')
                if(data.success){
                    $('.create-btn-bottom').find(".action-message").html('saved successfully.').addClass('success')
                        .slideDown().delay(3000).slideUp();
                
                    setTimeout(function(){
                        $("#createChainItemModal").modal('hide');
                    },2000)
                    
                    if($scope.modalType == "propertytypes"){
                        $scope.$apply(function () {
                            $scope.propertyTypesList.push(data.data)
                       });
                        
                    }else if($scope.modalType == "properties") {
                        $scope.$apply(function () {
                            $scope.propertiesList.push(data.data)
                       });
                         
                    }else if($scope.modalType == "services") {
                        $scope.$apply(function () {
                            $scope.servicesList.push(data.data)
                       });
                         
                    }else if($scope.modalType == "locations") {
                        $scope.$apply(function () {
                             $scope.locationList.push(data.data)
                       });
                        
                    }
                    $scope.modalType = '';
                    $scope.create_cust_name = "";
                    $scope.create_prov_name = "";
                }else{
                    $('.create-btn-bottom').find(".action-message").html('Something went wrong.').addClass('error')
                        .slideDown().delay(3000).slideUp();
                }
          }
      });
    }
    $scope.getRandomVal = function(name,type,item) {
        return Math.random().toString(36).substring(7);
    }
    $scope.nameChange = function(name,type,item) {
    console.log("namechanged = ",item[name]);
        if(type == 'propertytypes'){
            $scope.chainpropertytypes.forEach((ele) => {
                if(ele.property_type_id == item.property_type_id){
                    ele[name] = item[name];
                }
            })
            $scope.propertyTypesList.forEach((ele) => {
                if(ele.id == item.property_type_id){
                    ele[name] = item[name];
                }
            })
        }
        if(type == 'properties'){
            $scope.chainpropertytypes.forEach((ele) => {
                ele.properties.forEach((ele2) => {
                    if(ele2.property_id == item.property_id){
                        ele2[name] = item[name];
                    }
                })
            })
            $scope.propertiesList.forEach((ele) => {
                if(ele.id == item.property_id){
                    ele[name] = item[name];
                }
            });
        }
        if(type == 'services'){
            $scope.chainpropertytypes.forEach((ele) => {
                ele.properties.forEach((ele2) => {
                    ele2.services.forEach((ele3) => {
                        if(ele3.service_id == item.service_id){
                            ele3[name] = item[name];
                        }
                    });
                });
            });
            $scope.servicesList.forEach((ele) => {
                if(ele.id == item.service_id){
                    ele[name] = item[name];
                }
            })
        }
        if(type == 'locations'){
            $scope.chainlocations.forEach((ele) => {
                if(ele.city_id == item.city_id){
                    ele[name] = item[name];
                }
            });
            $scope.locationList.forEach((ele) => {
                if(ele.id == item.city_id){
                    ele[name] = item[name];
                }
            });
        }
    }
});
</script>
@stop
