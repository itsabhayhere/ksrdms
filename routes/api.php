<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::fallback(function(){
    return response()->json(['message' => 'The resource you are looking for, not found.', "status_code" => 202, "status" => "ERROR"], 200);
})->name('api.fallback.404');

Route::post('showProfile', "API\DairyController@showProfile");
Route::post('updateProfile', "API\DairyController@updateProfile");

Route::post('webviewLogin', "API\WebviewController@login");


Route::get('getStatesCities', 'API\DairyController@getStatesCities');
Route::get('getCities', 'API\DairyController@getCities');
Route::get('getStates', 'API\DairyController@getStates');


Route::post('login', 'API\LoginController@login');
Route::post('logout', 'API\LoginController@logout');

Route::post('sendLoginOtp', 'API\LoginController@sendLoginOtp');
Route::post('otpLogin', 'API\LoginController@otpLogin');

Route::post('setNewPass', 'API\LoginController@setNewPass');

Route::post('dairy_summary', 'API\DairyController@dairy_summary');
Route::post('memberCreditAndMilkCollection', 'API\DairyController@memberCreditAndMilkCollection');

Route::post('creditMembers', 'API\DairyController@creditMembers');
Route::post('creditSuppliers', 'API\DairyController@creditSuppliers');
Route::post('creditCustomers', 'API\DairyController@creditCustomers');
Route::post('debitMembers', 'API\DairyController@debitMembers');
Route::post('debitSuppliers', 'API\DairyController@debitSuppliers');
Route::post('debitCustomers', 'API\DairyController@debitCustomers');


Route::post('memberList', 'API\DairyController@memberList');
Route::post('memberDetail', 'API\DairyController@memberDetail');
Route::post('memberEdit', 'API\DairyController@memberEdit');
Route::post('memberAdd', 'API\DairyController@memberNew');
Route::post('memberDelete', 'API\DairyController@memberDelete');
//new
Route::post('memberstatus','API\DairyController@active_inactiveMember');

Route::post('customerList', 'API\DairyController@customerList');
Route::post('customerDetail', 'API\DairyController@customerDetail');
Route::post('customerEdit', 'API\DairyController@customerEdit');
Route::post('customerAdd', 'API\DairyController@customerNew');

Route::post('supplierList', 'API\DairyController@supplierList');
Route::post('supplierDetail', 'API\DairyController@supplierDetail');
Route::post('supplierEdit', 'API\DairyController@supplierEdit');
Route::post('supplierAdd', 'API\DairyController@supplierNew');

Route::post('milkPlantListForDairy', 'API\DairyController@milkPlantListForDairy');
Route::post('mainMilkPlantList', 'API\DairyController@mainMilkPlantList');
Route::post('milkPlantList', 'API\DairyController@milkPlantList');
Route::post('addMilkPlantToDairy', 'API\DairyController@addMilkPlantToDairy');

Route::post('productList', 'API\DairyController@productList');
Route::post('productDetail', 'API\DairyController@productDetail');
Route::post('productEdit', 'API\DairyController@productEdit');
Route::post('productAdd', 'API\DairyController@productNew');
Route::post('productStockAdd', 'API\DairyController@productStockAdd');
Route::post('productPurchaseHistory', 'API\DairyController@productPurchaseHistory');

Route::post('expenseList', 'API\DairyController@expenseList');
// Route::post('expenseDetail', 'API\DairyController@expenseDetail');
Route::post('expenseAdd', 'API\DairyController@expenseNew');

Route::post('expenseHeads', 'API\DairyController@expenseHeads');
Route::post('expenseHeadNew', 'API\DairyController@expenseHeadNew');

Route::post('milkCollectionList', 'API\DairyController@milkCollectionList');
Route::post('milkCollection', 'API\DairyController@milkCollection');
Route::post('fatSnfValue', 'API\DairyController@milkCollectionFatSnfValue');
Route::post('milkCollectionReq', 'API\DairyController@milkCollectionReq');

