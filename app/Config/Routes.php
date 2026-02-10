<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// adminv4
$routes->group('', function($routes) {
    $routes->get('/', 'Adminv4::index');
    $routes->get('adminv4', 'Adminv4::index'); // This is required
    $routes->get('adminv4/authenticate', 'Adminv4::authenticate'); // Optional
    $routes->post('adminv4/authenticate', 'Adminv4::authenticate');
    $routes->get('adminv4/logout', 'Adminv4::logout');
    $routes->match(['get', 'post'], 'adminv4/search', 'Adminv4::search');
    $routes->get('adminv4/home','Adminv4::home');
    $routes->get('adminv4/viewusers/(:num)', 'Adminv4::viewusers/$1');
    $routes->get('adminv4/closeWindow','Adminv4::closeWindow');

});

// Transactions methods
$routes->group('transactions', function($routes) {
$routes->get('amazon', 'Transactions\AmazonTransactions::UploadTransactions');
$routes->get('google', 'Transactions\GoogleTransactions::UploadTransactions');
$routes->get('overdrive', 'Transactions\OverdriveTransactions::UploadTransactions');
$routes->get('scribd', 'Transactions\ScribdTransactions::UploadTransactions');
$routes->get('pratilipi', 'Transactions\PratilipiTransactions::UploadTransactions');
$routes->get('storytelebook', 'Transactions\StorytelTransactions::EbookTransactions');
$routes->get('storytelaudiobook', 'Transactions\StorytelTransactions::AudiobookTransactions');
$routes->get('audible', 'Transactions\AudibleTransactions::UploadTransactions');
$routes->get('kukufm', 'Transactions\KukufmTransactions::UploadTransactions');
$routes->get('youtube', 'Transactions\YoutubeTransactions::UploadTransactions');
$routes->get('amazonpaperback', 'Transactions\AmazonTransactions::uploadPaperbackTransactions');
});

// Channel Excel download
$routes->post('book/download_amazon_excel', 'DownloadExcel\ChannelExcel::amazon_excel');
$routes->post('book/amazon_price_excel', 'DownloadExcel\ChannelExcel::amazon_price_excel');
$routes->post('book/scribd_excel', 'DownloadExcel\ChannelExcel::scribd_excel');
$routes->post('book/storytel_excel', 'DownloadExcel\ChannelExcel::storytel_excel');
$routes->post('book/google_excel', 'DownloadExcel\ChannelExcel::google_excel');
$routes->post('book/overdrive_excel', 'DownloadExcel\ChannelExcel::overdrive_excel');
$routes->match(['get','post'],'book/pratilipi_excel', 'DownloadExcel\ChannelExcel::pratilipi_excel');
$routes->post('book/overdrive_audio_excel', 'DownloadExcel\ChannelExcel::overdrive_audio_excel');
$routes->post('book/google_audio_excel', 'DownloadExcel\ChannelExcel::google_audio_excel');
$routes->post('book/storytel_audio_excel', 'DownloadExcel\ChannelExcel::storytel_audio_excel');
$routes->match(['get', 'post'], 'book/amazonPaperback_excel_download', 'DownloadExcel\ChannelExcel::amazonPaperback_excel_download');


// Bookfair sales details
$routes->get('bookfair/uploaditemwisesale', 'BookFairUpload::uploadItemwiseSaleReport');
$routes->get('bookfair/allocatedBooks', 'BookFairUpload::bookfair_allocated_books');

// stock
$routes->group('stock', function($routes) {
    $routes->get('/', 'Stock::index');
    $routes->get('stockdashboard', 'Stock::stockdashboard');
    $routes->get('getstockdetails', 'Stock::getstockdetails');
    $routes->get('outofstockdetails', 'Stock::outofstockdetails');
    $routes->get('loststockdetails', 'Stock::loststockdetails');
    $routes->get('outsidestockdetails', 'Stock::outsidestockdetails');
    $routes->get('addstock', 'Stock::addstock');
    $routes->match(['GET', 'POST'], 'bookslist', 'Stock::bookslist');
    $routes->post('submitdetails', 'Stock::submitdetails');    
    $routes->get('stockentrydetails', 'Stock::stockentrydetails');
    $routes->post('validatestock', 'Stock::validateStock');
    $routes->get('otherdistribution', 'Stock::otherdistribution');
    $routes->post('saveotherdistribution', 'Stock::saveotherdistribution');
    $routes->get('validate/(:num)', 'Stock::UpdatevalidateStock/$1');
    $routes->get('getmismatchstock', 'Stock::getmismatchstock');
    $routes->post('mismatchupdate', 'Stock::mismatchupdate');
    $routes->post('mismatchsubmit', 'Stock::mismatchSubmit');
    $routes->post('mismatchvalidate', 'Stock::mismatchValidate');
    $routes->post('savequantity', 'Stock::savequantity');
    $routes->get('pendingstock', 'Stock::pendingstock');
    $routes->get('validatedstock', 'Stock::validatedstock');
    $routes->get('totalstock', 'Stock::totalstock');
    $routes->get('disabledstock', 'Stock::disabledstock');
    $routes->get('bookfairdashboard', 'Stock::bookfairDashboard');
    $routes->get('exportExcel', 'Stock::exportExcel');
    $routes->get('bookfairview/(:num)', 'Stock::BookfairView/$1');
    $routes->get('allocatedbooks', 'Stock::allocatedBooks');
    $routes->post('saveallocation', 'Stock::saveAllocation');

    
    //stock Derails for paperbackledgerbooks
    $routes->get('paperbackledgerbooks', 'Stock::paperbackledgerbooks');
    $routes->get('paperbackledgerbooksdetails/(:num)', 'Stock::paperbackledgerbooksdetails/$1');
    $routes->get('paperbackledgerstockdetails', 'Stock::paperbackledgerstockdetails');

    //exceed and lost books
    $routes->get('lostexcessbooksstatus', 'Stock::lostexcessbooksstatus');
    $routes->get('printexcesslostone/(:num)', 'Stock::printexcesslostone/$1');
    $routes->get('printexcesslostall/(:num)', 'Stock::printexcesslostall/$1');

    //free books 
    $routes->get('freebooksdashboard', 'Stock::freebooksdashboard');
    $routes->get('otherdistributionbooksstatus', 'Stock::otherdistributionbooksstatus');
    $routes->get('totalfreebookscompleted','Stock::totalfreebookscompleted');
    $routes->post('markstart', 'Stock::markstart');
    $routes->post('markcovercomplete', 'Stock::markcovercomplete');
    $routes->post('markcontentcomplete', 'Stock::markcontentcomplete');
    $routes->post('marklaminationcomplete', 'Stock::marklaminationcomplete');
    $routes->post('markbindingcomplete', 'Stock::markbindingcomplete');
    $routes->post('markfinalcutcomplete', 'Stock::markfinalcutcomplete');
    $routes->post('markqccomplete', 'Stock::markqccomplete');
    $routes->post('markcompleted', 'Stock::markcompleted');
    $routes->post('freemarkcompleted', 'Stock::freemarkcompleted');

    $routes->get('getpaperbackstock', 'Stock::getpaperbackstock');

    //bulk stock add
    $routes->get('bulkupload', 'Stock::uploadView');
    $routes->post('upload', 'Stock::uploadProcess');
    $routes->post('updateAcceptBooks', 'Stock::updateAcceptBooks'); 
    $routes->post('BulkstockUpload', 'Stock::BulkstockUpload');
    $routes->post('BulkbookshopReturn', 'Stock::bulkbookshopReturn');
});

