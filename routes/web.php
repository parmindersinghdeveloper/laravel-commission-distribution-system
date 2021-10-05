<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/user', function () {
    return view('users.index');
});
Route::get('/', function () {
    return view('index');
});
Route::get('landing', function () {
    return view('frontend.index');
})->name('landing');

// Route::get('about-us', function () {
//     return view('frontend.about-us');
// })->name('about');

// Route::get('contact-us', function () {
//     return view('frontend.contact-us');
// })->name('contact');

Route::get('classified', function () {
    return view('frontend.classified');
})->name('classified');

Route::get('news', function () {
    return view('frontend.news');
})->name('news');

Route::get('classified-list', function () {
    return view('frontend.classified-list');
})->name('classified-list');

// Route::get('photo-gallery', function () {
//     return view('frontend.photo-gallery');
// })->name('gallery');

Route::get('privacy-policy', function () {
    return view('frontend.privacy-policy');
})->name('privacy');

Route::get('tbl_package','UserController@get_package_data');

Route::get('terms', 'FooterController@viewTermsConditions')->name('terms');
Route::get('legal', 'FooterController@viewLegals')->name('legal');

Route::get('products', 'FooterController@ourProducts')->name('products');
Route::get('product', 'FooterController@viewProduct')->name('product');
Route::get('about-us', 'AboutusSettingsController@aboutusView')->name('about');
Route::get('contact-us', 'FooterController@contactUs')->name('contact');
Route::post('contact_us', 'SupportController@mailing');

Route::get('photo-gallery', 'FooterController@photoGallery')->name('gallery');
Route::get('photo_gallery_view', 'FooterController@photoGalleryView');



Route::get('login', function () {
    return view('index');
})->name('login');

// Route::get('/test_direct_rollup', 'UserController@test_direct_rollup');<div class="F"></div>

Route::get('admin/generate_payout_entries', 'PayoutController@generate_payout_entries');

Route::get('/gen_direct_rollup', 'UserController@gen_direct_rollup');

Route::get('/logout', 'AdminController@logout');
Route::get('/user/logout', 'UserController@logout');

Route::post('/admin/checklogin', 'AdminController@checklogin');
Route::post('/user/checklogin', 'UserController@checklogin');
Route::get('/register', 'UserController@register');
Route::post('/register_store', 'UserController@register_store');
Route::post('/register/get_sponser', 'UserController@get_sponser');
Route::post('/register/get_filter_states', 'UserController@get_filter_states');
Route::post('/register/get_filter_cities', 'UserController@get_filter_cities');
Route::post('/register/get_epin_pkg', 'UserController@get_epin_pkg');

