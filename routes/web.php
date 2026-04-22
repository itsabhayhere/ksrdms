    <?php

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    \Debugbar::disable();

    Route::get('/', function () {
        return redirect("dairy-login");

        // if ($user = Auth::user() || !empty($loginUserInfo)) {
        //     return redirect("DairyAdminDashbord");
        // } else {
        //     return redirect("dairy-login");
        // }
    });


    //daily_transactions

    Auth::routes();

    Route::get('/home', 'HomeController@myHome')->name('home');

    Route::post('/setMilkPrice', 'ProductController@setMilkPrice');

    // Route::get('my-home', 'HomeController@myHome')->middleware('test');
    // Route::get('my-home', 'HomeController@myHome');
    // Route::get('my-users', 'HomeController@myUsers');
    // Route::get('logout', 'UserController@userLogout');

    Route::get('/my-login', 'HomeController@loginForm');
    Route::post('/my-login-submit', 'HomeController@loginFormSubmit');
    Route::get('/my-logout', 'HomeController@logoutUser');
    Route::get('/dairySetup', 'DairyAdminController@allStates');
    Route::get('/dairy-login', 'HomeController@DiryLoginForm');
    Route::POST('/sendLoginOtp', 'HomeController@sendLoginOtp');
    Route::POST('/loginOtp', 'HomeController@loginOtp');

    $router::get('/add-dairy-admin/city', 'DairyAdminController@allCitys');
    
    $router->group(['middleware' => 'test'], function () use ($router) {
        
        $router::get('my-home', 'HomeController@myHome');
        $router::get('my-users', 'HomeController@myUsers');
        $router::get('logout', 'UserController@userLogout');
        $router::post('sidebarMenu', 'UserController@sidebarMenu');
        $router::post('sidebarSubMenu', 'UserController@sidebarSubMenu');

        /* get menu by user */
        // $router::post('userSidebarMenu', 'UserController@userSidebarMenu');
        // $router::post('userSidebarSubMenu', 'UserController@userSidebarSubMenu');

        /* dairy setup */
        // $router::get('/add-dairy-admin', 'DairyAdminController@allStates');
        $router::get('/dairySetup', 'DairyAdminController@allStates');
        $router::get('/add-dairy-admin/SocietyValidate', 'DairyAdminController@SocietyValidate');
        $router::get('/add-dairy-admin/numberValidate', 'DairyAdminController@numberValidate');
        $router::get('/add-dairy-admin/memberPersonalCode', 'DairyAdminController@memberPersonalCodeValidation');
        $router::get('/add-dairy-admin/memberEmailValidation', 'DairyAdminController@memberEmailValidation');
        $router::get('/editDairyInfo', 'DairyAdminController@editDairyInfo');
        $router::post('/editDairyInfoSubmit', 'DairyAdminController@editDairyInfoSubmit');
        $router::get('/editDairyEmailValidation', 'DairyAdminController@editDairyEmailValidation');

        /* Member */
        $router::get('/memberSetupForm', 'MemberSetupController@memberSetupForm');
        $router::get('/memberPersonalCode', 'MemberSetupController@memberPersonalCodeValidation');
        $router::get('/memberNumberValidation', 'MemberSetupController@memberNumberValidation');
        $router::get('/memberNameValidation', 'MemberSetupController@memberNameValidation');
        $router::get('/memberAadharNumberValidation', 'MemberSetupController@memberAadharNumberValidation');
        $router::post('/memberSetupFormSubmit', 'MemberSetupController@create');
        $router::get('/memberList', 'MemberSetupController@memberList');
        $router::post('/deleteMember', 'MemberSetupController@deleteMember');
        $router::get('/deleted_member_list', 'MemberSetupController@deleted_member_list');

        $router::get('/editMemberNumberValidation', 'MemberSetupController@editMemberNumberValidation');
        $router::get('/editMemberAadharNumberValidation', 'MemberSetupController@editMemberAadharNumberValidation');
        $router::get('/editMemberNameValidation', 'MemberSetupController@editMemberNameValidation');

        $router::get('/editMemberInfo', 'DairyAdminController@editMemberInfo');
        $router::post('/editMemberInfoSubmit', 'MemberSetupController@editMemberInfoSubmit');

        /* supplier */
        $router::get('/supplierForm', 'SupplierController@supplierForm');
        $router::get('/checkSupplierCode', 'SupplierController@checkSupplierCode');
        $router::get('/checkSupplierEmail', 'SupplierController@checkSupplierEmail');
        $router::post('/supplierSubmit', 'SupplierController@create');
        $router::get('/supplierEdit', 'SupplierController@show');
        $router::post('/supplierEditSubmit', 'SupplierController@update');
        $router::get('/supplierEditEmailValidation', 'SupplierController@supplierEditEmailValidation');
        $router::get('/supplierList', 'SupplierController@supplierList');
        $router::post('/deleteSupplier', 'SupplierController@destroy');

        /* rate card setup by fat */
        $router::get('/rateCardForm', 'RateCardController@rateCardForm');

        $router::get('/rateCardNew', 'RateCardController@rateCardNew');
        $router::post('/saveRateCardNew', 'RateCardController@saveRateCardNew');
        Route::get('/rateCardShowNew', 'RateCardController@rateCardShowNew');
        Route::post('/getRateCardList', 'RateCardController@getRateCardList');
        Route::post('/deleteRateCard', 'RateCardController@deleteRateCard');
        Route::post('/updateRateCardNew', 'RateCardController@updateRateCardNew');
        Route::post('/applyRatecard', 'RateCardController@applyRatecard');

        $router::post('/rateCardFetSubmit', 'RateCardController@rateCardFetSubmit');
        $router::post('/editFatPrice', 'RateCardController@editFatPrice');
        $router::post('/submitFatPriceEdit', 'RateCardController@submitFatPriceEdit');
        // $router::post('/rateCardFetSnfSubmit', 'RateCardController@rateCardFetSnfSubmit');

        /* rate card setup by fat and snf */
        $router::post('/addFatSnfRange', 'RateCardController@addFatSnfRange');
        $router::post('/deleteFatSnfRange', 'RateCardController@deleteFatSnfRange');
        $router::post('/fatSnfSingleRange', 'RateCardController@fatSnfSingleRange');
        $router::post('/fatSnfSingleRangeEditSubmit', 'RateCardController@fatSnfSingleRangeEditSubmit');
        $router::post('/fatSnfRateCardSubmit', 'RateCardController@fatSnfRateCardSubmit');
        $router::post('/fatSnfRateCardvalue', 'RateCardController@fatSnfRateCardvalue');
        $router::get('/fatSnfRateCardShow', 'RateCardController@rateCardShowNew');
        $router::post('/getFatSnfRangeData', 'RateCardController@getFatSnfRangeData');
        $router::post('/getFatSnfRangeDataPdf', 'RateCardController@getFatSnfRangeDataPdf');

        $router::post('/getTransValues', 'RateCardController@getTransValues');

        /* Customer Set up */
        $router::get('/CustomerForm', 'CustomerController@customerForm');
        $router::get('/checkCustomerCode', 'CustomerController@checkCustomerCode');
        $router::get('/checkCustomerEmail', 'CustomerController@checkCustomerEmail');
        $router::post('/customerSubmit', 'CustomerController@create');
        $router::get('/customerEdit', 'CustomerController@show');
        $router::post('/customerEditSubmit', 'CustomerController@update');
        $router::get('/customerEditEmailValidation', 'CustomerController@customerEditEmailValidation');
        $router::get('customerList', 'CustomerController@customerList');
        $router::post('deleteCustomer', 'CustomerController@destroy');

        /* product setup */
        $router::get('/ProductForm', 'ProductController@productSetup');
        $router::post('/productCodeValidation', 'ProductController@productCodeValidation');
        $router::post('/productSubmit', 'ProductController@create');
        $router::get('/productEdit', 'ProductController@edit');
        $router::get('/productStockAdd', 'ProductController@productStockAdd');
        $router::post('/productStockSubmit', 'ProductController@productStockSubmit');
        $router::post('/productEditSubmit', 'ProductController@update');
        $router::get('/productList', 'ProductController@show');
        $router::post('/productDelete', 'ProductController@destroy');
        $router::get('/productSupply', 'ProductController@productSupply');
        $router::post('/getPurchaseHistoryByDate', 'ProductController@getPurchaseHistoryByDate');
        $router::get('/addStock', 'ProductController@addStock');

        /* category setup */
        $router::get('/CategoryForm', 'CategoryController@categorySetup');
        $router::post('/categoryCodeValidation', 'CategoryController@categoryCodeValidation');
        $router::post('/categorySubmit', 'CategoryController@create');
        $router::get('/categoryEdit', 'CategoryController@edit');
        $router::post('/categoryEditSubmit', 'CategoryController@update');
        $router::get('/categoryList', 'CategoryController@show');
        $router::post('/categoryDelete', 'CategoryController@destroy');
        // $router::get('/productSupply', 'ProductController@productSupply');
        // $router::post('/getPurchaseHistoryByDate', 'ProductController@getPurchaseHistoryByDate');
        // $router::get('/addStock', 'ProductController@addStock');

        /* Expense Setup */
        $router::view('expenseForm', 'expenseSetup');
        $router::post('/expenseCodeValidation', 'ExpenseController@expenseCodeValidation');
        $router::post('/expenseSubmit', 'ExpenseController@create');
        $router::get('/expenseEdit', 'ExpenseController@edit');
        $router::post('/expenseEditSubmit', 'ExpenseController@update');
        $router::get('/expenseList', 'ExpenseController@show')->name("expenseList");
        $router::post('/expenseDelete', 'ExpenseController@destroy');

        /* milk plant setup */
        $router::get('/milkPlantForm', 'MilkPlantController@milkPlantForm');
        $router::post('/checkMilkPlantEmail', 'MilkPlantController@checkMilkPlantEmail');
        $router::post('/checkMilkPlantContactNumberValidation', 'MilkPlantController@checkMilkPlantContactNumberValidation');
        $router::post('/milkPlantSubmit', 'MilkPlantController@create');
        $router::get('/milkPlantList', 'MilkPlantController@show');
        $router::post('/milkPlantRemove', 'MilkPlantController@destroy');
        $router::get('/milkPlantEdit', 'MilkPlantController@edit');
        $router::post('/milkPlantEditEmail', 'MilkPlantController@milkPlantEditEmail');
        $router::post('/milkPlantEditContactNumber', 'MilkPlantController@milkPlantEditContactNumber');
        $router::post('/milkPlantEditSubmit', 'MilkPlantController@update');
        $router::post('getChildMilkPlants', "MilkPlantController@getChildMilkPlants");

        /* Utility Setup */
        $router::get('/utilitySetupForm', 'UtilitySetupController@utilitySetupForm');
        $router::post('/portSubmit', 'UtilitySetupController@create');
        $router::get('/portEdit', 'UtilitySetupController@show');
        $router::post('/portEditSubmit', 'UtilitySetupController@update');
        $router::post('/utilityDelete', 'UtilitySetupController@destroy');
        $router::get('/utilityList', 'UtilitySetupController@utilityList');

        /* data backup */
        $router::get('/dataBackupForm', 'DataBackupController@dataBackupForm');
        $router::post('/dataBackupSubmit', 'DataBackupController@create');
        $router::get('/dataBackupEdit', 'DataBackupController@show');
        $router::post('/dataBackupEditSubmit', 'DataBackupController@update');
        $router::get('/dataBackupListDay', 'DataBackupController@dataBackupListDay');
        $router::get('/dataBackupListWeek', 'DataBackupController@dataBackupListWeek');
        $router::get('/dataBackupListMonth', 'DataBackupController@dataBackupListMonth');
        $router::post('/dataBackupDelete', 'DataBackupController@destroy');

        /* Daily Transaction */
        $router::get('/DailyTransactionForm', 'DailyTransactionController@DailyTransactionForm');
        $router::post('/DailyTransactionMemberCode', 'DailyTransactionController@DailyTransactionMemberCode');
        $router::post('/DailyTransactionMemberName', 'DailyTransactionController@DailyTransactionMemberName');
        $router::post('/DailyTransactionSubmit', 'DailyTransactionController@create');

        $router::get('/DailyTransactionList', 'DailyTransactionController@DailyTransactionList');


        $router::get('/ProductSale_deleted', 'DailyTransactionController@deleted_product_sale');


        $router::get('/DailyTransactionPsf', 'DailyTransactionController@DailyTransactionPsf');
        $router::get('/DailyTransactionEdit', 'DailyTransactionController@show');
        $router::post('/DailyTransactionEditSubmit', 'DailyTransactionController@update');
        $router::post('/DailyTransactionDelete', 'DailyTransactionController@DailyTransactionDelete');
        $router::get('/DailyTransactionResendNoti', 'DailyTransactionController@DailyTransactionResendNoti');

        $router::post('/dailyTransactionListAjax', 'DailyTransactionController@DailyTransactionListAjax');
        $router::get('/dailyTransactionListAjax_delete', 'DailyTransactionController@DailyTransactionListAjax_delete');

        

        $router::post('/updateTransaction', 'DailyTransactionController@updateTransaction');

        // $router::get("download-pdf","HomeController@downloadPDF");

        /* Sales */

        /* Local Sales */
        $router::get('/localSaleForm', 'SalesController@SaleForm');
        $router::get('/localSaleForm_delete', 'SalesController@SaleForm_delete');


        $router::get('/memberSaleForm', 'SalesController@memberSaleForm');
        $router::post('/localSaleFormSubmit', 'SalesController@localSaleFormSubmit');
        $router::post('/productSaleFormSubmit', 'SalesController@productSaleFormSubmit');
        $router::post('/getUserNameByledger', 'SalesController@getUserNameByledger');
        $router::post('/getLedgerIdByName', 'SalesController@getLedgerIdByName');
        $router::get('/saleList', 'SalesController@saleList');
        $router::post('/localSaleEditSubmitAj', 'SalesController@localSaleEditSubmitAj');
        $router::post('/productSaleEditSubmitAj', 'SalesController@productSaleEditSubmitAj');

        $router::post('/deleteSaleAjax', 'SalesController@deleteSaleAjax');

        $router::get('/getProductSaleAjax', 'SalesController@getProductSaleAjax');
        $router::get('/getProductSaleAjax_delete', 'SalesController@getProductSaleAjax_delete');

        
        $router::get('/getLocalSaleAjax', 'SalesController@getLocalSaleAjax');
        $router::get('/getLocalSaleAjaxDelete', 'SalesController@getLocalSaleAjaxDelete');


        $router::get('/getPlantSaleAjax', 'SalesController@getPlantSaleAjax');

        $router::POST("/getSaleDetails", "SalesController@getSaleDetails");


        $router::POST('/getProductUnit', 'SalesController@getProductUnit');
        $router::POST('member/getProductUnit', 'member\MemberController@getProductUnit');

        /* Plant Sale */
        $router::get('/plantSaleForm', 'SalesController@plantSaleForm');
        $router::post('/plantSaleFormSubmit', 'SalesController@plantSaleFormSubmit');

        // new plant sale


        $router::post('/plantSaleListAjax',  'SalesController@plantSaleListAjax');
        $router::post('/getPlantSaleValues', 'SalesController@getPlantSaleValues');
        $router::post('/updatePlantSale',    'SalesController@updatePlantSale');
        $router::post('/deletePlantSale',    'SalesController@deletePlantSale');

        // plant new rate card
        Route::post('plantSaleRateCardValue', [SalesController::class, 'plantSaleRateCardValue']);
        // plant rate card 

// ── Plant Rate Card management new wala ──────────────────────────────────────────────
Route::get ('plantRateCardShow',    'PlantRateCardController@plantRateCardShow');
Route::get ('plantRateCardNew',     'PlantRateCardController@plantRateCardNew');
Route::post('plantRateCardSave',    'PlantRateCardController@plantRateCardSave');
Route::post('plantRateCardGetList', 'PlantRateCardController@plantRateCardGetList');
Route::post('plantRateCardUpdate',  'PlantRateCardController@plantRateCardUpdate');
Route::post('plantRateCardDelete',  'PlantRateCardController@plantRateCardDelete');
Route::get ('plantRateCardApply',   'PlantRateCardController@plantRateCardApply');
Route::post('plantRateCardPdf',     'PlantRateCardController@plantRateCardPdf');

// ── Rate lookup called by plantSale.blade.php ───────────────────────────────
Route::post('plantSaleRateCardValue', 'PlantRateCardController@plantSaleRateCardValue');

        /* member sale */
        // $router::get('/memberSaleForm', 'SalesController@memberSaleForm');
        // $router::post('/memberSaleFormSubmit', 'SalesController@memberSaleFormSubmit');

        /* purchase setup */
        $router::get('/purchaseForm', 'PurchaseSetupController@purchaseForm');
        $router::post('/getSupplierName', 'PurchaseSetupController@getSupplierName');
        $router::post('/purchaseFormSubmit', 'PurchaseSetupController@create');
        $router::get('/purchaseList', 'PurchaseSetupController@purchaseList');
        //DailyTransactionSupplierCode

        /* expense setup */
        $router::get('/expenseSetupForm', 'ExpenseSetupController@expenseForm');
        $router::post('/expenseFormSubmit', 'ExpenseSetupController@create');
        $router::get('/expenseSetupList', 'ExpenseSetupController@expenseSetupList');
        $router::get('/expenseTypeSetup', 'ExpenseSetupController@expenseTypeForm');

        /* payment setup */
        $router::get('/paymentForm', 'PaymentController@paymentForm');
        $router::post('/paymentFormSubmit', 'PaymentController@create');
        $router::get('/paymentList', 'PaymentController@paymentList');

        /* Reprots */
        $router::get('/getReport', 'ReprotsController@report');
        // $router::get('/getReport', function(){
        //     return "<h2>This page is in maintenance mode.</h2>";
        // });

        /* sale report */
        $router::post('/getSaleReport', 'ReprotsController@getSaleReport');
        $router::post('/getSaleReportPdf', 'ReprotsController@getSaleReportPdf');

        /* get member report */
        $router::post('/getMemberReport', 'ReprotsController@getMemberReport');
        $router::post('/getMemberReportPdf', 'ReprotsController@getMemberReportPdf');

        /* get shift report */
        $router::post('/getShiftReport', 'ReprotsController@getShiftReport');
        $router::post('/getShiftReportPdf', 'ReprotsController@getShiftReportPdf');

        /* get member passbook report */
        $router::post('/getMemberPassbookReport', 'ReprotsController@getMemberPassbookReport');
        $router::post('/getMemberPassbookReportPdf', 'ReprotsController@getMemberPassbookReportPdf');

        $router::post('/getMemStatementReport', 'ReprotsController@getMemStatementReport');
        $router::post('/getMemStatementReport2', 'ReprotsController@getMemStatementReport2');
        $router::post('/getCustomerSalseReport', 'ReprotsController@getCustomerSalseReport');

        /* get balance sheet report */
        $router::post('/getBalanceSheetReport', 'ReprotsController@getBalanceSheetReport');
        $router::post('/getBalanceSheetReportPdf', 'ReprotsController@getBalanceSheetReportPdf');

        /* get ledger report */
        $router::post('/getLedgerReport', 'ReprotsController@getLedgerReport');
        $router::post('/getLedgerReportPdf', 'ReprotsController@getLedgerReportReportPdf');

        /* get cm subsidiary report */
        $router::post('/getCmSubsidiaryReport', 'ReprotsController@getCmSubsidiaryReport');

        $router::post('/getRateCardReport', 'ReprotsController@getRateCardReport');
        $router::post('/getProfitLossReport', 'ReprotsController@getProfitLossReport');
        $router::post('/getSupplierReport', 'ReprotsController@getSupplier');

        /* role Setup */
        $router::get('/roleSetupForm', 'RolesSetupController@roleSetupForm');
        $router::post('/roleSetupFormSubmit', 'RolesSetupController@create');
        $router::post('/roleList', 'RolesSetupController@roleList');

        /* other user setup */
        $router::get('/otherUserForm', 'OtherUserController@otherUsuerForm');
        $router::get('/checkOtherUserEmail', 'OtherUserController@checkOtherUserEmail');
        $router::post('/otherUserSubmit', 'OtherUserController@create');
        $router::get('/otherUserList', 'OtherUserController@otherUserList');
        $router::get('/otherUserEdit', 'OtherUserController@show');
        $router::post('/otherUserEditSubmit', 'OtherUserController@otherUserEditSubmit');
        $router::get('/UserEditEmailValidation', 'OtherUserController@UserEditEmailValidation');
        $router::get('/otherUserDelete', 'OtherUserController@destroy');

        // $router::get('/supplierEdit', 'SupplierController@show');
        // $router::post('/supplierEditSubmit', 'SupplierController@update');
        // $router::get('/supplierEditEmailValidation', 'SupplierController@supplierEditEmailValidation');
        // $router::get('/supplierList','SupplierController@supplierList');
        // $router::post('/deleteSupplier','SupplierController@destroy');

        /* get admin menu */
        $router::get('/adminMenuForm', 'AdminMenuController@adminMenuForm');
        $router::post('/adminMenuFormSubmit', 'AdminMenuController@create');

        /* dairy admin dashbor */
        $router::get('DairyAdminDashbord', 'DairyAdminDashbordController@dairyData');
        $router::post('getMilkCollactionData', 'DairyAdminDashbordController@getMilkCollactionData');
        $router::post('todaySale', 'DairyAdminDashbordController@getTodaySale');
        $router::post('memberByAmount', 'DairyAdminDashbordController@memberByAmount');
        $router::post('monthlyMilkCollaction', 'DairyAdminDashbordController@monthlyMilkCollaction');
        Route::get('/export/member/report', 'ReprotsController@exportMemberReport');

        Route::get('/export/member/report/pdf', 'ReprotsController@exportMemberReportPdf');

        

        $router::get('dairy-settings', 'DairyAdminDashbordController@dairy_settings');

    });

    /* Super Admin */
    $router->group(['middleware' => 'SuperAdmin'], function () use ($router) {

        $router::get('dms-admin-dashbord', 'SuperAdminController@showDashbord');

    });

    /* Sidebar Menu */
    Route::post('/dairy-info/sidebarMenu', 'UserController@sidebarMenu');

    /* report card  */
    Route::post('/dairy-info/getSaleReport', 'ReprotsController@getSaleReport');
    Route::post('/dairy-info/getAllParty', 'ReprotsController@getAllParty');

    Route::post('/dairy-info/getMemberReport', 'ReprotsController@getMemberReport');
    Route::post('/dairy-info/getShiftReport', 'ReprotsController@getShiftReport');
    Route::post('/dairy-info/getMemberPassbookReport', 'ReprotsController@getMemberPassbookReport');
    Route::post('/dairy-info/getBalanceSheetReport', 'ReprotsController@getBalanceSheetReport');
    Route::post('/dairy-info/getLedgerReport', 'ReprotsController@getLedgerReport');

    /* payment setup */
    Route::post('/dairy-info/paymentFormSubmit', 'PaymentController@Apicreate');
    Route::post('/dairy-info/paymentList', 'PaymentController@paymentList');

    /* expense setup */
    Route::post('/dairy-info/expenseFormSubmit', 'ExpenseSetupController@createApi');
    Route::post('/dairy-info/expenseSetupList', 'ExpenseSetupController@expenseSetupList');

    /* purchase setup */
    Route::post('/dairy-info/getSupplierName', 'PurchaseSetupController@getSupplierName');
    Route::post('/dairy-info/purchaseFormSubmit', 'PurchaseSetupController@createApi');
    Route::post('/dairy-info/purchaseList', 'PurchaseSetupController@purchaseListApi');

    /* Sales */
    Route::post('/dairy-info/saleList', 'apiSales@saleList');
    Route::post('/dairy-info/getMilkPlant', 'apiSales@getMilkPlant');

    /* Local Sales */
    Route::post('/dairy-info/localSaleFormSubmit', 'apiSales@localSaleFormSubmit');
    Route::post('/dairy-info/localSaleList', 'apiSales@localSaleList');
    Route::post('/dairy-info/getSalaFormPreFillData', 'apiSales@getSalaFormPreFillData');

    /* Plant Sale */
    Route::post('/dairy-info/milkPlantList', 'apiSales@milkPlantList');
    Route::post('/dairy-info/plantSaleFormSubmit', 'apiSales@plantSaleFormSubmit');

    /* member sale */
    Route::post('/dairy-info/memberSaleFormSubmit', 'apiSales@memberSaleFormSubmit');

    /* Daily Transaction Member api */
    Route::post('/dairy-info/DailyTransactionMemberCode', 'apiDailyTransactions@DailyTransactionMemberCode');
    Route::post('/dairy-info/DailyTransactionSubmit', 'apiDailyTransactions@create');
    Route::post('/dairy-info/DailyTransactionList', 'apiDailyTransactions@DailyTransactionList');

    // Route::post('/portEditSubmit', 'UtilitySetupController@update');
    // Route::post('/utlityDelete', 'UtilitySetupController@destroy');

    /* rate card by fat */
    Route::post('/addFatSnfRange', 'RateCardController@addFatSnfRange');
    Route::post('/getFatSnfRangeTable', 'RateCardController@getFatSnfRangeTable');
    Route::post('/fatSnfRangeTableUpdate', 'RateCardController@fatSnfRangeTableUpdate');

    /* rate by fat and snf */
    Route::post('/dairy-info/getFatSnfRangeData', 'RateCardController@getFatSnfRangeDataApi');
    Route::post('/dairy-info/getFatSnfRangeDataEdit', 'RateCardController@getFatSnfRangeDataEdit');

    /* product setup */
    Route::view('/dairy-info/ProductForm', 'productSetup');
    Route::post('/dairy-info/productCodeValidation', 'ProductController@productCodeValidation');
    Route::post('/dairy-info/productSubmit', 'ProductController@apiCreate');
    Route::get('/dairy-info/productEdit', 'ProductController@apiEdit');
    Route::post('/dairy-info/productEditSubmit', 'ProductController@apiUpdate');
    Route::post('/dairy-info/productList', 'ProductController@Apishow');
    Route::post('/dairy-info/productDelete', 'ProductController@apiDestroy');

    /* Expense Setup */
    Route::view('/dairy-info/expenseForm', 'expenseSetup');
    Route::post('/dairy-info/expenseCodeValidation', 'ExpenseController@expenseCodeValidation');
    Route::post('/dairy-info/expenseSubmit', 'ExpenseController@apiCreate');
    Route::get('/dairy-info/expenseEdit', 'ExpenseController@edit');
    Route::get('/dairy-info/expenseEditSubmit', 'ExpenseController@apiUpdate');
    Route::post('/dairy-info/expenseList', 'ExpenseController@Apishow');
    Route::post('/dairy-info/expenseDelete', 'ExpenseController@destroy');

    /* milk plant setup */
    Route::post('/dairy-info/milkPlantSubmit', 'ApiMilkPlantController@create');
    Route::post('/dairy-info/milkPlantList', 'ApiMilkPlantController@show');
    Route::post('/dairy-info/milkPlantDelete', 'ApiMilkPlantController@destroy');
    Route::post('/dairy-info/milkPlantEditSubmit', 'ApiMilkPlantController@update');

    /* api */
    Route::post('/dairy-info/allStatesList', 'ApiDairyAdminControler@dairyAdminCreate');
    Route::post('/dairy-info/city-by-state', 'ApiDairyAdminControler@CityByStateId');
    Route::get('/dairy-info/csrf', 'ApiDairyAdminControler@getcsrf');
    Route::post('/dairy-info/register', 'ApiDairyAdminControler@dairyRegister');
    Route::post('/dairy-info/editDairy', 'ApiDairyAdminControler@editDairy');
    Route::post('/dairy-info/DairyInfo', 'ApiDairyAdminControler@getDairyInfo');
    Route::post('/dairy-info/registerMember', 'ApiDairyAdminControler@registerMember');
    Route::post('/dairy-info/getAllMember', 'ApiDairyAdminControler@getAllMember');
    Route::post('/dairy-info/editMember', 'ApiDairyAdminControler@editMember');
    Route::post('/dairy-info/deleteMember', 'ApiDairyAdminControler@deleteMember');

    /* supplier */
    Route::post('/dairy-info/supplierSubmit', 'ApiSupplierController@create');
    Route::post('/dairy-info/editSupplier', 'ApiSupplierController@update');
    Route::post('/dairy-info/deleteSupplier', 'ApiSupplierController@destroy');
    Route::post('/dairy-info/supplierList', 'ApiSupplierController@supplierList');

    /* rate card */
    /* by fat */
    Route::post('/dairy-info/fatRateCard', 'RateCardController@fatRateCardSubmit');
    Route::post('/dairy-info/submitFatPriceEdit', 'RateCardController@submitFatPriceEdit');
    //$router::post('/submitFatPriceEdit', 'RateCardController@submitFatPriceEdit');

    /* Customer setup */
    Route::post('/dairy-info/customerSubmit', 'ApiCustomerController@create');
    Route::post('/dairy-info/customerList', 'ApiCustomerController@customerList');
    Route::post('/dairy-info/editCustomerSubmit', 'ApiCustomerController@update');
    Route::post('/dairy-info/deleteCustomer', 'ApiCustomerController@destroy');

    Route::get('/advanceForm', 'AdvanceCreditController@advanceForm');
    Route::post('/advanceSubmit', 'AdvanceCreditController@advanceSubmit');
    Route::get('/getAdvanceData', 'AdvanceCreditController@getAdvanceData');
    Route::post('/getValidateAdCredUser', 'AdvanceCreditController@validateUser');

    Route::get('/creditForm', 'AdvanceCreditController@creditForm');
    Route::post('/creditSubmit', 'AdvanceCreditController@creditSubmit');
    Route::get('/getCreditData', 'AdvanceCreditController@getCreditData');

    Route::get("memberDetailDash", "DairyAdminDashbordController@memberDetailDash");
    Route::get("customerDetailDash", "DairyAdminDashbordController@customerDetailDash");
    Route::get("suppDetailDash", "DairyAdminDashbordController@suppDetailDash");


    Route::get('/ratechart', 'RateCardController@rateChart');
    Route::get('/memberProfile', 'ReprotsController@memberProfile');

    Route::post('/checkDairy', 'DairyAdminDashbordController@checkDairy');
    Route::post('/updateCurrentDairyBal', 'DairyAdminDashbordController@updateCurrentDairyBal');
    Route::post('/getUserDetail', 'DailyTransactionController@getUserDetail');
    Route::get("/milkRequest", "DailyTransactionController@milkRequest");
    Route::get("/prodRequest", "DailyTransactionController@prodRequest");

    Route::post("/milkPlantAddRequest", "MilkPlantController@milkPlantAddRequest");

    Route::get("dairy/checkNotification", "DairyAdminDashbordController@checkNotification");
    Route::get("dairy/deleteNotification", "DairyAdminDashbordController@deleteNotification");

    Route::post("/requestComplete", "DailyTransactionController@requestComplete");
    Route::post("/getReqs", "DailyTransactionController@getReqs");
    Route::post('/notifSubmit', 'DailyTransactionController@notifSubmit');
    Route::post('/assignReq', 'DailyTransactionController@assignReq');

    Route::get("/dairyBal", "DairyAdminDashbordController@dairyBal");
    Route::get('/getDailryBalData', 'DairyAdminDashbordController@getDailryBalData');
    Route::post("/submitCashUpdate", "DairyAdminDashbordController@submitCashUpdate");
    Route::post("/submitCashEditUpdate", "DairyAdminDashbordController@submitCashEditUpdate");
    Route::post("/dairyBalOtherDetails", "DairyAdminDashbordController@dairyBalOtherDetails");
    Route::post("/getLastCashEdit", "DairyAdminDashbordController@getLastCashEdit");

    Route::get('sa', 'HomeController@superAdminLogin');
    Route::post('sa/login', 'HomeController@saLoginFormSubmit');

    Route::get('sa/dashboard', 'SuperAdminController@showDashbord');
    Route::get('sa/dairysetupwizard', 'SuperAdminController@dairySetup');
    Route::post('sa/addDairyAdminSubmit', 'SuperAdminController@create');
    Route::get('sa/dairyList', 'SuperAdminController@dairyList');
    Route::get('sa/pricePlan', 'SuperAdminController@pricePlan');
    Route::post('sa/createPricePlan', 'SuperAdminController@createPricePlan');
    Route::get('sa/milkPlants', 'SuperAdminController@showMilkPlants');
    Route::get('sa/addNewPlant', 'SuperAdminController@addNewPlant');
    Route::post('sa/newMilkPlantAdd', 'SuperAdminController@newMilkPlantAdd');
    Route::post('sa/milkPlantDelete', 'SuperAdminController@milkPlantDelete');
    Route::get('sa/appSettings', 'SuperAdminController@appSettings');
    Route::post('sa/updateAppSetting', "SuperAdminController@updateAppSetting");

    Route::post('getPricePlanDetails', 'HomeController@getPricePlanDetails');
    Route::get('buy', 'HomeController@buy');
    Route::post('registerNewDairy', 'HomeController@registerNewDairy');



    Route::post('sa/deactivateDairy', 'SuperAdminController@deactivateDairy');
    Route::get('sa/deleteDairyCompletely', 'SuperAdminController@DeleteDairyCompletely');

    Route::get('sa/pay', 'SuperAdminController@pay');
    Route::POST('sa/saveDairyAndPay', 'SuperAdminController@saveDairyAndPay');


    Route::get("colMans", "ColmanController@colMans");
    Route::get("newColMan", "ColmanController@newColMan");
    Route::POST("createColMan", "ColmanController@createColMan");
    Route::get("colMansUserName", "ColmanController@colMansUserName");
    Route::get("colManMobileNumberValidation", "ColmanController@colManMobileNumberValidation");

    Route::get('member/dashboard', 'member\MemberController@dashboard');
    Route::post('member/getMilkCollactionAjax', 'member\MemberController@getMilkCollactionAjax');
    Route::get('member/collectionHistory', 'member\MemberController@collectionHistory');
    Route::post('member/dailyTransactionListAjax', 'member\MemberController@dailyTransactionListAjax');
    Route::get('member/purchaseHistory', 'member\MemberController@purchaseHistory');
    Route::post('member/purchaseHistoryListAjax', 'member\MemberController@purchaseHistoryListAjax');
    Route::get('member/payments', 'member\MemberController@payments');
    Route::post('member/paymentListAjax', 'member\MemberController@paymentListAjax');
    Route::get('member/statement', 'member\MemberController@statement');
    Route::post('member/statementListAjax', 'member\MemberController@statementListAjax');
    Route::post('member/monthlyMilkCollaction', 'member\MemberController@monthlyMilkCollaction');
    Route::get('member/notification', 'member\MemberController@notification');
    Route::post("member/sendReq", "member\MemberController@sendReq");
    Route::get("member/changePassword", "member\MemberController@changePassword");


    Route::get('supplier/dashboard', 'supplier\SupplierController@dashboard');

    Route::get('customer/dashboard', function () {return redirect("customer/purchaseHistory");});
    Route::get('customer/purchaseHistory', 'customer\CustomerController@purchaseHistory');
    Route::get('customer/payments', 'customer\CustomerController@payments');
    Route::post('customer/paymentListAjax', 'customer\CustomerController@paymentListAjax');
    Route::post('customer/purchaseHistoryListAjax', 'customer\CustomerController@purchaseHistoryListAjax');

    Route::get('printSlipTrans', 'DailyTransactionController@printSlipTrans');

    Route::get('dairyDetails', 'DairyAdminController@dairyDetails');
    Route::POst('updateDairyDetails', 'DairyAdminController@updateDairyDetails');
    Route::get('change_password', 'HomeController@change_password');
    Route::post('checkPassword', 'HomeController@checkPassword');
    Route::POST('setNewPassword', 'HomeController@setNewPassword');

    Route::POST('saveDairyAndPay2', 'HomeController@saveDairyAndPay2');
    Route::POST('ccavRequestHandler', 'HomeController@ccavRequestHandler');
    Route::POST('ccavResponseHandler', 'HomeController@ccavResponseHandler');
    Route::POST('proceedToCheckOut', 'HomeController@proceedToCheckOut');

    //Plant Route
    //Plants
    Route::get("plant/dashboard", "PlantController@dashboard");
    Route::get("plant/dairies", "PlantController@dairies");

    Route::get("plant/alldairies", "PlantController@subplant_dairies");//new
    Route::get("plant/dairyByPlantId", "PlantController@dairyByPlantId");//new

    Route::get("plant/getDashboardData", "PlantController@getDashboardData");//new

    //plant Member



    Route::get("plant/allmember", "PlantController@allmember");//new
    Route::post("plant/memberByDairy", "PlantController@memberByDairy");//new




    Route::get("plant/paymentRegister", "PlantController@payment_register");//new
    Route::post("plant/paymentByDairy", "PlantController@paymentByDairy");//new
    Route::get("plant/paymentRegisterPdf", "PlantController@getPaymentRegisterReportPdf");//new

    //plant shift summary

    Route::get('plant/shift_summary','PlantController@shift_summary');//new

    Route::post('plant/shiftSummaryByDairy','PlantController@ShiftSummaryByDairy');//new



    //Route Cm Subsidiary
    Route::get('plant/cm_subsidiary','PlantController@cm_subsidiary');//new
    Route::post('plant/cm_subsidiaryByDairy','PlantController@cm_subsidiaryByDairy');//new

    Route::get('plant/paymentDemo','PlantController@demo');

    Route::get("plant/requestToAdd", "PlantController@requestToAdd");
    Route::post("plant/plantAddRequestComplete", "PlantController@plantAddRequestComplete");
    Route::get("plant/checkNotification", "PlantController@checkNotification");
    Route::get("plant/deleteNotification", "PlantController@deleteNotification");
    Route::get("plant/plants", "PlantController@plants");



    //Head Plant

    Route::get("headPlant/dashboard", "HeadPlantController@dashboard");
    Route::get("headPlant/dairies", "HeadPlantController@dairies");
    Route::get("headPlant/plants", "HeadPlantController@plants");
    Route::get("headPlant/alldairies", "HeadPlantController@subplant_dairies");//new
    Route::get("headPlant/dairyByPlantId", "HeadPlantController@dairyByPlantId");//new
    Route::post("headPlant/getDairies_by_plant", "HeadPlantController@getDairies_by_plant");//new

    //plant Member

    Route::get("headPlant/allmember", "HeadPlantController@allmember");//new
    Route::post("headPlant/memberByDairy", "HeadPlantController@memberByDairy");//new

    //plant payment register
    Route::get("headPlant/paymentRegister", "HeadPlantController@paymentRegister");//new member
    Route::get("headPlant/payment_register", "HeadPlantController@payment_register");//new
    Route::post("headPlant/paymentByDairy", "HeadPlantController@paymentByDairy");//new
    Route::get("headPlant/paymentRegisterPdf", "HeadPlantController@getPaymentRegisterReportPdf");//new

    //plant shift summary

    Route::get('headPlant/shift_summary','HeadPlantController@shift_summary');//new

    Route::post('headPlant/shiftSummaryByDairy','HeadPlantController@ShiftSummaryByDairy');//new



    //Route Cm Subsidiary
    Route::get('headPlant/cm_subsidiary','HeadPlantController@cm_subsidiary');//new
    Route::post('headPlant/cm_subsidiaryByDairy','HeadPlantController@cm_subsidiaryByDairy');//new


    //End

    Route::POST("markasReadNotification", "HomeController@markasReadNotification");

    Route::get("checkSubscription", "HomeController@checkSubscription");
    Route::get("renewPage", "HomeController@renewPage");
    Route::get("expiredPage", "HomeController@expiredPage");
    Route::get("subscriptionHistory", "HomeController@subscriptionHistory");



    Route::post('getPDFfromHTML', 'HomeController@getPDFfromHTML');

    Route::get('downloadFiles', 'HomeController@download');
    Route::get('createInvoice', 'HomeController@createInvoice');



    Route::get("termsCond", "HomeController@termsCond");
    Route::get("privacyPolicy", "HomeController@privacyPolicy");
    Route::get("contactUs", "HomeController@contactUs");
    Route::get("aboutUs", "HomeController@aboutUs");

    Route::post("contactMail", "HomeController@contactMail");



    Route::get("testSms", "HomeController@testBulkSms");
    // Route::get("testInvoicePdf", "HomeController@testInvoicePdf");



    Route::get("/refund","HomeController@refund");
    Route::get("/disclaimer","HomeController@disclaimer");