// tppublisher
$routes->group('tppublisher', function($routes) {
    $routes->get('/', 'TpPublisher::tppublisherDashboard', ['as' => 'tppublisher']);
    $routes->get('tppublisherDashboard/(:any)', 'TpPublisher::tppublisherDashboard/$1');
    $routes->get('tppublisherdetails', 'TpPublisher::tpPublisherDetails'); 
    $routes->post('setpublisherstatus', 'TpPublisher::setpublisherstatus');
    $routes->get('tppublisherview', 'TpPublisher::tpPublisherView');
    $routes->post('tpPublisherAdd', 'TpPublisher::tpPublisherAdd');
    $routes->get('tppublisherdetailsview/(:num)', 'TpPublisher::tpPublisherDetailsView/$1');
    $routes->get('tppublisheredit/(:num)', 'TpPublisher::tpPublisherEdit/$1');
    $routes->post('editpublisherpost', 'TpPublisher::editPublisherPost');

    $routes->get('tpauthordetails', 'TpPublisher::tpAuthorDetails');
    $routes->get('tpauthoradddetails', 'TpPublisher::tpAuthorAddDetails');
    $routes->post('tpAuthoradd', 'TpPublisher::tpAuthoradd');
    $routes->post('setAuthorStatus', 'TpPublisher::setAuthorStatus');
    $routes->get('tpauthorview/(:num)', 'TpPublisher::tpAuthorView/$1');
    $routes->get('tpauthoredit/(:num)', 'TpPublisher::tpAuthorEdit/$1');
    $routes->post('editauthorpost', 'TpPublisher::editAuthorPost');

    $routes->get('tpbookdetails', 'TpPublisher::tpBookDetails');
    $routes->get('tpbookadddetails', 'TpPublisher::tpBookAddDetails');
    $routes->post('getAuthorsByPublisher', 'TpPublisher::getAuthorsByPublisher');
    $routes->post('tpBookPost', 'TpPublisher::tpBookPost');
    $routes->post('tpbookupdatestatus', 'TpPublisher::tpBookUpdateStatus');
    $routes->post('setBookStatus', 'TpPublisher::setBookStatus');
    $routes->get('tpbookview/(:num)', 'TpPublisher::tpBookView/$1');
    $routes->get('edittpbook/(:num)', 'TpPublisher::editTpBook/$1');
    $routes->post('edittpbookpost', 'TpPublisher::editTpBookPost');

    $routes->get('tppublisherorder', 'TpPublisher::tppublisherOrder');
    $routes->get('tpstockdetails', 'TpPublisher::tpStockDetails');
    $routes->get('book-ledger-details/(:num)/(:any)', 'TpPublisher::bookLedgerDetails/$1/$2');

    $routes->match(['GET', 'POST'], 'tpbookaddstock', 'TpPublisher::tpbookaddstock');
    $routes->post('getAuthorTpBook', 'TpPublisher::getAuthorTpBook');
    $routes->post('addTpBookStock', 'TpPublisher::addTpBookStock');
    
    $routes->get('tpsalesdetails', 'TpPublisher::tpSalesDetails');
    $routes->get('tpsalesadd', 'TpPublisher::tpSalesAdd');
    $routes->get('tporderfulldetails/(:num)', 'TpPublisher::tpOrderFullDetails/$1');
    $routes->post('tpbookorderdetails', 'TpPublisher::tpbookOrderDetails');
    $routes->post('tppublisherorderpost', 'TpPublisher::tppublisherOrderPost');
    $routes->post('tppublisherordersubmit', 'TpPublisher::tppublisherOrderSubmit');
    $routes->get('tpordersuccess', 'TpPublisher::tpordersuccess'); 
    $routes->get('tpstockledgerdetails', 'TpPublisher::tpstockLedgerDetails');
    $routes->get('tpstockledgerview/(:num)', 'TpPublisher::tpstockLedgerView/$1');

    $routes->get('tppublisherorderdetails', 'TpPublisher::tppublisherOrderDetails');
    $routes->get('tppublisherorderpayment', 'TpPublisher::tppublisherOrderPayment');
     $routes->get('tpsalesfull/(:any)/(:any)/(:any)', 'TpPublisher::tpsalesfull/$1/$2/$3');
    $routes->post('tpsalespaid', 'TpPublisher::tpSalesPaid');

    $routes->post('markShipped', 'TpPublisher::markShipped');
    $routes->post('markCancel', 'TpPublisher::markCancel');
    $routes->post('markReturn', 'TpPublisher::markReturn');
    $routes->post('initiatePrint', 'TpPublisher::initiatePrint');

    $routes->post('markAsPaid', 'TpPublisher::markAsPaid');
    $routes->post('tppublisheradd', 'TpPublisher::tpPublisherAdd');
    $routes->get('tppublishersdetails/(:num)/(:segment)?', 'TpPublisher::tppublishersdetails/$1/$2');

    $routes->get('getshippedorders', 'TpPublisher::getShippedOrders');
    $routes->get('getallshippedorders', 'TpPublisher::getallshippedorders');
    $routes->get('tppublishercreateorder/(:any)', 'TpPublisher::tppublisherCreateOrder/$1');
    $routes->get('tppublishercreateorder', 'TpPublisher::tppublisherCreateOrder');
    $routes->post('tppublishersorder', 'TpPublisher::tppublishersOrder');
    $routes->post('tppublisherorderstock', 'TpPublisher::tppublisherOrderStock');
    $routes->get('orderpreview', 'TpPublisher::orderpreview');  
    $routes->post('submitorder', 'TpPublisher::submitOrder');
    $routes->get('ordersuccess', 'TpPublisher::ordersuccess');

   });