Route::group(['prefix' => '/admin', 'middleware' => 'auth:web'], function()
{

	Route::get('/payout_dist','SMSController@index_payout')->name('payout-dist')->middleware('can:payout-dist');

	Route::get('/SMS_history','SMSController@index')->name('sms-history')->middleware('can:sms-history');
	Route::get('/repurchase_comm_','SMSController@repurchase_index')->name('repurchase-comm')->middleware('can:repurchase-comm');

	Route::get('/bill_report','SMSController@bill_reprt_index')->name('bill-report')->middleware('can:bill-report');
	Route::get('/search_bill_report','SMSController@search');

	Route::get('/billl','SMSController@bill');

	Route::get('/approve_kyc','KYCController@index')->name('approve-kyc')->middleware('can:approve-kyc');
	Route::post('/view_cancelled_cheque_photo','KYCController@image');
	Route::post('/view_pan_photo','KYCController@view_pan_photo');
	Route::post('/approve_user_kyc','KYCController@approve_user_kyc');
	Route::post('/reject_user_kyc','KYCController@reject_user_kyc');
	Route::get('/approved_kyc_details','KYCController@approved_kyc_details');
	Route::get('/rejected_kyc_details','KYCController@rejected_kyc_details');
	Route::get('/search_member_KYC','KYCController@search_member_KYC');

	Route::get('/delivery_report','DeliveryReportController@delivery_report_index')->name('delivery-report')->middleware('can:delivery-report');
	Route::get('/search_delivery_labels','DeliveryReportController@search');

	Route::get('/visitors_data','UserController@index_visitors_data')->name('visitors-data')->middleware('can:visitors-data');

	Route::get('/plan_details','PlanDetailsController@index')->name('plan-details')->middleware('can:plan-details');
	Route::post('/plan_details','PlanDetailsController@addPlanDetails');

	Route::get('/complaints','SupportController@index')->name('complaints')->middleware('can:complaints');
	Route::post('/get_complaint_form','SupportController@get_complaint_form');
	Route::post('/get_messages_table_view','SupportController@get_messages_table_view');
	Route::post('/submit_complaint_reply','SupportController@submit_complaint_reply');


	Route::get('/state_wise_id','StateWiseUserController@index')->name('state-wise-id')->middleware('can:state-wise-id');
	Route::post('/get_user_count_for_state','StateWiseUserController@get_user_count_for_state');
	Route::post('/get_user_count_for_city','StateWiseUserController@get_user_count_for_city');

	Route::get('/joining_comm','JoiningCommissionController@index')->name('joining-comm')->middleware('can:joining-comm');
	Route::get('/get_kit_price','JoiningCommissionController@get_kit_price');
	Route::post('/insert_joining','JoiningCommissionController@insert');
	Route::post('/delete_joining','JoiningCommissionController@delete');

	Route::get('/repurchase_stock','RepurchaseController@index')->name('repurchase-stock')->middleware('can:repurchase-stock');
	Route::get('/get_discount','RepurchaseController@get_discount');
	Route::post('/repurchase_stock_insert','RepurchaseController@repurchase_stock_insert');
	Route::post('/get_user','RepurchaseController@get_user_details');

	Route::get('/repurchase_report','RepurchaseController@repurchase_report_index')->name('repurchase-report')->middleware('can:repurchase-report');
	Route::get('/search_repurchase_report','RepurchaseController@search');
	Route::get('/get_reports','RepurchaseController@repurchase_report');

	Route::get('/new_order','EshopProductController@new_order_index')->name('new-order')->middleware('can:new-order');
	Route::post('/get_order_details','EshopProductController@get_order_details');
	Route::post('/add_delivery_data','EshopProductController@add_delivery_data');
	Route::post('/get_delivery_form','EshopProductController@get_delivery_form');
	Route::get('/search_delivery','EshopProductController@search_delivery');
	Route::get('/cancel_order','EshopProductController@cancel_order');

	Route::get('/completed_orders','EshopProductController@completed_orders_index')->name('completed-orders')->middleware('can:completed-orders');
	Route::get('/search_completed_delivery','EshopProductController@search_completed_delivery');
	Route::post('/get_completed_form','EshopProductController@get_completed_form');

	Route::get('/deleted_products','EshopProductController@deleted_products_index')->name('deleted-products')->middleware('can:deleted-products');
	Route::get('/restore_deleted_product','EshopProductController@restore_deleted_products');

	Route::get('/banker_master','BankerMasterController@index')->name('banker-master')->middleware('can:banker-master');
	Route::get('/getState','BankerMasterController@getState');
	Route::get('/getCity','BankerMasterController@getCity');
	Route::get('/banker_delete','BankerMasterController@delete');
	Route::post('/banker_edit','BankerMasterController@update');
	Route::post('/banker_master_insert','BankerMasterController@insert');


	Route::get('/event_add_news','NewsController@index')->name('add-news')->middleware('can:add-news');
	Route::post('/event_insert','NewsController@insert');
	Route::post('/news_event_edit','NewsController@update');
	Route::post('/news_event_delete','NewsController@delete');

	Route::get('event_add_video','AddVideoController@index')->name('add-video')->middleware('can:add-video');
	Route::post('event_add_video_insert','AddVideoController@insert');
	Route::post('event_add_video_delete','AddVideoController@delete');
	Route::post('event_add_video_edit','AddVideoController@update');

	Route::get('add_event','AddEventController@index')->name('add-event')->middleware('can:add-event');
	Route::post('add_event_insert','AddEventController@insert');
	Route::post('add_event_delete','AddEventController@delete');
	Route::post('add_event_update','AddEventController@update');

	Route::get('add_photo','EventAddPhotoController@index')->name('add-photo')->middleware('can:add-photo');
	Route::post('add_photo_insert','EventAddPhotoController@insert');
	Route::post('view_photo','EventAddPhotoController@view_image');
	Route::post('add_photo_update','EventAddPhotoController@update');
	Route::post('add_photo_delete','EventAddPhotoController@delete');
	Route::post('search_photo','EventAddPhotoController@search');

	Route::get('add_categories','AddCategoriesController@index')->name('add-categories')->middleware('can:add-categories');
	Route::post('add_categories_insert','AddCategoriesController@insert');
	Route::post('add_categories_delete','AddCategoriesController@delete');
	Route::post('add_categories_edit','AddCategoriesController@update');
	Route::post('add_categories_view_image','AddCategoriesController@view_image');

	Route::get('sub_categories','SubCategoriesController@index')->name('sub-categories')->middleware('can:sub-categories');
	Route::post('add_sub_category_insert','SubCategoriesController@insert');
	Route::post('sub_category_delete','SubCategoriesController@delete');
	Route::post('sub_category_update','SubCategoriesController@update');
	Route::post('sub_category_view_image','SubCategoriesController@view_image');

	Route::get('Eshop_products','EshopProductController@index')->name('Eshop-products')->middleware('can:Eshop-products');
	Route::post('add_Eshop_product','EshopProductController@insert');
	Route::post('delete_Eshop_product','EshopProductController@delete');
	Route::post('edit_Eshop_product','EshopProductController@update');
	Route::post('Eshop_product_view_images','EshopProductController@view_images');

	Route::get('print_address_labels','AddressLabelController@index')->name('Print-Address-Labels')->middleware('can:Print-Address-Labels');
	Route::get('search_print_address_labels','AddressLabelController@search');


	Route::get('/aboutus_settings','AboutusSettingsController@aboutus')->name('about')->middleware('can:about');
	Route::post('/aboutus_update','AboutusSettingsController@aboutusUpdate');

	Route::get('/address_master','AddressMasterController@addressmaster')->name('addressMaster')->middleware('can:addressMaster');
	Route::post('/getState','AddressMasterController@getState');
	Route::post('/getCity','AddressMasterController@getCity');
	Route::post('/address_master_update','AddressMasterController@addressmasterUpdate');

	Route::get('/logo_master','LogoMasterController@logomaster')->name('logoMaster')->middleware('can:logoMaster');
	Route::post('/logo_master_update','LogoMasterController@logoMasterUpdate');
	Route::get('/popup_master','LogoMasterController@popupMaster')->name('popupMaster')->middleware('can:popupMaster');
	Route::post('/popup_master_update','LogoMasterController@popupMasterUpdate');
	Route::post('/display_popup_update','LogoMasterController@popupDisplayUpdate');

	Route::get('/terms_conditions','LegalController@termsConditions')->name('termsConditions')->middleware('can:termsConditions');
	Route::post('/terms_conditions_add','LegalController@addTermsConditions');
	Route::post('/terms_conditions_edit','LegalController@editTermsConditions');
	Route::post('/terms_conditions_delete','LegalController@deleteTermsConditions');
	Route::get('/legal_master','LegalController@legalMaster')->name('legalMaster')->middleware('can:legalMaster');
	Route::post('/legal_master_add','LegalController@addLegalMaster');
	Route::post('/legal_master_edit','LegalController@editLegalMaster');
	Route::post('/legal_master_delete','LegalController@deleteLegalMaster');
	Route::post('/legal_master_img','LegalController@imgLegalMaster');


	Route::get('/upper_bar_links','LinksController@upperBarLinks')->name('upperBarLinks')->middleware('can:upperBarLinks');
	Route::get('/footer_links','LinksController@footerLinks')->name('footerLinks')->middleware('can:footerLinks');
	Route::post('/upper_bar_links_add','LinksController@addUpperBarLinks');
	Route::post('/upper_bar_links_edit','LinksController@editUpperBarLinks');
	Route::post('/upper_bar_links_delete','LinksController@deleteUpperBarLinks');
	Route::get('/social_links','LinksController@socialLinks')->name('socialLinks')->middleware('can:socialLinks');
	Route::post('/social_links_add','LinksController@addSocialLinks');


	Route::get('/happy_birthday_list', 'EventsController@happyBirthdayList')->name('happyBirthdayList')->middleware('can:happyBirthdayList');
	Route::get('/happy_birthday_list_search', 'EventsController@birthdaySearch');

	Route::get('/welcome_letter', 'MemberController@welcomeLetter')->name('welcomeLetter')->middleware('can:welcomeLetter');
	Route::post('/edit_welcome_letter', 'MemberController@editWelcomeLetter');
	Route::get('/quick_joining', 'MemberController@quickJoining')->name('quickJoining')->middleware('can:quickJoining');
	Route::get('/block_member_id', 'MemberController@blockMemberId')->name('blockMemberId')->middleware('can:blockMemberId');
	Route::post('/block_member','MemberController@blockMember');
	Route::post('/block_member_all','MemberController@blockAllMember');
	Route::get('/delete_id', 'MemberController@deleteId')->name('deleteId')->middleware('can:deleteId');
	Route::post('/delete_member_name','MemberController@deleteMemberName');
	Route::post('/delete_member_id','MemberController@deleteMember');
	Route::get('/hide_options', 'MemberController@hideOptions')->name('hideOptions')->middleware('can:hideOptions');
	Route::post('/hide_options_detail', 'MemberController@hideOptionsDetail');
	Route::post('/hide_options_detail_update', 'MemberController@hideOptionsDetailUpdate');

    Route::get('/welcome', 'AdminController@welcome');
    Route::get('/change_password', 'AdminController@change_password');
    Route::post('/change_password', 'AdminController@change_password_logic');


    // Route::post('/test_direct_rollup', 'UserController@test_pin_direct_rollup');
    Route::post('/test_direct_rollup', 'UserController@test_direct_rollup');

    Route::get('/user/create', 'UserController@create');
	Route::get('/user/search', 'UserController@search');
	Route::get('/user/index', 'UserController@index')->name('Member-View')->middleware('can:Member-View');
	Route::post('/user/store', 'UserController@store');
	Route::get('/user/member-pass', 'UserController@member_pass_show')->name('Member-Password')->middleware('can:Member-Password');
	Route::get('/user/txn-pass', 'UserController@member_txn_show')->name('Txn-Password')->middleware('can:Txn-Password');
	Route::get('/user/update/{id}', 'UserController@update');
	Route::post('/user/edit/{id}', 'UserController@store');
	Route::get('/user/show/{id}', 'UserController@show');
	Route::get('/user/delete/{id}', 'UserController@destroy');
	Route::get('/user/active_inactive/{id}', 'UserController@activeinactive');
	Route::get('/user/search_userId', 'UserController@search_userId');
	Route::get('/user/tree-view', 'UserController@tree_view')->name('Tree-View')->middleware('can:Tree-View');
	Route::get('/user/tree-view/remaining/{id}', 'UserController@tree_view_remaining');

	Route::get('/user/direct-ids', 'UserController@Direct_ids_show')->name('Direct-IDS')->middleware('can:Direct-IDS');
	Route::get('/user/top-up', 'UserController@topup_id_show')->name('top-up')->middleware('can:top-up');
	Route::get('/user/direct-ids-single', 'UserController@Direct_ids_single_show');
	Route::get('/user/direct-ids/search', 'UserController@Direct_ids_search');
	Route::post('/user/pass_change', 'UserController@change_mem_pass');
	Route::post('/user/txn_pass_change', 'UserController@change_txn_pass');
	Route::post('/user/get_user_details', 'UserController@get_user_details');
	Route::post('/user/get_package_details', 'UserController@get_package_details');
	Route::post('/user/update_user_package', 'UserController@update_user_package');
	Route::post('/user/edit_profile_update', 'UserController@update_user_details');
	Route::get('/user/member-panel', 'UserController@member_panel_view')->name('member-panel')->middleware('can:member-panel');
	Route::get('/user/member-downline', 'UserController@member_downline_view')->name('member-downline')->middleware('can:member-downline');
	Route::get('/user/member-payout-summary', 'UserController@member_payout_summ_view')->name('Payout-Summary')->middleware('can:Payout-Summary');
	Route::post('/user/Member-Panel-access', 'UserController@member_panel_access');
	Route::post('/user/Member-Downline-access', 'UserController@member_downline_access');
	Route::post('/user/Member-Payout-Summary-access', 'UserController@member_payout_summ_access');
	Route::get('/user/block_reward', 'UserController@block_reward')->name('Blocked-Rwd-Ach')->middleware('can:Blocked-Rwd-Ach');
	Route::get('/user/block_payout', 'UserController@block_payout')->name('Blocked-Pay-Ach')->middleware('can:Blocked-Pay-Ach');
	Route::get('/user/Receipt-report', 'UserController@receipt_report')->name('Reciept-Report')->middleware('can:Reciept-Report');
	Route::get('/user/Receipt-report/search', 'UserController@receipt_report_search');
	Route::get('/user/Welcome-report', 'UserController@welcome_report')->name('Welcome-Report')->middleware('can:Welcome-Report');
	Route::get('/user/Welcome-report/search', 'UserController@welcome_report_search');

	Route::get('/user/edit_profile', 'UserController@edit_profile')->name('edit-profile')->middleware('can:edit-profile');
	Route::get('/user/block_reward_search', 'UserController@block_reward_search');
	Route::post('/user/block_reward_action', 'UserController@block_reward_action');
	Route::get('/user/block_payout_search', 'UserController@block_payout_search');
	Route::post('/user/block_payout_action', 'UserController@block_payout_action');

	Route::get('/package/create', 'PackageController@manage_form')->name('Add-Package')->middleware('can:Add-Package');
	Route::get('/package/update/{id}', 'PackageController@manage_form');
	Route::get('/package/search', 'PackageController@search');
	Route::get('/package/index', 'PackageController@index')->name('Packages')->middleware('can:Packages');
	Route::get('/package/deleted_search', 'PackageController@deleted_search');
	Route::get('/package/deleted', 'PackageController@deleted')->name('Deleted-Packages')->middleware('can:Deleted-Packages');
	Route::post('/package/store', 'PackageController@store');
	Route::post('/package/edit/{id}', 'PackageController@store');
	Route::get('/package/load_data_table', 'PackageController@load_data_table');
	Route::get('/package/show/{id}', 'PackageController@show');
	Route::get('/package/delete/{id}', 'PackageController@destroy');
	Route::get('/package/restore/{id}', 'PackageController@restore');
	Route::get('/package/active_inactive/{id}', 'PackageController@activeinactive');

	Route::get('/payout/view', 'PayoutController@payout_view');
	Route::post('/payout/create', 'PayoutController@process_payout');
	Route::post('/payout/test', 'PayoutController@test_payout');
	Route::get('/payout/process', 'UserController@process_payout')->name('payout-process')->middleware('can:payout-process');
	Route::get('/payout/report', 'UserController@payout_report')->name('payout-report')->middleware('can:payout-report');
	Route::get('/payout/delete', 'PayoutController@payout_delete')->name('payout-delete')->middleware('can:payout-delete');
	Route::post('/payout/action/delete', 'PayoutController@delete_payout');
	Route::post('/payout/process_payout_report', 'UserController@get_payout_details');
	Route::post('/payout/get_payout_list', 'UserController@get_payout_list');
	Route::get('/payout/per-report', 'PayoutController@per_payout_view')->name('per-report')->middleware('can:per-report');
	Route::post('/payout/get_filtered_payout', 'PayoutController@get_filtered_payout');
	Route::get('/payout/add-debit-funds', 'PayoutController@add_debit_fund_view')->name('add-debit-funds')->middleware('can:add-debit-funds');
	Route::post('/payout/add-fund', 'PayoutController@add_fund');
	Route::post('/payout/fund/get_bal', 'PayoutController@get_bal');
	Route::get('/payout/transactions', 'PayoutController@view_transactions');
	Route::post('/payout/all-trans', 'PayoutController@get_transactions_data');
	Route::get('/payout/clear-ewallet', 'PayoutController@get_clear_ewallet_view')->name('clear-ewallet')->middleware('can:clear-ewallet');
	Route::post('/payout/ewallet/pay-payment', 'PayoutController@pay_payment');
	Route::post('/payout/ewallet/get-ids', 'PayoutController@get_ids');
	Route::post('/payout/ewallet/set-min-ewallet-amt', 'PayoutController@set_minimum_ewallet_amt');
	Route::post('/payout/ewallet/set-ewallet-status', 'PayoutController@set_ewallet_status');
	Route::get('/payout/wallets-balance', 'PayoutController@get_wallet_bal_view')->name('wallets-balance')->middleware('can:wallets-balance');
	Route::post('/payout/wallets-balance/search', 'PayoutController@get_wallet_bal');
	Route::get('/payout/withdrawal-request', 'PayoutController@withdrawal_view')->name('withdrawal-request')->middleware('can:withdrawal-request');
	Route::post('/payout/withdrawl/get-list', 'PayoutController@get_withdrawal_list');
	Route::post('/payout/withdrawl/payment', 'PayoutController@withdrawl_payment');
	Route::post('/payout/withdrawl/delete-record', 'PayoutController@withdrawl_delete');



	Route::get('/e-pin/create', 'EpinController@manage_form')->name('epin-create')->middleware('can:epin-create');
	Route::get('/e-pin/update/{id}', 'EpinController@manage_form');
	Route::get('/e-pin/search', 'EpinController@search');
	Route::get('/e-pin/index', 'EpinController@index');
	Route::get('/e-pin/show', 'EpinController@show');
	Route::get('/e-pin/delete', 'EpinController@delete_epin');
	Route::get('/e-pin/status', 'EpinController@status')->name('epin-status')->middleware('can:epin-status');
	Route::post('/e-pin/status/search', 'EpinController@status_search');
	Route::post('/e-pin/store', 'EpinController@store');
	Route::post('/e-pin/edit/{id}', 'EpinController@store');
	Route::get('/e-pin/load_data_table', 'EpinController@load_data_table');
	Route::get('/e-pin/sale', 'EpinController@sale')->name('epin-sale')->middleware('can:epin-sale');
	Route::post('/e-pin/sale_store', 'EpinController@sale_store');
	Route::get('/e-pin/transactions', 'EpinController@transactions')->name('epin-transactions')->middleware('can:epin-transactions');
	Route::get('/e-pin/transaction-search', 'EpinController@transaction_search');
	Route::get('/e-pin/pending', 'EpinController@pending')->name('epin-pending')->middleware('can:epin-pending');
	Route::get('/e-pin/pending-search', 'EpinController@pending_search');
	Route::get('/e-pin/new-joinings', 'EpinController@new_joinings')->name('new-joinings')->middleware('can:new-joinings');
	Route::get('/e-pin/new-joinings-search', 'EpinController@new_joinings_search');

	Route::get('/agents/create', 'AgentController@create');
	Route::get('/agents/search', 'AgentController@search');
	Route::get('/agents/index', 'AgentController@index');
	Route::post('/agents/store', 'AgentController@store');
	Route::get('/agents/update/{id}', 'AgentController@update');
	Route::post('/agents/edit/{id}', 'AgentController@edit');
	Route::get('/agents/show/{id}', 'AgentController@show');
	Route::get('/agents/delete/{id}', 'AgentController@destroy');
	Route::get('/agents/active_inactive/{id}', 'AgentController@activeinactive');

	Route::get('/commission_settings', 'CommissionController@create')->name('Commission-Settings')->middleware('can:Commission-Settings');
	Route::get('/commission_settings/get_commissions', 'CommissionController@get_commissions');

	Route::post('/commission_settings/store', 'CommissionController@store');

    Route::get('/pin_commission_settings', 'CommissionController@pin_commission_create')->name('Pin-Commission-Settings')->middleware('can:Pin-Commission-Settings');
	Route::get('/pin_commission_settings/get_commissions', 'CommissionController@get_pin_commissions');

    Route::get('/pair_commission_settings', 'CommissionController@pair_commission_create')->name('Pair-Commission-Settings')->middleware('can:Pair-Commission-Settings');
    Route::post('/pair_commission_settings_store', 'CommissionController@pair_commission_store');


	Route::post('/pin_commission_settings/store', 'CommissionController@pin_commission_store');

	Route::get('/admin/pan-card-reports', 'AdminController@pan_card_report_view')->name('pan-card-reports')->middleware('can:pan-card-reports');
	Route::post('/admin/pan-card/search', 'AdminController@pan_card_report_search');

    Route::get('/admin/admin-pass', 'AdminController@admin_pass_view')->name('admin-pass')->middleware(('can:admin-pass'));
	Route::post('/admin/pass_change_store', 'AdminController@admin_pass_change_store');

    Route::get('/admin/gst-master', 'AdminController@gst_master_view')->name('gst-master')->middleware('can:gst-master');
	Route::post('/admin/gst_store', 'AdminController@gst_store');

    Route::get('/admin/sub-admin', 'AdminController@sub_admin_view')->name('Sub-admin')->middleware('can:Sub-admin');
	Route::post('/admin/sub-admin-store', 'AdminController@sub_admin_store');

    Route::get('/admin/sub-admin-rights', 'AdminController@sub_admin_rights_view')->name('sub-admin-rights')->middleware('can:sub-admin-rights');
	Route::post('/admin/sub-admin-rights-store', 'AdminController@sub_admin_rights_store');
	Route::post('/admin/get-sub-admin-rights', 'AdminController@get_sub_admin_rights');

    Route::get('/admin/menu', 'AdminController@menu_view')->name('menu');
	Route::post('/admin/menu-store', 'AdminController@menu_store')->name('menu_store');
	Route::get('/admin/menu_search', 'AdminController@get_menu_list')->name('menu_list');
	Route::post('/admin/menu-delete', 'AdminController@menu_delete')->name('menu_delete');

    Route::get('/admin/sub-menu', 'AdminController@sub_menu_view')->name('sub_menu');
	Route::post('/admin/sub-menu-store', 'AdminController@sub_menu_store')->name('sub_menu_store');
	// Route::get('/admin/sub-menu_search', 'AdminController@get_menu_list')->name('menu_list');
	Route::post('/admin/sub-menu-delete', 'AdminController@sub_menu_delete')->name('sub_menu_delete');
	Route::post('/admin/sub-menu-list', 'AdminController@sub_menu_list')->name('sub_menu_list');

    Route::get('/admin/sale-report', 'AdminController@sale_report_view')->name('Sale-Report')->middleware('can:Sale-Report');
	Route::get('/admin/sale-report/search', 'AdminController@sale_report_search')->name('sale_report_search');

    Route::get('/admin/tds-report', 'AdminController@tds_report_view')->name('TDS-Report')->middleware('can:TDS-Report');
	Route::get('/admin/tds-report/search', 'AdminController@tds_report_search')->name('sale_report_search');
	Route::get('/admin/tds-report/get_detailed_report', 'AdminController@get_detailed_report');

    Route::get('/admin/admin-charges-report', 'AdminController@admin_charges_report_view');
	Route::get('/admin/admin-charges-report/search', 'AdminController@admin_charges_search');

    Route::get('/admin/commission-report', 'AdminController@commission_report_view');
	Route::get('/admin/commission-report/search', 'AdminController@commission_search');

    Route::get('/admin/member-deposit', 'AdminController@get_member_deposit');
	Route::post('/admin/member_deposit/search', 'AdminController@search_member_deposit');
	Route::post('/admin/member_deposit/approve', 'AdminController@approve_member_deposit');

    Route::get('/admin/invoice-master', 'AdminController@get_invoice_master_view');
	Route::post('/admin/invoice-master/store', 'AdminController@invoice_master_store');
});

