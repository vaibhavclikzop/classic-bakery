<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AdvanceOrder;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Authentication;
use App\Http\Controllers\BulkImport;
use App\Http\Controllers\FinishProducts;
use App\Http\Controllers\InwardStock;
use App\Http\Controllers\LeadManagement;
use App\Http\Controllers\Masters;
use App\Http\Controllers\OrderManagement;
use App\Http\Controllers\Outlet;
use App\Http\Controllers\OutwardStock;
use App\Http\Controllers\PurchaseReturn;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\Reports;
use App\Http\Controllers\ResetSoftware;
use App\Http\Controllers\SaleReturn;
use App\Http\Controllers\StockReport;
use App\Http\Controllers\Barcode;
use App\Http\Controllers\Email\cancelInvoiceController;
use App\Http\Controllers\Email\sendOTPController;
use App\Http\Controllers\expenseManagement;
use App\Http\Controllers\posOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Reports\OperationsReport;
use App\Http\Controllers\Reports\SaleReportController;
use App\Http\Controllers\TallyController;
use App\Mail\SendCancelInvoiceOTP;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/Clear', function () {

  $clearcache = Artisan::call('cache:clear');
  echo "Cache cleared<br>";

  $clearview = Artisan::call('view:clear');
  echo "View cleared<br>";

  $clearconfig = Artisan::call('config:cache');
  echo "Config cleared<br>";
});

Route::get('reset-software/{key}', [ResetSoftware::class, 'ResetSoftware'])->name('reset-software');
Route::post('ResetSoftware', [ResetSoftware::class, 'ResetSoft'])->name('ResetSoftware');


Route::get('/', [Authentication::class, 'SuperAdmin'])->name('/');
Route::post('/', [Authentication::class, 'SuperAdminLogin'])->name('SuperAdminLogin');