// tppublisher dashboard
    $routes->group('tppublisherdashboard', function($routes) {
    $routes->get('/', 'TpPublisherDashboard::tpPublisherDashboard', ['as' => 'tppublisherdashboard']);
    $routes->get('tppublisherdashboard', 'TpPublisherDashboard::tpPublisherDashboard');
    $routes->get('viewpublisherbooks', 'TpPublisherDashboard::viewPublisherBooks');
    $routes->get('tppublishercreateorder', 'TpPublisherDashboard::tppublisherCreateOrder');
    $routes->get('tporderfulldetails/(:num)', 'TpPublisherDashboard::tpOrderFullDetails/$1');
    $routes->get('tpsalesdetails', 'TpPublisherDashboard::tpSalesDetails');
    $routes->get('handlingandpay', 'TpPublisherDashboard::handlingAndPay');
    $routes->post('tppublisherorder', 'TpPublisherDashboard::tppublisherOrder'); 
    $routes->post('tppublisherorderstock', 'TpPublisherDashboard::tppublisherOrderStock');
    $routes->post('tppublisherordersubmit', 'TpPublisherDashboard::tppublisherOrderSubmit');
    $routes->get('tppublisherorderdetails', 'TpPublisherDashboard::tppublisherOrderDetails');
    $routes->get('tppublisherorderpayment', 'TpPublisherDashboard::tppublisherOrderPayment');
    $routes->get('tpsalesfull/(:any)/(:any)', 'TpPublisherDashboard::tpSalesFull/$1/$2');
    
    $routes->get('tpbookfulldetails/(:num)', 'TpPublisherDashboard::tpBookFullDetails/$1');
    $routes->get('tpstockledgerdetails', 'TpPublisherDashboard::tpstockLedgerDetails');
    $routes->get('tpstockledgerview/(:num)', 'TpPublisherDashboard::tpstockLedgerView/$1'); 
    $routes->get('orderpreview', 'TpPublisherDashboard::orderpreview');
    $routes->get('ordersuccess', 'TpPublisherDashboard::ordersuccess');
});

// user dashboard
    $routes->group('user', function ($routes) {
    $routes->get('userdashboard', 'User::userDashboard');
    $routes->post('getuserdetails', 'User::getUserDetails');
    $routes->post('clearuserdevices', 'User::clearUserDevices');
    $routes->post('addplanforuser', 'User::addPlanForUser');
    $routes->get('authorgiftbooks', 'User::authorGiftBooks');
    $routes->post('checkorcreate', 'User::checkOrCreate');
    $routes->post('createuser', 'User::CreateUser');
    $routes->post('submitgiftbook', 'User::submitGiftBook');
    $routes->get('deletecontactus/(:num)', 'User::deleteContactUs/$1');
    $routes->get('cancelsubscription', 'User::cancelSubscription');
    $routes->get('markSubscriptionCancelled/(:segment)', 'User::markSubscriptionCancelled/$1');
});

//Royalty
$routes->group('royalty', function ($routes) {
    $routes->get('royaltyconsolidation', 'Royalty::royaltyconsolidation');
    $routes->post('paynow', 'Royalty::paynow');
    $routes->get('getroyaltybreakup/(:any)', 'Royalty::getroyaltybreakup/$1');
    $routes->match(['GET', 'POST'], 'royaltyrevenue', 'Royalty::royaltyrevenue');
    $routes->get('transactiondetails', 'Royalty::transactiondetails');
    $routes->get('dashboard', 'Royalty::index');
    $routes->get('royaltyquaterlyreport/(:any)', 'Royalty::royaltyquaterlyreport/$1');
    $routes->get('royaltyquaterfullreport', 'Royalty::royaltyquaterfullreport');  
    $routes->get('royaltyconsolidationreport/(:any)', 'Royalty::royaltyconsolidationreport/$1');
});

// royalty publisher excel download 
$routes->get('royalty/download_bank_excel', 'DownloadExcel\RoyaltyExcel::DownloadBankExcel');

$routes->group('bookId', function ($routes) {
    $routes->get('processBookExcel', 'DownloadExcel\BookIdExcel::processBookExcel');
    $routes->get('processTamilBookExcel', 'DownloadExcel\BookIdExcel::processTamilBookExcel'); // opti
});


//Sales
$routes->group('sales', function($routes) {
    $routes->get('salesdashboard', 'Sales::salesdashboard');
    $routes->get('salesreports', 'Sales::salesReports');
    $routes->get('ebooksales', 'Sales::ebookSales');
    $routes->get('audiobooksales', 'Sales::audiobookSales');
    $routes->get('paperbacksales', 'Sales::paperbackSales');
    $routes->get('ebookamazondetails', 'Sales::EbookAmazondetails');
    $routes->get('ebookoverdrivedetails', 'Sales::EbookOverdrivedetails');
    $routes->get('overdrivebooks', 'Sales::overdriveBooks');
    $routes->get('overdriveauthors', 'Sales::overdriveAuthors');
    $routes->get('overdrivebooktransactions/(:num)', 'Sales::overdriveBookTransactions/$1');
    $routes->get('overdriveauthorbooks/(:num)', 'Sales::overdriveAuthorBooks/$1');
    $routes->get('overdriveorders', 'Sales::overdriveOrders');
    $routes->get('ebookscribddetails', 'Sales::EbookScribddetails');
    $routes->get('scribdbooks', 'Sales::scribdBooks');
    $routes->get('scribdbookdetails/(:num)', 'Sales::scribdBookDetails/$1');
    $routes->get('ebookstoryteldetails', 'Sales::EbookStoryteldetails');
   $routes->get('audibleaudiobookdetails', 'Sales::AudibleAudiobookDetails');
   $routes->get('audiobookoverdrivedetails', 'Sales::AudiobookOverdriveDetails');
   $routes->get('audiobookgoogledetails', 'Sales::audiobookGoogleDetails');
   $routes->get('audiobookstoryteldetails', 'Sales::audiobookStorytelDetails');
    $routes->get('youtubedetails', 'Sales::youtubeDetails');
    $routes->get('kukufmdetails', 'Sales::kukuFMDetails');

});
//Sales dashboard
$routes->group('dashboard', function($routes) {
    $routes->get('amazonpaperback', 'Sales::amazonpaperback');
    $routes->get('amazonpaperbackrevenue', 'Sales::amazonpaperbackrevenue');
    $routes->get('salesreports', 'Sales::salesReports');
    $routes->get('ebooksales', 'Sales::ebookSales');
    $routes->get('audiobooksales', 'Sales::audiobookSales');
    $routes->get('paperbacksales', 'Sales::paperbackSales');
    $routes->get('amazonpbkbookdetails/(:num)', 'Sales::amazonpbkbookdetails/$1');
    $routes->get('offlinesales', 'Sales::offlinesales');
    $routes->get('bookshopsales', 'Sales::bookshopsales');
    
});