Route::group(['prefix' => '/users', 'middleware' => 'auth:customer'], function()
{
	Route::get('/welcome', 'UserController@welcome');
	Route::get('/e-pin/index', 'UserController@get_epin_list')->name('epin-index')->middleware('can:epin-index');
	Route::get('/e-pin/transfer-epin', 'UserController@get_transfer_epin_view');
	Route::post('/e-pin/transfer-epin/get_epins_record', 'UserController@get_epins_record');
	Route::post('/e-pin/transfer-epin/get_transfer_user_details', 'UserController@get_transfer_user_details');
	Route::post('/e-pin/transfer-epin/insert', 'UserController@insert_transfer_epin');
	Route::get('/e-pin-tansfer/search', 'EpinController@search_transfer_epin');
	Route::post('/e-pin/tansfer/search', 'EpinController@search_transfer_epin_history');
	Route::get('/e-pin/transfer-view', 'UserController@show_transfer_epin');
	Route::get('/e-pin/detailed-purchase-history/{id}', 'EpinController@detailed_purchase_history');
	Route::get('/e-pin/detailed-tfr-history/{id}', 'EpinController@detailed_tfr_history');
	Route::get('/e-pin/show', 'EpinController@show');
	Route::get('/e-pin/purchase', 'EpinController@purchase_pin_view');
	Route::post('/e-pin/purchase_store', 'EpinController@purchase_store');
	
	Route::post('/e-pin/shipment_details', 'EpinController@shipmentDetails');
	Route::post('/e-pin/shipment_details_update', 'EpinController@shipmentDetailsUpdate');
	

	Route::get('/e-pin/topup-pin', 'EpinController@topup_pin_view');
	Route::post('/e-pin/topup_pin_store', 'EpinController@topup_pin_store');
	Route::get('/e-pin/topup-wallet', 'EpinController@topup_wallet_view');
	Route::post('/e-pin/topup_wallet_store', 'EpinController@topup_wallet_store');
	Route::get('/e-pin/topup-history', 'EpinController@topup_history_view');
	Route::post('/e-pin/topup-history-search', 'EpinController@topup_history_search');
	Route::get('/e-pin/photo_gallery', 'FooterController@photoGallery');
	Route::get('/e-pin/photo_gallery_view', 'FooterController@photoGalleryView');
	Route::get('/e-pin/delivery_detail', 'EpinController@deliveryDetail');


	Route::get('/geneology/my-down-line', 'UserController@my_downline_view');
	Route::get('/geneology/my-directs', 'UserController@my_direct_view');
	Route::post('/geneology/get_direct_ids', 'UserController@get_direct_ids');
	Route::get('/geneology/view-tree', 'UserController@get_user_tree_view');
	Route::get('/tree-view/remaining/{id}', 'UserController@user_tree_view_remaining');

	Route::get('/payments/payout-detail', 'payoutController@payout_detail_view');
	Route::post('/payments/payout-list', 'payoutController@get_user_payout_list');
	Route::post('/payments/get_user_payout_detail', 'payoutController@get_user_payout_detail');
	Route::get('/payments/payout-Summary', 'UserController@payout_summary');
	Route::get('/payments/payout-report', 'PayoutController@payout_report');
	Route::post('/payments/get_user_payouts', 'PayoutController@get_payout_user_report');
	Route::get('/payments/withdrawal-request', 'PayoutController@get_withdraw_view');
	Route::post('/payments/set_withdraw_rqst', 'PayoutController@set_withdraw_rqst');
	Route::post('/payments/E-Wallet-fund-transfer', 'PayoutController@ewallet_transfer_fund');
	Route::post('/payments/S-Wallet-fund-transfer', 'PayoutController@swallet_transfer_fund');
	Route::get('/payments/E-Wallet-Transfer', 'PayoutController@get_e_wallet_transfer_view');
	Route::get('/payments/S-Wallet-Transfer', 'PayoutController@get_r_wallet_transfer_view');
	Route::get('/payments/request-payment', 'PayoutController@get_req_payment_view');
	Route::post('/payments/request-payment-store', 'PayoutController@req_payment_store');
	Route::get('/payments/request-payment-status', 'PayoutController@req_payments_status_view');
	Route::post('/payments/get_req_paymnt_status_filter', 'PayoutController@get_req_paymnt_status_filter');

	Route::get('/profile/update-pan', 'UserController@update_pan_view');
	Route::post('/profile/pan-store', 'UserController@pan_store');

	Route::get('/repurchase/shopping_cart', 'RepurchaseController@shoppingCart');
	Route::get('/repurchase/shopping_cart_view', 'RepurchaseController@shoppingCartView');
	Route::get('/repurchase/shopping_cart/product', 'RepurchaseController@shoppingCartProduct');
	Route::post('/repurchase/shopping_addToCart', 'RepurchaseController@shoppingCartProductAdd');
	Route::get('/repurchase/shopping_cart/confirm', 'RepurchaseController@shoppingCartProductConfirm');
	Route::post('/repurchase/shopping_orderConfirm', 'RepurchaseController@shoppingCartOrderConfirm');
	Route::post('/repurchase/shopping_orderDelete', 'RepurchaseController@shoppingCartOrderDelete');

	Route::get('/repurchase/repurchase_report', 'RepurchaseController@repurchaseReport');
	Route::get('/repurchase/repurchase_report_search', 'RepurchaseController@repurchaseReportShow');
	Route::get('/repurchase/repurchase_report_products', 'RepurchaseController@repurchase_report_products');

	Route::get('/repurchase/monthly_bv_report', 'RepurchaseController@monthlyReport');

	Route::get('/support/add_complaint', 'SupportController@viewComplaintForm');
	Route::post('/support/add_complaint', 'SupportController@addComplaint');
	Route::get('/support/our_bankers', 'SupportController@ourBankers');
	Route::get('/support/view_complaint', 'SupportController@viewComplaints');
	Route::get('/support/view_complaint_reply', 'SupportController@viewComplaintReply');
	Route::post('/support/view_complaint_reply', 'SupportController@complaintReply');
	Route::get('/support/email_messaging', 'SupportController@emailMessaging');
	Route::get('/support/email_view', 'SupportController@emailView');
	Route::post('/support/email_delete', 'SupportController@emailDelete');
	Route::post('/support/email_search', 'SupportController@emailSearch');
	Route::post('/support/email_refresh', 'SupportController@emailRefresh');


	Route::get('/home/our_products', 'FooterController@ourProducts');
	Route::get('/home/product', 'FooterController@viewProduct');

	Route::get('/footer/terms_conditions', 'FooterController@viewTermsConditions');
	Route::get('/footer/legals', 'FooterController@viewLegals');
	Route::get('/footer/top_achievers', 'FooterController@topAchievers');
	Route::get('/footer/contact_us', 'FooterController@contactUs');
    Route::post('/footer/contact_us', 'SupportController@mailing');
	Route::get('/footer/privacy_policy', 'FooterController@privacyPolicy');

	Route::get('/profile/edit_profile','Profi/leController@index');
	Route::get('/profile/getUserState','ProfileController@getUserState');
	Route::get('/profile/getCity','ProfileController@getCity');
	Route::post('/profile/update_profile','ProfileController@update');

	Route::get('/profile/edit_bank','ProfileController@bankindex');
	Route::Post('/profile/edit_bank_update','ProfileController@updateBank');

	Route::get('/profile/change_password','ProfileController@changePasswordindex');
	Route::post('/profile/change_password_insert','ProfileController@changePassword');
	Route::get('/profile/change_txn_password','ProfileController@changeTxnPasswordindex');
	Route::post('/profile/change_txn_password_insert','ProfileController@changeTxnPassword');

	Route::get('/profile','ProfileController@ProfileIndex');
	Route::post('/profile/upload_picture','ProfileController@UploadProfile');

	Route::get('/video_gallery','ProfileController@VideoGalleryIndex');

	Route::get('/news','ProfileController@NewsIndex');
});