Route::post('localSale', 'API\DairyController@localSale');
Route::post('productSale', 'API\DairyController@productSale');
Route::post('localSaleList', 'API\DairyController@localSaleList');
Route::post('productSaleList', 'API\DairyController@productSaleList');
// Route::post('localSaleDetail', 'API\DairyController@localSaleDetail');
// Route::post('productSaleDetail', 'API\DairyController@productSaleDetail');
Route::post('plantSaleList', 'API\DairyController@plantSaleList');
Route::post('plantSale', 'API\DairyController@plantSale');

Route::post('advanceList', 'API\DairyController@advanceList');
Route::post('advanceAdd', 'API\DairyController@advanceAdd');
Route::post('creditList', 'API\DairyController@creditList');
Route::post('creditAdd', 'API\DairyController@creditAdd');

Route::post('getMilkPrice', 'API\DairyController@getMilkPrice');

Route::post('milkRequestList', 'API\DairyController@milkRequestList');
Route::post('productDlvryReqList', 'API\DairyController@productDlvryReqList');
Route::post('requestComplete', 'API\DairyController@requestComplete');

Route::post('collectionManagers', 'API\DairyController@collectionManagers');
Route::post('addCollectionManager', 'API\DairyController@addCollectionManager');

Route::post('dairyBalance', 'API\DairyController@dairyBalance');
Route::post('dairySubscription', 'API\DairyController@dairySubscription');

Route::post('get_user_detail', 'API\DairyController@getUserDetail');

Route::post('milkCollection_edit', 'API\DairyController@milkCollection_edit');
Route::post('milkCollection_delete', 'API\DairyController@milkCollection_delete');


Route::get('rateCardShow', 'API\WebviewController@rateCardShow');
Route::post('rateCardDetails', 'API\WebviewController@rateCardDetails');
Route::get('rateCardNew', 'API\WebviewController@rateCardNew');
Route::post('saveRateCardNew', 'API\WebviewController@saveRateCardNew');
Route::post('deleteRateCard', 'API\WebviewController@deleteRateCard');
Route::post('applyRatecard', 'API\WebviewController@applyRatecard');
Route::post('updateRateCardNew', 'API\WebviewController@updateRateCardNew');


Route::post('getUserDetail', 'API\WebviewController@getUserDetail');

Route::get('reports', 'API\WebviewController@reports');
Route::post('getSaleReport', 'API\WebviewController@getSaleReport');
Route::post('getMemberReport', 'API\WebviewController@getMemberReport');
Route::post('getRateCardReport', 'API\WebviewController@getRateCardReport');
Route::post('getShiftReport', 'API\WebviewController@getShiftReport');
Route::post('getMemberPassbookReport', 'API\WebviewController@getMemberPassbookReport');
Route::post('getBalanceSheetReport', 'API\WebviewController@getBalanceSheetReport');
Route::post('getLedgerReport', 'API\WebviewController@getLedgerReport');
Route::post('getCmSubsidiaryReport', 'API\WebviewController@getCmSubsidiaryReport');
Route::post('getMemStatementReport', 'API\WebviewController@getMemStatementReport');
Route::post('getCustomerSalseReport', 'API\WebviewController@getCustomerSalseReport');
Route::post('getProfitLossReport', 'API\WebviewController@getProfitLossReport');



//--------------------Member Apis----------------------//
Route::post('memDashboardSummary', 'API\DairyController@memDashboardSummary');
Route::post('memDailyTransaction', 'API\DairyController@memDailyTransaction');
Route::post('memPurchaseHistory', 'API\DairyController@memPurchaseHistory');
// Route::post('memPayments', 'API\DairyController@memPayments');
Route::post('memMilkReqList', 'API\DairyController@memMilkReqList');
Route::post('memProdReqList', 'API\DairyController@memProdReqList');
Route::post('memMilkProdReqSend', 'API\DairyController@memMilkProdReqSend');

Route::get('memStatement', 'API\WebviewController@memStatement');

Route::post('memStatementListAjax', 'API\WebviewController@memStatementListAjax');


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('details', 'API\LoginController@details');
});