//Paperback//
$routes->group('paperback', function($routes){
    //online//
    $routes->get('onlineorderbooksstatus', 'Paperback::onlineOrderbooksStatus');
    $routes->get('onlineordership/(:num)/(:num)', 'Paperback::onlineordership/$1/$2');
    $routes->get('totalonlineordercompleted','Paperback::totalonlineordercompleted');
    $routes->get('onlinebulkordersship/(:num)', 'Paperback::onlinebulkordersship/$1');
    $routes->post('bulkonlineordershipmentcompleted','Paperback::bulkonlineordershipmentcompleted');
    $routes->post('onlinemarkcancel','Paperback::onlinemarkcancel');
    $routes->post('onlineordership','Paperback::onlineordership');
    $routes->post('onlinemarkshipped','Paperback::onlinemarkshipped');
    $routes->post('onlinetrackingdetails','Paperback::onlinetrackingdetails');
    
    //offline//
    $routes->get('offlineorderbooksstatus','Paperback::offlineorderbooksstatus');
    $routes->get('offlineorderbooksdashboard','Paperback::offlineorderbooksdashboard');
    $routes->get('offlineorderbookslist','Paperback::offlineorderbookslist');
    $routes->get('offlinebulkordersship/(:num)', 'Paperback::offlinebulkordersship/$1');
    $routes->post('bulkordershipmentcompleted', 'Paperback::bulkordershipmentcompleted'); 
    $routes->get('offlineordership/(:num)/(:num)','Paperback::offlineordership/$1/$2');
    $routes->get('offlineorderbooksdashboard','Paperback::offlineorderbooksdashboard');
    $routes->post('offlineorderbookslist','Paperback::offlineorderbookslist');
    $routes->POST('offlineorderstock','Paperback::offlineorderstock');
    $routes->POST('offlineorderbookssubmit','Paperback::offlineorderbookssubmit');
    $routes->get('totalofflineordercompleted','Paperback::totalofflineordercompleted');
    $routes->get('offlineorderdetails/(:num)','Paperback::offlineorderdetails/$1');
    $routes->post('offlinemarkreturn', 'Paperback::offlinemarkreturn');
    $routes->post('offlinemarkcancel', 'Paperback::offlinemarkcancel');
    $routes->post('offlinemarkpay', 'Paperback::offlinemarkpay');
    $routes->post('offlinemarkshipped', 'Paperback::offlinemarkshipped');
    $routes->get('offlineorderdetails/(:any)', 'Paperback::offlineOrderDetails/$1');

    $routes->get('paperbackledgerbooksdetails/(:num)','Paperback::paperbackledgerbooksdetails/$1');
    $routes->get('onlineorderdetails/(:num)','Paperback::onlineorderdetails/$1');
    $routes->get('paperbackprintstatus','Paperback::paperbackprintstatus');
    $routes->get('initiateprintdashboard/(:num)', 'Paperback::initiateprintdashboard/$1');
    $routes->get('initiateprintbooksdashboard','Paperback::initiateprintbooksdashboard');
    $routes->post('initiateprintbookslist','Paperback::initiateprintbookslist');
    $routes->get('editinitiateprint/(:num)', 'Paperback::editinitiateprint/$1');
    $routes->post('updateinitiateprint', 'Paperback::updateinitiateprint');
    $routes->post('editquantity', 'Paperback::editquantity');
    $routes->post('deleteinitiateprint', 'Paperback::deleteinitiateprint');
    $routes->get('totalinitiateprintcompleted','Paperback::totalinitiateprintcompleted');
    $routes->get('initiateprintdetails/(:num)','Paperback::initiateprintdetails/$1');
    $routes->post('uploadquantitylist', 'Paperback::uploadquantitylist');

    $routes->post('marklevel1complete', 'Paperback::marklevel1complete');
    $routes->post('marklevel2complete', 'Paperback::marklevel2complete');
    $routes->post('markcovercomplete', 'Paperback::markcovercomplete');
    $routes->post('markbookgenerationcomplete', 'Paperback::markbookgenerationcomplete');
    $routes->post('markuploadcomplete', 'Paperback::markuploadcomplete');
    $routes->post('markcompleted', 'Paperback::markcompleted');
    $routes->post('markcancel', 'Paperback::markcancel');
    $routes->post('markreturn', 'Paperback::markreturn');
    $routes->post('markshipped', 'Paperback::markshipped');
    $routes->post('markpay', 'Paperback::markpay');
    $routes->post('initiatePrint', 'Paperback::initiatePrint');
    $routes->post('updatequantity', 'Paperback::updatequantity');
    $routes->post('markstart', 'Paperback::markstart');
    $routes->post('markcovercomplete', 'Paperback::markcovercomplete');
    $routes->post('markcontentcomplete', 'Paperback::markcontentcomplete');
    $routes->post('marklaminationcomplete', 'Paperback::marklaminationcomplete');
    $routes->post('markbindingcomplete', 'Paperback::markbindingcomplete');
    $routes->post('markfinalcutcomplete', 'Paperback::markfinalcutcomplete');
    $routes->post('markqccomplete', 'Paperback::markqccomplete');
    $routes->post('markcompleted', 'Paperback::markcompleted');

    $routes->get('amazonorderbooksstatus','Paperback::amazonorderbooksstatus');
    $routes->get('paperbackamazonorder','Paperback::paperbackamazonorder');
    $routes->post('pustakaamazonorderbookslist','Paperback::pustakaamazonorderbookslist');
    $routes->post('pustakaamazonorderstock','Paperback::pustakaamazonorderstock');
    $routes->post('amazonorderbookssubmit','Paperback::amazonorderbookssubmit');
    $routes->get('totalamazonordercompleted','Paperback::totalamazonordercompleted');
    $routes->get('amazonorderdetails/(:num)','Paperback::amazonorderdetails/$1');
    $routes->get('amazonorderdetails/(:any)', 'Paperback::amazonorderdetails/$1');

    $routes->get('authororderbooksstatus','Paperback::authororderbooksstatus');
    $routes->get('authorlistdetails','Paperback::authorlistdetails');
    $routes->get('authororderbooks/(:num)', 'Paperback::authororderbooks/$1');
    $routes->get('totalauthorordercompleted','Paperback::totalauthorordercompleted');
    $routes->get('authororderdetails/(:num)', 'Paperback::authororderdetails/$1');
    $routes->post('authororderdetails', 'Paperback::authororderdetails');
    $routes->get('createauthorinvoice/(:num)', 'Paperback::createauthorinvoice/$1');
    $routes->get('authorordership/(:num)', 'Paperback::authorordership/$1');
    $routes->post('createauthorinvoice', 'Paperback::createauthorinvoice');
    $routes->post('createinvoice','Paperback::createinvoice');
    $routes->post('authororderbooks', 'Paperback::authororderbooks');
    $routes->get('authororderbooks/(:num)', 'Paperback:: authororderbooks/$1');
    $routes->post('submitauthororders', 'Paperback::submitauthororders');
    $routes->post('authororderqtylist','Paperback::authororderqtylist');
    $routes->post('authororderbookssubmit', 'Paperback::authororderbookssubmit');
    $routes->post('authormarkshipped', 'Paperback::authormarkshipped');
    $routes->post('authormarkcancel', 'Paperback::authormarkcancel');
    $routes->post('authormarkreturn', 'Paperback::authormarkreturn');
    $routes->post('authormarkpay', 'Paperback::authormarkpay');
    $routes->post('markcovercompleted','Paperback::markcovercompleted');
    $routes->post('markcontentcompleted','Paperback::markcontentcompleted');
    $routes->post('marklaminationcompleted','Paperback::marklaminationcompleted');
    $routes->post('markbindingcompleted','Paperback::markbindingcompleted');
    $routes->post('markfinalcutcompleted','Paperback::markfinalcutcompleted');
    $routes->post('markqccompleted','Paperback::markqccompleted');
    $routes->post('authorordermarkcompleted','Paperback::authorordermarkcompleted');
    $routes->post('authormarkcancel','Paperback::authormarkcancel');
    $routes->post('authormarkpay','Paperback::authormarkpay');
    $routes->post('markfilesreadycompleted','Paperback::markfilesreadycompleted');
    $routes->post('authorordermarkstart','Paperback::authorordermarkstart');

    $routes->get('bookshopordersdashboard','Paperback::bookshopordersdashboard');
    $routes->get('bookshoporderbooksstatus', 'Paperback::bookshoporderbooksstatus');
    $routes->get('totalbookshopordercompleted','Paperback::totalbookshopordercompleted');
    $routes->get('bookshoporderdetails/(:num)', 'Paperback::bookshoporderdetails/$1');
    $routes->get('bookshopordership/(:num)', 'Paperback::bookshopordership/$1');
    $routes->get('bookshopmarkshipped','Paperback::bookshopmarkshipped');
    $routes->post('bookshopmarkshipped', 'Paperback::bookshopmarkshipped');
    $routes->get('bookshoporderdetails/(:any)', 'Paperback::bookshoporderdetails/$1');
    $routes->get('createbookshoporder/(:num)', 'Paperback::createbookshoporder/$1');
    $routes->post('createbookshopinvoice', 'Paperback::createbookshopinvoice');
    $routes->get('bookshopdetails', 'Paperback::bookshopdetails');
    $routes->post('addbookshop', 'Paperback::addbookshop');
    $routes->post('bookshoporderbooks', 'Paperback::bookshoporderbooks');
    $routes->get('bookshoporderbooks', 'Paperback:: bookshoporderbooks');
    $routes->post('submitbookshoporders', 'Paperback::submitbookshoporders');
    $routes->get('submitbookshoporders', 'Paperback::submitbookshoporders');
    $routes->post('bookshopmarkcancel', 'Paperback::bookshopmarkcancel');
    $routes->post('bookshopmarkpay', 'Paperback::bookshopmarkpay');

    $routes->get('flipkartorderbooksstatus', 'Paperback::flipkartorderbooksstatus');
    $routes->get('paperbackflipkartorder','Paperback::paperbackflipkartorder');
    $routes->post('pustakaflipkartorderbookslist','Paperback::pustakaflipkartorderbookslist');
    $routes->post('pustakaflipkartorderstock','Paperback::pustakaflipkartorderstock');
    $routes->post('flipkartorderbookssubmit','Paperback::flipkartorderbookssubmit');
    $routes->get('totalflipkartordercompleted','Paperback::totalflipkartordercompleted');
    $routes->get('flipkartorderdetails/(:num)','Paperback::flipkartorderdetails/$1');
    $routes->get('paperbackorderledger/(:num)','Paperback::paperbackorderledger/$1');
    $routes->get('paperbackstockdetails','Paperback::paperbackstockdetails');
    $routes->get('flipkartorderdetails/(:any)', 'Paperback::flipkartorderdetails/$1');
    $routes->post('flipkartmarkshipped', 'Paperback::flipkartmarkshipped');
    $routes->post('flipkartmarkcancel', 'Paperback::flipkartmarkcancel');
    $routes->post('flipkartmarkreturn', 'Paperback::flipkartmarkreturn');

    //bookfair sale or return
    $routes->get('bookfairsaleorreturnview','Paperback::bookfairsaleorreturnview');
    $routes->get('bookfairdetailsview/(:num)','Paperback::bookfairdetailsview/$1');
    $routes->get('ship/(:num)', 'Paperback::ship/$1');

});