Route::group(['middleware' => ['SuperAdmin']], function () {

  Route::get('settings', [Masters::class, 'settings'])->name('settings');
  Route::post('SaveSettings', [Masters::class, 'SaveSettings'])->name('SaveSettings');
  Route::get('logout', [Authentication::class, 'logout'])->name('Logout');
  Route::get('dashboard', [Admin::class, 'Dashboard'])->name('dashboard');
  Route::post('GetUserDetails', [Masters::class, 'GetUserDetails'])->name('GetUserDetails');

  // ajax call
  //Email 
  Route::post('sendCancelInvoiceOTP', [cancelInvoiceController::class, "sendCancelInvoiceOTP"])->name("sendCancelInvoiceOTP");
  Route::post('verifyCancelOTP', [cancelInvoiceController::class, "verifyCancelOTP"])->name("verifyCancelOTP");
  Route::post('cancelRegularInvoice', [cancelInvoiceController::class, "cancelRegularInvoice"])->name("cancelRegularInvoice");

  Route::post('sendStockUpdateOTP', [sendOTPController::class, "sendStockUpdateOTP"])->name("sendStockUpdateOTP");
  Route::post('updateOutletStock', [sendOTPController::class, "updateOutletStock"])->name("updateOutletStock");
  Route::post('sendDeleteDuplicateOTP', [sendOTPController::class, "sendDeleteDuplicateOTP"])->name("sendDeleteDuplicateOTP");
  Route::post('deleteOutletCSDuplicate', [sendOTPController::class, "deleteOutletCSDuplicate"])->name("deleteOutletCSDuplicate");


  Route::post('cancelOrderOTP', [sendOTPController::class, "cancelOrderOTP"])->name("cancelOrderOTP");

  Route::post('CancelOrder', [OrderManagement::class, 'CancelOrder'])->name('CancelOrder');

  Route::post('cancelPurchaseInvoiceOTP', [sendOTPController::class, "cancelPurchaseInvoiceOTP"])->name("cancelPurchaseInvoiceOTP");

  Route::post('cancelPurchaseInvoice', [sendOTPController::class, "cancelPurchaseInvoice"])->name("cancelPurchaseInvoice");
  Route::post('updateOrderOTP', [sendOTPController::class, "updateOrderOTP"])->name("updateOrderOTP");
  Route::post('sendUnAllocateOTP', [sendOTPController::class, "sendUnAllocateOTP"])->name("sendUnAllocateOTP");
  Route::post('unAllocateProducts', [sendOTPController::class, "unAllocateProducts"])->name("unAllocateProducts");

  Route::post('sendCancelAdvanceOTP', [cancelInvoiceController::class, "sendCancelAdvanceOTP"])->name("sendCancelAdvanceOTP");
  Route::post('/verifyCancelAdvanceOrderOTP', [cancelInvoiceController::class, 'verifyCancelAdvanceOrderOTP'])->name("verifyCancelAdvanceOrderOTP");


  //ajax call 


  Route::post('getLastPurchasePriceRM', [AjaxController::class, 'getLastPurchasePriceRM'])->name('getLastPurchasePriceRM');
  Route::post('getLastPurchasePriceFG', [AjaxController::class, 'getLastPurchasePriceFG'])->name('getLastPurchasePriceFG');


  Route::post('GetCity', [Masters::class, 'GetCity'])->name('GetCity');
  Route::post('GetCategory', [Masters::class, 'GetCategory'])->name('GetCategory');
  Route::post('GetSubCategory', [Masters::class, 'GetSubCategory'])->name('GetSubCategory');
  Route::post('GetFinishSubCategory', [Masters::class, 'GetFinishSubCategory'])->name('GetFinishSubCategory');
  Route::post('GetProductFinish', [Masters::class, 'GetProductFinish'])->name('GetProductFinish');
  Route::post('GetProducts', [Masters::class, 'GetProducts'])->name('GetProducts');
  Route::post('GetVendorProducts', [InwardStock::class, 'GetVendorProducts'])->name('GetVendorProducts');
  Route::post('GetTeamMember', [Masters::class, 'GetTeamMember'])->name('GetTeamMember');
  Route::post('GetRawMaterial', [InwardStock::class, 'GetRawMaterial'])->name('GetRawMaterial');


  Route::post('StartDay', [Admin::class, 'StartDay'])->name('StartDay');
  Route::post('EndDay', [Admin::class, 'EndDay'])->name('EndDay');
  Route::get('profile', [Admin::class, 'Profile'])->name('profile');
  Route::post('SaveProfile', [Admin::class, 'SaveProfile'])->name('SaveProfile');
  Route::post('GetCSProducts', [StockReport::class, 'GetCSProducts'])->name('GetCSProducts');
  Route::post('GetCustomerOutlet', [Masters::class, 'GetCustomerOutlet'])->name('GetCustomerOutlet');
  // master route
  Route::get('company', [Masters::class, 'Company'])->name('company');
  Route::post('SaveCompany', [Masters::class, 'SaveCompany'])->name('SaveCompany');

  Route::get('customers', [Masters::class, 'Customers'])->name('customers');
  Route::post('SaveCustomer', [Masters::class, 'SaveCustomer'])->name('SaveCustomer');

  Route::get('vendor-type', [Masters::class, 'VendorType'])->name('vendor-type');
  Route::post('SaveVendorType', [Masters::class, 'SaveVendorType'])->name('SaveVendorType');


  Route::get('vendor', [Masters::class, 'Vendor'])->name('vendor');
  Route::post('SaveVendor', [Masters::class, 'SaveVendor'])->name('SaveVendor');

  Route::get('vendor-product/{id}', [Masters::class, 'VendorProduct'])->name('vendor-product');
  Route::post('AllocateProduct', [Masters::class, 'AllocateProduct'])->name('AllocateProduct');

  Route::get('store-location', [Masters::class, 'StoreLocation'])->name('store-location');
  Route::post('SaveStoreLocation', [Masters::class, 'SaveStoreLocation'])->name('SaveStoreLocation');

  Route::get('unit-type', [Masters::class, 'UnitType'])->name('unit-type');
  Route::post('SaveUnitType', [Masters::class, 'SaveUnitType'])->name('SaveUnitType');


  Route::get('brand', [Masters::class, 'Brand'])->name('brand');
  Route::post('SaveBrand', [Masters::class, 'SaveBrand'])->name('SaveBrand');



  Route::get('category', [Masters::class, 'Category'])->name('category');
  Route::post('SaveCategory', [Masters::class, 'SaveCategory'])->name('SaveCategory');


  Route::get('sub-category', [Masters::class, 'SubCategory'])->name('sub-category');
  Route::post('SaveSubCategory', [Masters::class, 'SaveSubCategory'])->name('SaveSubCategory');



  Route::get('products', [Masters::class, 'Product'])->name('products');
  Route::post('SaveProduct', [Masters::class, 'SaveProduct'])->name('SaveProduct');

  Route::get('finish-products', [Masters::class, 'FinishProduct'])->name('finish-products');
  Route::post('SaveFinishProduct', [Masters::class, 'SaveFinishProduct'])->name('SaveFinishProduct');

  Route::get('raw-material-product/{id}', [Masters::class, 'RawMaterialProduct'])->name('RawMaterialProduct');

  Route::post('SaveRawProduct', [Masters::class, 'SaveRawProduct'])->name('SaveRawProduct');
  Route::post('DeleteProduct', [Masters::class, 'DeleteProduct'])->name('DeleteProduct');

  Route::get('users', [Masters::class, 'users'])->name('users');
  Route::post('SaveUser', [Masters::class, 'SaveUser'])->name('SaveUser');

  Route::get('user-role', [Masters::class, 'UserRole'])->name('user-role');

  Route::post('SaveRole', [Masters::class, 'SaveRole'])->name('SaveRole');

  Route::get('user-permission/{id}', [Masters::class, 'UserPermission'])->name('user-permission');

  Route::post('SaveUserPermission', [Masters::class, 'SaveUserPermission'])->name('SaveUserPermission');
  Route::post('RemovePermission', [Masters::class, 'RemovePermission'])->name('RemovePermission');
  Route::post('UpdateGenSet', [Masters::class, 'UpdateGenSet'])->name('UpdateGenSet');

  Route::get('gst', [Masters::class, 'Gst'])->name('gst');
  Route::post('SaveGst', [Masters::class, 'SaveGst'])->name('SaveGst');
  Route::get('customer-type', [Masters::class, 'CustomerType'])->name('customer-type');
  Route::post('SaveCustomerType', [Masters::class, 'SaveCustomerType'])->name('SaveCustomerType');
  Route::post('UpdateCustomerTypePrice', [Masters::class, 'UpdateCustomerTypePrice'])->name('UpdateCustomerTypePrice');
  Route::get('outlet', [Masters::class, 'Outlet'])->name('outlet');



  Route::get('outlet-product/{id}', [Masters::class, 'OutletProduct'])->name('outlet-product');

  Route::post('AllocateOutletProduct', [Masters::class, 'AllocateOutletProduct'])->name('AllocateOutletProduct');
  Route::get('customer-type-product/{id}', [Masters::class, 'CustomerTypeProduct'])->name('customer-type-product');



  Route::post('AllocateCustomerTypeProduct', [Masters::class, 'AllocateCustomerTypeProduct'])->name('AllocateCustomerTypeProduct');
  Route::get('customer-products/{id}', [Masters::class, 'CustomerProducts'])->name('customer-products');


  Route::get('finish-product-category', [Masters::class, 'FinishProductCategory'])->name('finish-product-category');
  Route::post('SaveFinishProductCategory', [Masters::class, 'SaveFinishProductCategory'])->name('SaveFinishProductCategory');
  Route::get('finish-product-sub-category', [Masters::class, 'FinishProductSubCategory'])->name('finish-product-sub-category');
  Route::post('SaveFinishProductSubCategory', [Masters::class, 'SaveFinishProductSubCategory'])->name('SaveFinishProductSubCategory');
  Route::post('SaveFinishProduct', [Masters::class, 'SaveFinishProduct'])->name('SaveFinishProduct');
  Route::get('department', [Masters::class, 'Department'])->name('department');
  Route::post('SaveDepartment', [Masters::class, 'SaveDepartment'])->name('SaveDepartment');
  Route::post('sendDeleteDepartmentOTP', [cancelInvoiceController::class, 'sendDeleteDepartmentOTP'])->name('sendDeleteDepartmentOTP');
  Route::post('deleteDepartment', [cancelInvoiceController::class, 'deleteDepartment'])->name('deleteDepartment');




  Route::get('department-product/{id}', [Masters::class, 'DepartmentProduct'])->name('department-product');


  Route::post('AllocateDepartmentProduct', [Masters::class, 'AllocateDepartmentProduct'])->name('AllocateDepartmentProduct');
  Route::post('UnAllocateDepartmentProduct', [Masters::class, 'UnAllocateDepartmentProduct'])->name('UnAllocateDepartmentProduct');


  Route::get('mode-of-transport', [Masters::class, 'ModeOfTransport'])->name('mode-of-transport');

  Route::post('SaveModeOfTransport', [Masters::class, 'SaveModeOfTransport'])->name('SaveModeOfTransport');
  Route::post('UpdateAllMargin', [Masters::class, 'UpdateAllMargin'])->name('UpdateAllMargin');

  Route::get('order-type', [Masters::class, 'OrderType'])->name('order-type');
  Route::post('SaveOrderType', [Masters::class, 'SaveOrderType'])->name('SaveOrderType');

  Route::get('barcode', [Barcode::class, 'index'])->name('barcode');
  Route::get('print-barcode/{id}', [Barcode::class, 'PrintBarcode'])->name('print-barcode');
  Route::post('printall-barcode', [Barcode::class, 'PrintallBarcode'])->name('printall-barcode');

  Route::get('order-type', [Masters::class, 'OrderType'])->name('order-type');
  Route::post('UpdateVendorPrice', [Masters::class, 'UpdateVendorPrice'])->name('UpdateVendorPrice');
  Route::post('GetCustomerOutletList', [Masters::class, 'GetCustomerOutletList'])->name('GetCustomerOutletList');
  Route::post('updateVendorProduct', [Masters::class, 'updateVendorProduct'])->name('updateVendorProduct');



  //order routes
  // Route::get('new-order', [OrderManagement::class, 'NewOrder'])->name('new-order'); 
  // Route::post('UploadRequirementList', [OrderManagement::class, 'UploadRequirementList'])->name('UploadRequirementList'); 
  // Route::post('SaveNewOrder', [OrderManagement::class, 'SaveNewOrder'])->name('SaveNewOrder'); 

  // Route::post('InitiateOrder', [OrderManagement::class, 'InitiateOrder'])->name('InitiateOrder'); 

  //PO/Inward Stock Routes
  Route::get('generate-po', [InwardStock::class, 'GeneratePO'])->name('generate-po');
  Route::post('deletePO', [InwardStock::class, 'deletePO'])->name('deletePO');
  Route::post('SavePO', [InwardStock::class, 'SavePO'])->name('SavePO');

  Route::get('purchase-order/{status}', [InwardStock::class, 'PurchaseOrder'])->name('purchase-order/{status}');
  Route::get('purchase-order-view/{id}', [InwardStock::class, 'PurchaseOrderView'])->name('purchase-order/{id}');
  Route::get('inward-stock', [InwardStock::class, 'InwardStock'])->name('inward-stock');
  Route::post('GetPO', [InwardStock::class, 'GetPO'])->name('GetPO');
  Route::post('GetPODet', [InwardStock::class, 'GetPODet'])->name('GetPODet');
  Route::post('SaveInwardStock', [InwardStock::class, 'SaveInwardStock'])->name('SaveInwardStock');

  Route::get('generate-po-finish-goods', [InwardStock::class, 'GeneratePOFinishGoods'])->name('generate-po-finish-goods');
  Route::post('GetFinishProducts', [InwardStock::class, 'GetFinishProducts'])->name('GetFinishProducts');
  Route::post('SaveFinishPO', [InwardStock::class, 'SaveFinishPO'])->name('SaveFinishPO');
  Route::get('purchase-order-finish-goods/{status}', [InwardStock::class, 'PurchaseOrderFInishGoods'])->name('purchase-order-finish-goods');

  Route::get('inward-stock-finish-goods', [InwardStock::class, 'InwardStockFinishGoods'])->name('inward-stock-finish-goods');
  Route::post('GetPOFinishGoods', [InwardStock::class, 'GetPOFinishGoods'])->name('GetPOFinishGoods');
  Route::post('GetPODetFinishGoods', [InwardStock::class, 'GetPODetFinishGoods'])->name('GetPODetFinishGoods');
  Route::post('SaveInwardStockFinishGoods', [InwardStock::class, 'SaveInwardStockFinishGoods'])->name('SaveInwardStockFinishGoods');


  Route::get('purchase-order-view-finish-products/{id}', [InwardStock::class, 'PurchaseOrderViewFinishProducts'])->name('purchase-order-view-finish-products/{id}');



  Route::post('CheckCurrentStock', [InwardStock::class, 'CheckCurrentStock'])->name('CheckCurrentStock');

  Route::post('CompleteGenSet', [InwardStock::class, 'CompleteGenSet'])->name('CompleteGenSet');
  Route::post('SaveGeneratePO', [InwardStock::class, 'SaveGeneratePO'])->name('SaveGeneratePO');
  Route::post('SaveFGGeneratePO', [InwardStock::class, 'SaveFGGeneratePO'])->name('SaveFGGeneratePO');

  Route::get('view-gen-set-details/{id}', [InwardStock::class, 'ViewGenSetDetails'])->name('view-gen-set-details');
  Route::post('GetSerialNumber', [InwardStock::class, 'GetSerialNumber'])->name('GetSerialNumber');
  Route::post('DeleteGenSet', [InwardStock::class, 'DeleteGenSet'])->name('DeleteGenSet');


  Route::get('inward-finish-goods', [InwardStock::class, 'InwardFinishGoods'])->name('inward-finish-goods');
  Route::post('SaveInwardFinishGoods', [InwardStock::class, 'SaveInwardFinishGoods'])->name('SaveInwardFinishGoods');

  Route::get('direct-inward', [InwardStock::class, 'DirectInward'])->name('direct-inward');
  Route::post('SaveDirectInward', [InwardStock::class, 'SaveDirectInward'])->name('SaveDirectInward');

  Route::get('direct-inward-challan', [InwardStock::class, 'DirectInwardChallan'])->name('direct-inward-challan');
  Route::post('DeletePOProduct', [InwardStock::class, 'DeletePOProduct'])->name('DeletePOProduct');
  Route::post('updatePOProduct', [InwardStock::class, 'updatePOProduct'])->name('updatePOProduct');
  Route::post('AddPOProduct', [InwardStock::class, 'AddPOProduct'])->name('AddPOProduct');


  Route::get('vendor-product-finish-goods/{id}', [InwardStock::class, 'VendorProductFinishGoods'])->name('vendor-product-finish-goods');
  Route::post('AllocateFinishGoodsProduct', [InwardStock::class, 'AllocateFinishGoodsProduct'])->name('AllocateFinishGoodsProduct');
  Route::post('GetVendorFinishProducts', [InwardStock::class, 'GetVendorFinishProducts'])->name('GetVendorFinishProducts');
  Route::get('inward-challan-finish-goods', [InwardStock::class, 'inwardChallanFG'])->name('inward-challan-finish-goods');
  Route::get('inward-challan-finish-goods-view/{id}', [InwardStock::class, 'inwardChallanFGView'])->name('inward-challan-finish-goods-view');




  // new order routes 
  Route::get('create-order', [OrderManagement::class, 'CreateOrder'])->name('create-order');
  Route::post('GetPendingTaskList', [OrderManagement::class, 'GetPendingTaskList'])->name('GetPendingTaskList');
  Route::post('SaveOrder', [OrderManagement::class, 'SaveOrder'])->name('SaveOrder');
  Route::get('generate-po-product/{id?}', [OrderManagement::class, 'GeneratePOProduct'])->name('generate-po-product');
  Route::post('GetGenSetProduct', [OrderManagement::class, 'GetGenSetProduct'])->name('GetGenSetProduct');
  Route::post('SavePoProducts', [OrderManagement::class, 'SavePoProducts'])->name('SavePoProducts');

  Route::get('orders/{status}', [OrderManagement::class, 'Orders'])->name('orders/{status}');
  Route::post('SaveOrderStatus', [OrderManagement::class, 'SaveOrderStatus'])->name('SaveOrderStatus');

  Route::get('order-view/{id}', [OrderManagement::class, 'OrderView'])->name('order-view');
  Route::post('ShiftOrder', [OrderManagement::class, 'ShiftOrder'])->name('ShiftOrder');
  Route::post('DeleteGenSetDet', [OrderManagement::class, 'DeleteGenSetDet'])->name('DeleteGenSetDet');
  Route::post('AddGenSetDet', [OrderManagement::class, 'AddGenSetDet'])->name('AddGenSetDet');
  Route::get('order-summary', [OrderManagement::class, 'OrderSummary'])->name('order-summary');
  Route::post('GenerateWorkOrder', [OrderManagement::class, 'GenerateWorkOrder'])->name('GenerateWorkOrder');


  Route::get('order-summary-department-wise', [OrderManagement::class, 'OrderSummaryDepartmentWise'])->name('order-summary-department-wise');
  Route::get('order-summary-customer-wise', [OrderManagement::class, 'OrderSummaryCustomerWise'])->name('order-summary-customer-wise');
  Route::get('order-summary-shop-wise', [OrderManagement::class, 'OrderSummaryShopWise'])->name('order-summary-shop-wise');
  Route::post('GetCustomerTypeProducts', [OrderManagement::class, 'GetCustomerTypeProducts'])->name('GetCustomerTypeProducts');
  Route::post('CompleteProduction', [OrderManagement::class, 'CompleteProduction'])->name('CompleteProduction');

  Route::post('ConvertToInvoice', [OrderManagement::class, 'ConvertToInvoice'])->name('ConvertToInvoice');

  Route::post('GetWordOrder', [OrderManagement::class, 'GetWordOrder'])->name('GetWordOrder');


  Route::post('convertInvoiceDelivered', [OrderManagement::class, 'convertInvoiceDelivered'])->name('convertInvoiceDelivered');
  //bulk import routes 
  Route::post('ImportProducts', [BulkImport::class, 'ImportProducts'])->name('ImportProducts');
  Route::post('ImportFinishProducts', [BulkImport::class, 'ImportFinishProducts'])->name('ImportFinishProducts');

  Route::post('ImportVendor', [BulkImport::class, 'ImportVendor'])->name('ImportVendor');
  Route::post('ImportCustomer', [BulkImport::class, 'ImportCustomer'])->name('ImportCustomer');

  //stock report
  Route::get('current-stock', [StockReport::class, 'CurrentStock'])->name('current-stock');
  Route::get('current-stock-finish-products', [StockReport::class, 'CurrentStockFinishProducts'])->name('current-stock-finish-products');
  Route::get('near-by-minimum-stock', [StockReport::class, 'NearMinimumStock'])->name('near-by-minimum-stock');
  //stock reports

  Route::get('inward-report', [StockReport::class, 'InwardReport'])->name('inward-report');
  Route::get('inward-report-view/{id}', [StockReport::class, 'InwardReportView'])->name('inward-report-view');
  Route::get('attendance-report', [StockReport::class, 'AttendanceReport'])->name('attendance-report');
  Route::get('attendance-report-monthly', [StockReport::class, 'AttendanceReportMonthly'])->name('attendance-report-monthly');
  Route::get('audit-setting', [StockReport::class, 'AuditSetting'])->name('audit-setting');
  Route::post('SaveAuditReport', [StockReport::class, 'SaveAuditReport'])->name('SaveAuditReport');

  Route::get('audit-report', [StockReport::class, 'AuditReport'])->name('audit-report');
  Route::get('audit-report-view/{id}', [StockReport::class, 'AuditReportView'])->name('audit-report-view');
  Route::post('SaveAudit', [StockReport::class, 'SaveAudit'])->name('SaveAudit');

  Route::post('SaveStock', [StockReport::class, 'SaveStock'])->name('SaveStock');
  Route::post('updateStock', [StockReport::class, 'updateStock'])->name('updateStock');
  Route::post('updateFGStock', [StockReport::class, 'updateFGStock'])->name('updateFGStock');
  Route::post('SaveFPStock', [StockReport::class, 'SaveFPStock'])->name('SaveFPStock');
  Route::post('GetStockAdjustmentHistory', [StockReport::class, 'GetStockAdjustmentHistory'])->name('GetStockAdjustmentHistory');
  Route::post('GetFPStockAdjustmentHistory', [StockReport::class, 'GetFPStockAdjustmentHistory'])->name('GetFPStockAdjustmentHistory');
  Route::get('finish-goods-defective-stock', [StockReport::class, 'FinishGoodsDefectiveStock'])->name('finish-goods-defective-stock');

  Route::get('outlet-current-stock', [StockReport::class, 'CurrentStockOutlet'])->name('outlet-current-stock');


  Route::get('fg-audit-setting', [StockReport::class, 'FGAuditSetting'])->name('fg-audit-setting');
  Route::post('SaveFPAuditReport', [StockReport::class, 'SaveFPAuditReport'])->name('SaveFPAuditReport');
  Route::get('fg-audit-report', [StockReport::class, 'FGAuditReport'])->name('fg-audit-report');
  Route::get('fg-audit-report-view/{id}', [StockReport::class, 'FGAuditReportView'])->name('fg-audit-report-view');
  Route::post('SaveFGAudit', [StockReport::class, 'SaveFGAudit'])->name('SaveFGAudit');

  Route::get('outlet-audit-setting', [StockReport::class, 'OutletAuditSetting'])->name('outlet-audit-setting');
  Route::post('SaveOutletAuditReport', [StockReport::class, 'SaveOutletAuditReport'])->name('SaveOutletAuditReport');
  Route::get('outlet-audit-report', [StockReport::class, 'OutletAuditReport'])->name('outlet-audit-report');
  Route::get('outlet-audit-report-view/{id}', [StockReport::class, 'OutletAuditReportView'])->name('outlet-audit-report-view');
  Route::post('SaveOutletAudit', [StockReport::class, 'SaveOutletAudit'])->name('SaveOutletAudit');
  //Lead management 
  Route::get('status', [LeadManagement::class, 'Status'])->name('status');
  Route::post('SaveStatus', [LeadManagement::class, 'SaveStatus'])->name('SaveStatus');

  Route::get('lead/{id}', [LeadManagement::class, 'Lead'])->name('lead');
  Route::post('SaveLead', [LeadManagement::class, 'SaveLead'])->name('SaveLead');
  Route::post('GetLeadDetails', [LeadManagement::class, 'GetLeadDetails'])->name('GetLeadDetails');
  Route::post('GetRemarks', [LeadManagement::class, 'GetRemarks'])->name('GetRemarks');


  //finish products

  Route::get('create-product', [FinishProducts::class, 'CreateProduct'])->name('create-product');
  Route::post('SaveFProducts', [FinishProducts::class, 'SaveFProducts'])->name('SaveFProducts');
  Route::get('product-list', [FinishProducts::class, 'ProductList'])->name('product-list');
  Route::post('ProcessProducts', [FinishProducts::class, 'ProcessProducts'])->name('ProcessProducts');
  Route::post('GetProductionProducts', [FinishProducts::class, 'GetProductionProducts'])->name('GetProductionProducts');

  Route::get('product-raw-material-view/{id}', [FinishProducts::class, 'ProductRawView'])->name('ProductRawView');

  // outward stock

  Route::get('outward-order', [OutwardStock::class, 'OutwardOrder'])->name('outward-order');
  Route::post('GetCustomerOrder', [OutwardStock::class, 'GetCustomerOrder'])->name('GetCustomerOrder');
  Route::post('GetOrderDetails', [OutwardStock::class, 'GetOrderDetails'])->name('GetOrderDetails');
  Route::post('SaveOutward', [OutwardStock::class, 'SaveOutward'])->name('SaveOutward');

  Route::get('outward-order-list', [OutwardStock::class, 'OutwardOrderList'])->name('outward-order-list');

  Route::post('GetOrderDetails', [OutwardStock::class, 'GetOrderDetails'])->name('GetOrderDetails');
  Route::post('DispatchChallan', [OutwardStock::class, 'DispatchChallan'])->name('DispatchChallan');
  Route::post('DeliveredChallan', [OutwardStock::class, 'DeliveredChallan'])->name('DeliveredChallan');

  Route::get('outward-challan-view/{id}', [OutwardStock::class, 'OutwardChallanView'])->name('outward-challan-view');

  Route::post('SaveHeaderMenu', [Masters::class, 'SaveHeaderMenu'])->name('SaveHeaderMenu');



  Route::post('GetProducts', [Masters::class, 'GetProducts'])->name('GetProducts');
  Route::get('outward-customer-order', [OutwardStock::class, 'OutwardCustomerOrder'])->name('outward-customer-order');
  Route::post('GetCustomerOrderProduct', [OutwardStock::class, 'GetCustomerOrderProduct'])->name('GetCustomerOrderProduct');
  Route::post('SaveCustomerOutward', [OutwardStock::class, 'SaveCustomerOutward'])->name('SaveCustomerOutward');


  Route::get('outward-customer-order-list', [OutwardStock::class, 'OutwardCustomerOrderList'])->name('outward-customer-order-list');

  Route::get('customer-outward-challan-view/{id}', [OutwardStock::class, 'CustomerOutwardChallanView'])->name('customer-outward-challan-view');

  Route::post('SaveCustomerOutwardStatus', [OutwardStock::class, 'SaveCustomerOutwardStatus'])->name('SaveCustomerOutwardStatus');

  Route::get('invoices', [OutwardStock::class, 'Invoices'])->name('invoices');
  Route::get('invoice-view/{id}', [OutwardStock::class, 'InvoiceView'])->name('invoice-view');
  Route::post('bulk-invoice-view', [OutwardStock::class, 'bulkInvoiceView'])->name('bulk-invoice-view');

  Route::get('kot', [Masters::class, 'Kot'])->name('kot');
  Route::post('delete_kot', [Masters::class, 'deletekot'])->name('delete_kot');
  //purchase return

  Route::get('purchase-return', [PurchaseReturn::class, 'PurchaseReturnList'])->name('purchase-return');
  Route::post('GetInwardChallan', [PurchaseReturn::class, 'GetInwardChallan'])->name('GetInwardChallan');
  Route::post('GetInwardChallanProducts', [PurchaseReturn::class, 'GetInwardChallanProducts'])->name('GetInwardChallanProducts');
  Route::post('SavePurchaseReturn', [PurchaseReturn::class, 'SavePurchaseReturn'])->name('SavePurchaseReturn');

  Route::get('purchase-return-challan-view/{id}', [PurchaseReturn::class, 'PurchaseReturnChallanView'])->name('purchase-return-challan-view');

  //sale return


  Route::get('sale-return', [SaleReturn::class, 'SaleReturnList'])->name('sale-return');
  Route::post('GetOutwardChallan', [SaleReturn::class, 'GetOutwardChallan'])->name('GetOutwardChallan');
  Route::post('GetOutwardChallanProducts', [SaleReturn::class, 'GetOutwardChallanProducts'])->name('GetOutwardChallanProducts');
  Route::post('SaveSaleReturn', [SaleReturn::class, 'SaveSaleReturn'])->name('SaveSaleReturn');
  Route::get('sale-return-challan-view/{id}', [SaleReturn::class, 'SaleReturnChallanView'])->name('sale-return-challan-view');

  Route::get('sale-return-approve/{id}', [SaleReturn::class, 'SaleReturnApprove'])->name('sale-return-approve');
  Route::post('SaveSaleReturnApprove', [SaleReturn::class, 'SaveSaleReturnApprove'])->name('SaveSaleReturnApprove');
  // outlet role
  Route::post('SaveOutlet', [Outlet::class, 'SaveOutlet'])->name('SaveOutlet');
  Route::get('outlet-role', [Outlet::class, 'OutletRole'])->name('outlet-role');
  Route::post('SaveOutletRole', [Outlet::class, 'SaveOutletRole'])->name('SaveOutletRole');
  Route::get('outlet-user-permission/{id}', [Outlet::class, 'OutletUserPermission'])->name('outlet-user-permission');
  Route::post('SaveOutletUserPermission', [Outlet::class, 'SaveOutletUserPermission'])->name('SaveOutletUserPermission');
  Route::post('RemoveOutletPermission', [Outlet::class, 'RemoveOutletPermission'])->name('RemoveOutletPermission');


  //advance order

  Route::get('advance-order-category', [AdvanceOrder::class, 'AdvanceOrderCategory'])->name('advance-order-category');
  Route::post('SaveAdvanceOrderCategory', [AdvanceOrder::class, 'SaveAdvanceOrderCategory'])->name('SaveAdvanceOrderCategory');

  Route::get('advance-order-flavour', [AdvanceOrder::class, 'AdvanceOrderFlavour'])->name('advance-order-flavour');
  Route::post('SaveAdvanceOrderFlavour', [AdvanceOrder::class, 'SaveAdvanceOrderFlavour'])->name('SaveAdvanceOrderFlavour');

  Route::get('advance-order-shape', [AdvanceOrder::class, 'AdvanceOrderShape'])->name('advance-order-shape');
  Route::post('SaveAdvanceOrderShape', [AdvanceOrder::class, 'SaveAdvanceOrderShape'])->name('SaveAdvanceOrderShape');

  Route::get('advance-order-food-type', [AdvanceOrder::class, 'AdvanceOrderFoodType'])->name('advance-order-food-type');
  Route::post('SaveAdvanceOrderFoodType', [AdvanceOrder::class, 'SaveAdvanceOrderFoodType'])->name('SaveAdvanceOrderFoodType');

  Route::get('advance-order-weight', [AdvanceOrder::class, 'AdvanceOrderWeight'])->name('advance-order-weight');
  Route::post('SaveAdvanceOrderWeight', [AdvanceOrder::class, 'SaveAdvanceOrderWeight'])->name('SaveAdvanceOrderWeight');


  Route::get('advance-order-items', [AdvanceOrder::class, 'AdvanceOrderItems'])->name('advance-order-items');
  Route::post('SaveAdvanceOrderItem', [AdvanceOrder::class, 'SaveAdvanceOrderItem'])->name('SaveAdvanceOrderItem');
  Route::post('GetFlavourDetails', [AdvanceOrder::class, 'GetFlavourDetails'])->name('GetFlavourDetails');
  Route::post('GetFlavourDetailItem', [AdvanceOrder::class, 'GetFlavourDetailItem'])->name('GetFlavourDetailItem');
  Route::post('UpdateAdvancedItem', [AdvanceOrder::class, 'UpdateAdvancedItem'])->name('UpdateAdvancedItem');


  Route::get('create-advance-order', [AdvanceOrder::class, 'CreateAdvanceOrder'])->name('create-advance-order');
  Route::post('SaveAdvanceOrder', [AdvanceOrder::class, 'SaveAdvanceOrder'])->name('SaveAdvanceOrder');
  Route::get('advance-order-list/{status}', [AdvanceOrder::class, 'AdvanceOrderList'])->name('advance-order-list');
  Route::get('advance-order-view/{id}', [AdvanceOrder::class, 'AdvanceOrderView'])->name('advance-order-view');

  Route::post('GetCustomerOrOutlet', [AdvanceOrder::class, 'GetCustomerOrOutlet'])->name('GetCustomerOrOutlet');
  Route::post('UpdateStatus', [AdvanceOrder::class, 'UpdateStatus'])->name('UpdateStatus');

  Route::get('customer-type-advance-items/{id}', [AdvanceOrder::class, 'customerTypeAdvanceItems'])->name('customer-type-advance-items');

  Route::post('AllocateAdvanceItem', [AdvanceOrder::class, 'AllocateAdvanceItem'])->name('AllocateAdvanceItem');
  Route::post('UpdateAdvanceItem', [AdvanceOrder::class, 'UpdateAdvanceItem'])->name('UpdateAdvanceItem');
  Route::post('GetAdvProduct', [AdvanceOrder::class, 'GetAdvProduct'])->name('GetAdvProduct');
  Route::post('Cancel_order', [AdvanceOrder::class, 'Cancel_Order'])->name('Cancel_order');

  // Route::post('/sendCancelOrderOTP', [AdvanceOrder::class, 'sendCancelOrderOTP']);

  Route::post('advConvertToInvoice', [AdvanceOrder::class, 'advConvertToInvoice'])->name('advConvertToInvoice');

  Route::get('advance-invoice-view/{id}', [AdvanceOrder::class, 'advanceInvoiceView'])->name('advance-invoice-view');

  //recipe
  Route::get('create-recipe', [RecipeController::class, 'createRecipe'])->name('create-recipe');
  Route::post('SaveRecipe', [RecipeController::class, 'SaveRecipe'])->name('SaveRecipe');
  Route::get('recipe-list', [RecipeController::class, 'recipeList'])->name('recipe-list');
  Route::get('recipe-view/{id}', [RecipeController::class, 'recipeView'])->name('recipe-view');
  Route::get('make-recipe/{id}', [RecipeController::class, 'makeRecipe'])->name('make-recipe');
  Route::post('delete-recipe', [RecipeController::class, 'receipeDelete'])->name('delete-recipe');

  //daily reports
  Route::get('purchase-variation-report', [Reports::class, 'PurchaseVariationReport'])->name('purchase-variation-report');
  Route::get('purchase-register-report', [Reports::class, 'PurchaseRegisterReport'])->name('purchase-register-report');
  Route::get('sale-register-report', [Reports::class, 'SaleRegisterReport'])->name('sale-register-report');
  Route::get('category-subcategory-report', [Reports::class, 'CategorySubCategoryReport'])->name('category-subcategory-report');
  Route::get('customer-wise-report', [Reports::class, 'CustomerWiseReport'])->name('customer-wise-report');
  Route::get('getSaleRegisterReportData', [Reports::class, 'getSaleRegisterReportData'])->name('getSaleRegisterReportData');

  Route::get('sale-report-tax-bifurcation', [Reports::class, 'saleReportTaxBifurcation'])->name('sale-report-tax-bifurcation');
  Route::get('getSaleReportGstBifurcation', [Reports::class, 'getSaleReportGstBifurcation'])->name('getSaleReportGstBifurcation');
  Route::get('department-wise-treading', [Reports::class, 'departmentWiseTreadingReport'])->name('department-wise-treading');
  Route::get('sale-register-user-wise', [Reports::class, 'saleRegisterUserWise'])->name('sale-register-user-wise');



  //tally report
  Route::get('tally-report', [TallyController::class, 'tallyReport'])->name('tally-report');
  Route::get('outlet_customer', [Masters::class, 'outlet_customer'])->name('outlet_customer');
  Route::post('SaveOutletCustomer', [Masters::class, 'SaveOutletCustomer'])->name('SaveOutletCustomer');

  Route::get('debit-credit-report', [TallyController::class, 'debitCreditReport'])->name('debit-credit-report');

  //report
  Route::get('rm-consumpton-report', [ReportController::class, 'rmConsumptionReport'])->name('rm-consumption-report');
  Route::get('getRmConsumptionReportData', [ReportController::class, 'getRmConsumptionReportData'])->name('getRmConsumptionReportData');

  Route::get('sub-report-consumption', [ReportController::class, 'SubReportConsumption'])->name('sub-report-consumption');
  Route::get('SaleRegisterReport', [ReportController::class, 'SaleRegisterReport'])->name('SaleRegisterReport');

  Route::get('/production-chart-report', [ReportController::class, 'productionChartReport']);
  Route::get('/productionChartReportData', [ReportController::class, 'productionChartReportData']);
  //expense management
  Route::get('expense-category', [expenseManagement::class, 'expenseCategory'])->name('expense-category');
  Route::post('saveExpenseCategory', [expenseManagement::class, 'saveExpenseCategory'])->name('saveExpenseCategory');

  Route::get('expense-sub-category', [expenseManagement::class, 'expenseSubCategory'])->name('expense-sub-category');
  Route::post('saveExpenseSubCategory', [expenseManagement::class, 'saveExpenseSubCategory'])->name('saveExpenseSubCategory');

  Route::get('expense', [expenseManagement::class, 'expense'])->name('expense');
  Route::post('updateExpenseStatus', [expenseManagement::class, 'updateExpenseStatus'])->name('updateExpenseStatus');


  //pos management

  Route::get('pos-order', [posOrderController::class, 'posOrder'])->name('pos-order');
  Route::get('pos-order-view/{id}', [posOrderController::class, 'posOrderView'])->name('pos-order-view');

  Route::get('fa-stock-upload-report', [ReportController::class, 'FaStockUploadReport'])->name('fa-stock-upload-report');
  Route::get('getFaStockReportData', [ReportController::class, 'getFaStockReportData'])->name('getFaStockReportData');
  Route::get('manual-order-report', [ReportController::class, 'manualOrderReport'])->name('manualOrderReport');
  Route::get('purchase-register-tax-bifurcation', [ReportController::class, 'purchaseRegisterTaxBifurcation'])->name('purchase-register-tax-bifurcation');





  //reports controller 

  Route::get('reports/category-wise-sale-and-damage', [SaleReportController::class, 'categoryWiseSaleDamage'])->name('category-wise-sale-and-damage');

  Route::get('reports/advance-order-sale-report', [SaleReportController::class, 'advanceOrderSaleReport'])->name('advance-order-sale-report');

  Route::get('reports/department-consumption-report', [OperationsReport::class, 'departmentConsumptionReport'])->name('reports/department-consumption-report');

  Route::get('reports/po-generated-report', [OperationsReport::class, 'poGeneratedReport'])->name('reports/po-generated-report');

  Route::get('reports/rm-purchase-history-report', [OperationsReport::class, 'rmPurchaseHistoryReport'])->name('reports/rm-purchase-history-report');

  Route::get('reports/department-sale-report', [OperationsReport::class, 'departmentSaleReport'])->name('reports/department-sale-report');

  Route::get('reports/rm-product-ledger-report', [OperationsReport::class, 'rmProductledgerReport'])->name('reports/rm-product-ledger-report');



  Route::get('reports/re-order-report', [OperationsReport::class, 'reOrderReport'])->name('reports/re-order-report');
});