//book//
    $routes->group('book', function($routes) {
    $routes->get('bookdashboard', 'Book::bookDashboard');
    $routes->get('getebooksstatus', 'Book::getEbooksStatus');
    $routes->get('ebooks', 'Book::Ebooks');
    $routes->get('audiobookdashboard', 'Book::audioBookDashboard');
    $routes->get('ebookprecurrmonthdetails', 'Book::ebookPreCurrMonthDetails');
    $routes->get('paperbackprecurrmonthdetails', 'Book::paperbackPreCurrMonthDetails');
    $routes->get('paperbacksummary', 'Book::paperBackSummary');
    $routes->get('paperbackdashboard', 'Book::paperbackdashboard');
    $routes->get('getholdbookdetails', 'Book::getholdbookdetails');
    $routes->get('getinactivebooks', 'Book::getInactiveBooks');
    $routes->get('getactivebooks', 'Book::getActiveBooks');
    $routes->get('addbook', 'Book::addBook');
    $routes->post('checkBookUrl', 'Book::checkBookUrl');
    $routes->post('ebooksmarkstart', 'Book::ebooksMarkStart');
    $routes->get('filldataview/(:num)', 'Book::fillDataView/$1');
    $routes->post('filldata', 'Book::fillData');
    $routes->post('addtotest', 'Book::addToTest');
    $routes->post('holdinprogress', 'Book::holdInProgress');
    $routes->get('activatebookpage/(:num)', 'Book::activateBookPage/$1');
    $routes->post('activatebook', 'Book::activateBook');
    $routes->post('addbookpost', 'Book::addBookPost');
    $routes->get('browseinprogressbooks', 'Book::browseInProgressBooks');
    
    $routes->post('markscancomplete', 'Book::markScanComplete');
    $routes->post('markocrcomplete', 'Book::markOcrComplete');
    $routes->post('marklevel1complete', 'Book::markLevel1Complete');
    $routes->post('marklevel2complete', 'Book::markLevel2Complete');
    $routes->post('markcovercomplete', 'Book::markCoverComplete');
    $routes->post('markbookgenerationcomplete', 'Book::markBookGenerationComplete');
    $routes->post('markuploadcomplete', 'Book::markUploadComplete');
    $routes->post('markcompleted', 'Book::markCompleted');

    $routes->get('addaudiobook', 'Book::addAudioBook');
    $routes->post('addaudiobookpost', 'Book::addAudioBookPost');
    $routes->get('audiobookchapters/(:num)', 'Book::audioBookChapters/$1');
    $routes->post('addaudiobookchapter', 'Book::addAudioBookChapter');
    $routes->post('editaudiobookchapter', 'Book::editAudioBookChapter');
    $routes->get('notstartedbooks', 'Book::notStartedBooks');

    $routes->get('pustakadetails', 'Book::pustakaDetails');
     $routes->get('audiodetails', 'Book::audioDetails');
     $routes->get('paperbackdetails', 'Book::paperbackDetails');

    $routes->get('amazondetails', 'Book::amazonDetails');
    $routes->get('amazonunpublishedtamil', 'Book::amazonUnpublishedTamil');
    $routes->get('amazonunpublishedenglish', 'Book::amazonUnpublishedEnglish');
    $routes->get('amazonunpublishedmalayalam', 'Book::amazonUnpublishedMalayalam');

    $routes->get('scribddetails', 'Book::scribdDetails');
    $routes->get('scribdunpublishedtamil', 'Book::scribdUnpublishedTamil');
    $routes->get('scribdunpublishedkannada', 'Book::scribdUnpublishedKannada');
    $routes->get('scribdunpublishedtelugu', 'Book::scribdUnpublishedTelugu');
    $routes->get('scribdunpublishedmalayalam', 'Book::scribdUnpublishedMalayalam');
    $routes->get('scribdunpublishedenglish', 'Book::scribdUnpublishedEnglish');

    $routes->get('storyteldetails', 'Book::storytelDetails');
    $routes->get('storytelunpublishedtamil', 'Book::storytelUnpublishedTamil');
    $routes->get('storytelunpublishedkannada', 'Book::storytelUnpublishedKannada');
    $routes->get('storytelunpublishedtelugu', 'Book::storytelUnpublishedTelugu');
    $routes->get('storytelunpublishedmalayalam', 'Book::storytelUnpublishedMalayalam');
    $routes->get('storytelunpublishedenglish', 'Book::storytelUnpublishedEnglish');

    $routes->get('googledetails', 'Book::GoogleDetails');
    $routes->get('googleunpublishedtamil', 'Book::GoogleUnpublishedTamil');
    $routes->get('googleunpublishedkannada', 'Book::GoogleUnpublishedKannada');
    $routes->get('googleunpublishedtelugu', 'Book::GoogleUnpublishedTelugu');
    $routes->get('googleunpublishedmalayalam', 'Book::GoogleUnpublishedMalayalam');
    $routes->get('googleunpublishedenglish', 'Book::GoogleUnpublishedEnglish');

    $routes->get('overdrivedetails', 'Book::OverdriveDetails');
    $routes->get('overdriveunpublishedtamil', 'Book::OverdriveUnpublishedTamil');
    $routes->get('overdriveunpublishedkannada', 'Book::OverdriveUnpublishedKannada');
    $routes->get('overdriveunpublishedmalayalam', 'Book::OverdriveUnpublishedMalayalam');
    $routes->get('overdriveunpublishedenglish', 'Book::OverdriveUnpublishedEnglish');

    $routes->get('pratilipidetails', 'Book::PratilipiDetails');
    $routes->get('pratilipiunpublishedtamil', 'Book::PratilipiUnpublishedTamil');
    $routes->get('pratilipiunpublishedkannada', 'Book::PratilipiUnpublishedKannada');
    $routes->get('pratilipiunpublishedtelugu', 'Book::PratilipiUnpublishedTelugu');
    $routes->get('pratilipiunpublishedmalayalam', 'Book::PratilipiUnpublishedMalayalam');
    $routes->get('pratilipiunpublishedenglish', 'Book::PratilipiUnpublishedEnglish');

    $routes->get('overdriveudiobookdetails', 'Book::overdriveAudiobookDetails');
    $routes->get('overaudiounpublished/(:segment)', 'Book::overaudioUnpublished/$1');

    $routes->get('amazonpaperbackdetails', 'Book::amazonPaperbackDetails');
    $routes->get('amazonunpublishedbooks/(:num)', 'Book::amazonUnpublishedBooks/$1');

    $routes->get('flipkartpaperbackdetails', 'Book::flipkartPaperbackDetails');
    $routes->get('flipkartunpublishedbooks/(:num)', 'Book::flipkartUnpublishedBooks/$1');

    $routes->get('pustakaaudiodetails', 'Book::pustakaAudioDetails');

    $routes->get('audibledetails', 'Book::audibleDetails');
    $routes->get('audibleunpublished/(:num)', 'Book::audibleUnpublished/$1');

    $routes->get('kukufmdetails', 'Book::kukufmDetails');
    $routes->get('kukufmunpublished/(:num)', 'Book::kukufmUnpublished/$1');

    $routes->get('youtubedetails', 'Book::youtubeDetails');
    $routes->get('youtubeunpublished/(:num)', 'Book::youtubeUnpublished/$1');

    $routes->get('googleaudiodetails', 'Book::googleAudioDetails');
    $routes->get('googleaudiounpublished/(:segment)', 'Book::googleAudioUnpublished/$1');

    $routes->get('storytelaudiodetails', 'Book::storytelAudioDetails');
    $routes->get('storytelaudiounpublished/(:segment)', 'Book::storytelAudioUnpublished/$1');
    
    $routes->get('editbook/(:num)', 'Book::editBook/$1');
    $routes->get('editbookbasicdetails/(:num)', 'Book::editBookBasicDetails/$1');
    $routes->post('editbookbasicdetailspost', 'Book::editBookBasicDetailsPost');
    $routes->get('editbookurldetails/(:num)', 'Book::editBookUrlDetails/$1');
    $routes->post('editurldetailspost', 'Book::editUrlDetailsPost');
    $routes->get('editbookisbndetails/(:num)', 'Book::editBookIsbnDetails/$1');
    $routes->post('editbookisbndetailspost', 'Book::editBookIsbnDetailsPost');
    $routes->get('editbookpaperbackdetails/(:num)', 'Book::editPaperbackDetails/$1');
    $routes->post('editbookpaperbackdetailspost', 'Book::editBookPaperbackDetailsPost');
    $routes->get('editaudiobookdetails/(:num)', 'Book::editAudioBookDetails/$1');
    $routes->post('editaudiobookdetailspost', 'Book::editAudioBookDetailsPost');
    
    $routes->get('initiateindesigndashboard', 'Book::paperbackBooksList');
    $routes->post('selectedbooklist', 'Book::selectedBookList');
    $routes->post('booklistsubmit', 'Book::bookListSubmit');
    $routes->post('indesignmarkstart', 'Book::indesignMarkStart');
    $routes->post('marklevel3completed', 'Book::markLevel3Completed');
    $routes->post('markindesigncompleted', 'Book::markIndesignCompleted');
    $routes->post('markindesignqccompleted', 'Book::markIndesignQcCompleted');
    $routes->post('markreqccompleted', 'Book::markReQcCompleted');
    $routes->post('markindesigncovercompleted', 'Book::markIndesignCoverCompleted');
    $routes->post('markisbnreadycompleted', 'Book::markIsbnReadyCompleted');
    $routes->post('markfinalqccompleted', 'Book::markFinalQcCompleted');
    $routes->post('markfileuploadcompleted', 'Book::markFileUploadCompleted');
    $routes->get('completedbookssubmit/(:num)', 'Book::completedBooksSubmit/$1');
    $routes->post('indesignmarkcompleted', 'Book::indesignMarkCompleted');
    $routes->get('paperbackreworkbook', 'Book::paperbackReworkBook');
    $routes->post('reworkselectedbooks', 'Book::reworkSelectedBooks');
    $routes->post('reworkbooksubmit', 'Book::reworkBookSubmit');
    $routes->get('reworkbookview', 'Book::reworkBookView');
    $routes->post('reworkmarkstart', 'Book::reworkMarkStart');
    $routes->post('markreproofingcompleted', 'Book::markReProofingCompleted');
    $routes->post('markreindesigncompleted', 'Book::markReIndesignCompleted');
    $routes->post('markrefileuploadcompleted', 'Book::markReFileuploadCompleted');
    $routes->get('reworkcompletedsubmit/(:num)', 'Book::reworkCompletedSubmit/$1');
    $routes->post('markreworkcompleted', 'Book::markReworkCompleted');
   


});

// pod 
$routes->group('pod', function($routes) {
    $routes->get('publisherdashboard', 'Pod::publisherDashboard');
    $routes->get('publisheradd', 'Pod::publisherAdd');
    $routes->post('publishersubmit', 'Pod::PodpublisherSubmit');
    $routes->get('dashboard', 'Pod::PodDashboard');
    $routes->get('invoice', 'Pod::PodInvoice');
    $routes->get('endtoendpod', 'Pod::EndToEndPod');
    $routes->post('mark_process/(:any)', 'Pod::markProcess/$1');
    $routes->get('orders', 'Pod::podOrderDetails');
    $routes->get('editpublisher/(:num)', 'Pod::editPublisher/$1');   
    $routes->post('updatepublisher', 'Pod::updatePublisher');
    $routes->get('publisherview/(:num)', 'Pod::publisherView/$1');  
    $routes->get('bookview/(:num)', 'Pod::bookView/$1');
    $routes->get('podbookadd', 'Pod::podBookAdd');
    $routes->post('podbookpost', 'Pod::podBookPost');
    $routes->get('completedpodorders', 'Pod::completedPodOrders');
    $routes->get('podbookscompleted', 'Pod::podBooksCompleted');
    $routes->get('monthDetailsPage/(:any)', 'Pod::monthDetailsPage/$1');
    $routes->get('podbookcreateinvoice/(:num)', 'Pod::podBookCreateInvoice/$1');
    $routes->post('createinvoice', 'Pod::createInvoice');
    $routes->get('pendinginvoices', 'Pod::pendingInvoices');
    $routes->get('pendinginvoicedetails/(:num)', 'Pod::pendingInvoiceDetails/$1');
    $routes->get('raisedinvoices', 'Pod::raisedInvoices');
    $routes->get('raisedinvoicedetails/(:num)', 'Pod::raisedInvoiceDetails/$1');
    $routes->get('paidinvoices', 'Pod::paidInvoices');
    $routes->get('paidinvoicedetails/(:num)', 'Pod::paidInvoiceDetails/$1');
    $routes->post('markstep', 'Pod::markStep');
    $routes->post('mark_start', 'Pod::mark_start');
    $routes->post('indesign_complete', 'Pod::indesign_complete');
    $routes->post('indesign_qc', 'Pod::indesign_qc');
    $routes->post('cover_complete', 'Pod::cover_complete');
    $routes->post('final_approval', 'Pod::final_approval');
    $routes->post('sample_complete', 'Pod::sample_complete');
    $routes->post('file_upload', 'Pod::file_upload');
    $routes->get('viewBookDetails/(:num)', 'Pod::viewBookDetails/$1');
    $routes->get('editpublisherbookdetails/(:num)', 'Pod::editPublisherBookDetails/$1');
    $routes->post('podpublisherbookedit', 'Pod::podPublisherBookEdit');
    $routes->post('mark_payment','Pod::mark_payment');
    $routes->get('mark_payment','Pod::mark_payment');
    $routes->get('invoice/details/(:segment)/(:segment)', 'Pod::monthlyDetails/$1/$2');
});

//narrator
$routes->group('narrator', function($routes) {
    $routes->get('narratordashboard', 'Narrator::narratorDashboard');
    $routes->get('addnarratorview', 'Narrator::addNarratorView');
    $routes->post('addnarratorpost', 'Narrator::addNarratorPost');
    $routes->get('editnarratorview/(:num)', 'Narrator::editNarratorView/$1');
    $routes->post('editnarratorpost', 'Narrator::editNarratorPost');
    $routes->post('addbook', 'Narrator::addBook');

});

//order
$routes->group('orders', function($routes) {
  $routes->get('ordersdashboard', 'Paperback::OrdersDashboard');
  $routes->get('uploadForm', 'UploadExcel\BulkOrder::uploadForm');       
  $routes->post('processUpload', 'UploadExcel\BulkOrder::processUpload');
  $routes->post('fixMismatchedBooks', 'UploadExcel\BulkOrder::fixMismatchedBooks');
  $routes->post('updateMatchedBooks', 'UploadExcel\BulkOrder::updateMatchedBooks'); 
  $routes->post('confirmBooks', 'UploadExcel\BulkOrder::confirmBooks');   
  $routes->post('saveOfflineOrder', 'UploadExcel\BulkOrder::saveOfflineOrder'); 
  $routes->post('saveBookshopOrder','UploadExcel\BulkOrder::saveBookshopOrder');
});

// upload routes
$routes->group('upload', function($routes) {
    $routes->get('scribdbooks', 'UploadExcel\Scribd::ScribdUpload');
    $routes->get('storytelbooks', 'UploadExcel\Storytel::StorytelUpload');
    $routes->get('overdriveebooks', 'UploadExcel\Overdrive::OverdriveUpload');
    $routes->get('overdriveaudiobooks', 'UploadExcel\Overdrive::uploadAudiobooks');
    $routes->get('amazonebooks', 'UploadExcel\Amazon::uploadEBooks');
    $routes->get('amazonpaperbacks', 'UploadExcel\Amazon::uploadPaperbacks');
    $routes->get('googlebooks', 'UploadExcel\Google::Uploadbooks');
    $routes->get('audible', 'UploadExcel\Audible::Uploadbooks');
});

//author
$routes->group('author', function($routes) {
    $routes->get('authordashboard', 'Author::authordashboard');
    $routes->get('addauthor', 'Author::addauthor');
    $routes->get('royaltyauthordashboard', 'Author::royaltyAuthorDashboard');
    $routes->get('freeauthordashboard', 'Author::freeAuthorDashboard');
    $routes->get('magpubauthordashboard', 'Author::magpubauthordashboard');
    //add author//
    $routes->post('addauthorpost', 'Author::addauthorpost');
    $routes->get('addauthorpost', 'Author::addauthorpost');
    $routes->post('getpublishercopyrightowner', 'Author::getpublishercopyrightowner');
    $routes->get('manageauthors/royalty/(:segment)', 'Author::manageauthors/$1');
    $routes->get('manageauthors/free/(:segment)', 'Author::manageauthors/$1');
    $routes->get('manageauthors/magpub/(:segment)', 'Author::manageauthors/$1');
    $routes->get('authordetails/(:num)', 'Author::authordetails/$1');
    //edit//
    $routes->get('editauthor/(:num)', 'Author::editauthor/$1');
    $routes->get('editauthorbasicdetails/(:num)', 'Author::editauthorbasicdetails/$1');
    $routes->post('editauthorbasicdetailspost', 'Author::editauthorbasicdetailspost');
    $routes->get('editauthoragreementdetails/(:num)', 'Author::editauthoragreementdetails/$1');
    $routes->post('editauthoragreementdetailspost', 'Author::editauthoragreementdetailspost');
    $routes->get('editauthorpublisherdetails/(:num)', 'Author::editauthorpublisherdetails/$1');
    $routes->post('editauthorpublisherdetailspost', 'Author::editauthorpublisherdetailspost');
    $routes->get('editauthorbankdetails/(:num)', 'Author::editauthorbankdetails/$1');
    $routes->post('editauthorbankdetailspost', 'Author::editauthorbankdetailspost');
    $routes->get('editauthorcopyrightdetails/(:num)', 'Author::editauthorcopyrightdetails/$1');
    $routes->post('editauthorcopyrightdetailspost', 'Author::editauthorcopyrightdetailspost');
    $routes->get('editauthornamedetails/(:num)', 'Author::editauthornamedetails/$1');
    $routes->post('editauthornamedetailspost','Author::editauthornamedetailspost');
    $routes->get('editauthorlinks/(:num)', 'Author::editauthorlinks/$1');
    $routes->post('editauthorlinkpost', 'Author::editauthorlinkpost');
    $routes->get('editauthorsocialmedialinks/(:num)','Author::editauthorsocialmedialinks/$1');
    $routes->post('editauthorsociallinkpost','Author::editauthorsociallinkpost');
    $routes->match(['get', 'post'], 'addauthorcopyrightdetails/(:num)', 'Author::addauthorcopyrightdetails/$1');
    $routes->post('saveauthorcopyrightdetails','Author::saveauthorcopyrightdetails');
    $routes->post('addauthornamelanguagepost','Author::addauthornamelanguagepost');

    //activate//
    $routes->get('activateauthordetails','Author::activateauthordetails');
    $routes->get('activateauthordetails/(:num)','Author::activateauthordetails/$1');
    $routes->post('activateauthor','Author::activateauthor');

    $routes->get('authorpublishdetails/(:num)/(:any)', 'Author::authorpublishdetails/$1/$2');
    $routes->get('authorpustakadetails/(:num)', 'Author::authorpustakadetails/$1');
    $routes->get('authoramazondetails/(:num)', 'Author::authoramazondetails/$1');
    $routes->get('authorsgoogledetails/(:num)', 'Author::authorsgoogledetails/$1');
    $routes->get('authoroverdrivedetails/(:num)', 'Author::authoroverdrivedetails/$1');
    $routes->get('authorscribddetails/(:num)', 'Author::authorscribddetails/$1');
    $routes->get('authorstoryteldetails/(:num)', 'Author::authorstoryteldetails/$1');
    $routes->get('authorpratilipidetails/(:num)', 'Author::authorpratilipidetails/$1');
}); 

//authors
$routes->group('planauthor', function($routes) {
    $routes->get('addauthorform', 'PlanAuthor::addAuthorForm');
    $routes->post('saveauthor', 'PlanAuthor::saveAuthor');
    $routes->get('planauthor/edit/(:num)', 'PlanAuthor::editAuthor/$1');
});

//masterpage
$routes->group('masterpage', function($routes) {
    $routes->get('dashboard', 'MasterPage::dashboard');
});

//prospectivemanagement
$routes->group('prospectivemanagement', function($routes) {
    $routes->get('/', 'ProspectiveManagement::dashboard');
    $routes->get('dashboard', 'ProspectiveManagement::dashboard');
    $routes->get('addprospect', 'ProspectiveManagement::addProspect');
    $routes->post('saveprospect', 'ProspectiveManagement::saveProspect');
    $routes->get('plandetails', 'ProspectiveManagement::planDetails');
    $routes->get('inprogress', 'ProspectiveManagement::inProgress');
    $routes->get('edit/(:num)', 'ProspectiveManagement::edit/$1');
    $routes->post('updateprospect/(:num)', 'ProspectiveManagement::updateProspect/$1');
    $routes->post('updateprospector/(:num)', 'ProspectiveManagement::updateProspector/$1');
    $routes->get('editinprogress/(:num)', 'ProspectiveManagement::editInprogress/$1');
    $routes->post('updateinprogress/(:num)', 'ProspectiveManagement::updateInprogress/$1');
    $routes->get('deny/(:num)', 'ProspectiveManagement::deny/$1');
    $routes->get('closed', 'ProspectiveManagement::closed');
    $routes->post('close/(:num)', 'ProspectiveManagement::close/$1');
    $routes->get('inprogres/(:num)', 'ProspectiveManagement::inprogres/$1');
    $routes->get('denied', 'ProspectiveManagement::denied');
    $routes->get('view/(:num)', 'ProspectiveManagement::view/$1');
    $routes->get('viewprospector/(:num)', 'ProspectiveManagement::viewProspector/$1');
    $routes->get('close/(:num)', 'ProspectiveManagement::close/$1');
    $routes->post('close/(:num)', 'ProspectiveManagement::close/$1');
    $routes->get('viewplan/(:any)', 'ProspectiveManagement::viewPlan/$1');
    $routes->get('planssummary', 'ProspectiveManagement::plansSummary');
    $routes->get('paymentsummary', 'ProspectiveManagement::paymentSummary');
    $routes->get('paymentdetails', 'ProspectiveManagement::paymentDetails');
    $routes->get('closeinprogress/(:num)', 'ProspectiveManagement::closeInprogress/$1');
    $routes->post('savePayment/(:num)', 'ProspectiveManagement::savePayment/$1');

    $routes->get('addbook', 'ProspectiveManagement::addBook');
    $routes->get('addProspectBook/(:num)', 'ProspectiveManagement::addProspectBook/$1');
    $routes->post('savebookdetails', 'ProspectiveManagement::saveBookDetails');
    $routes->get('editbook/(:num)/(:num)', 'ProspectiveManagement::editBook/$1/$2');
    $routes->post('updatebook/(:num)/(:num)', 'ProspectiveManagement::updateBook/$1/$2');
    $routes->get('viewbook/(:num)/(:num)', 'ProspectiveManagement::viewBook/$1/$2');
    $routes->get('hold', 'ProspectiveManagement::hold');


});


$routes->group('combobookfair', function($routes) {
    $routes->get('createcombo', 'ComboBookfair::uploadView');
    $routes->post('upload', 'combobookfair::uploadProcess');
    $routes->post('updateAcceptBooks', 'combobookfair::updateAcceptBooks'); 
    $routes->post('combopackupload', 'combobookfair::combopackupload');
});
