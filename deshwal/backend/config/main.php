<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$config = [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'layout' => 'main-one', /* Globally apply the new modern layout */
    'modules' => [
        // custome created modules

        'settings' => [
            'class' => 'backend\modules\settings\Settings',
        ],
        'opportunities' => [
            'class' => 'backend\modules\opportunities\Opportunities',
        ],
        'call' => [
            'class' => 'backend\modules\call\Call',
        ],
        'meeting' => [
            'class' => 'backend\modules\meeting\Meeting',
        ],

        'task' => [
            'class' => 'backend\modules\task\Task',
        ],

        'notes' => [
            'class' => 'backend\modules\notes\Notes',
        ],
        'documents' => [
            'class' => 'backend\modules\documents\Documents',
        ],
        'vendoraccount' => [
            'class' => 'backend\modules\vendoraccount\Vendoraccount',
        ],
        'leads' => [
            'class' => 'backend\modules\leads\Leads',
        ],

        'vendor' => [
            'class' => 'backend\modules\vendor\Vendor',
        ],
        'vendorlocations' => [
            'class' => 'backend\modules\vendorlocations\Vendorlocations',
        ],
        'contacts' => [
            'class' => 'backend\modules\contacts\Contacts',
        ],
        'warehouse' => [
            'class' => 'backend\modules\warehouse\Warehouse',
        ],
        'products' => [
            'class' => 'backend\modules\products\Products',
        ],
        'productdetail' => [
            'class' => 'backend\modules\productdetail\Productdetail',
        ],
        'grn' => [
            'class' => 'backend\modules\grn\Grn',
        ],
        'purchaseorder' => [
            'class' => 'backend\modules\purchaseorder\Purchaseorder',
        ],
        'iqclaptop' => [
            'class' => 'backend\modules\iqclaptop\Iqclaptop',
        ],
        'pickup' => [
            'class' => 'backend\modules\pickup\Pickup',
        ],
        'iqcdesktop' => [
            'class' => 'backend\modules\iqcdesktop\Iqcdesktop',
        ],
        'iqctft' => [
            'class' => 'backend\modules\iqctft\Iqctft',
        ],
        'iqclaptopgrade' => [
            'class' => 'backend\modules\iqclaptopgrade\Iqclaptopgrade',
        ],
        'iqcdesktopgrade' => [
            'class' => 'backend\modules\iqcdesktopgrade\Iqcdesktopgrade',
        ],
        'user' => [
            'class' => 'backend\modules\user\User',
        ],
        'quotes' => [
            'class' => 'backend\modules\quotes\Quotes',
        ],
        'drilling' => [
            'class' => 'backend\modules\drilling\Drilling',
        ],
        'degaussing' => [
            'class' => 'backend\modules\degaussing\Degaussing',
        ],
        'datawiping' => [
            'class' => 'backend\modules\datawiping\Datawiping',
        ],
        'shredding' => [
            'class' => 'backend\modules\shredding\Shredding',
        ],
        'inspection' => [
            'class' => 'backend\modules\inspection\Inspection',
        ],
        'weighing' => [
            'class' => 'backend\modules\weighing\Weighing',
        ],
        'drillingformat' => [
            'class' => 'backend\modules\drillingformat\Drillingformat',
        ],
        'drillingvendordetails' => [
            'class' => 'backend\modules\drillingvendordetails\Drillingvendordetails',
        ],
        'degaussingformat' => [
            'class' => 'backend\modules\degaussingformat\Degaussingformat',
        ],
        'degaussingvendordetails' => [
            'class' => 'backend\modules\degaussingvendordetails\Degaussingvendordetails',
        ],
        'sourcingdeal' => [
            'class' => 'backend\modules\sourcingdeal\Sourcingdeal',
        ],
        'servicemaster' => [
            'class' => 'backend\modules\servicemaster\Servicemaster'
        ],
        'servicedetail' => [
            'class' => 'backend\modules\servicedetail\Servicedetail',
        ],
        'contracts' => [
            'class' => 'backend\modules\contracts\Contracts',

        ],
        'campaign' => [
            'class' => 'backend\modules\campaign\Campaign',
        ],
        'sourcingdealcontactrole' => [
            'class' => 'backend\modules\sourcingdealcontactrole\Sourcingdealcontactrole',
        ],
        'opportunitycontactrole' => [
            'class' => 'backend\modules\opportunitycontactrole\Opportunitycontactrole',
        ],
        'drillingcalculator' => [
            'class' => 'backend\modules\drillingcalculator\Drillingcalculator',
        ],
        'datawipingcalculator' => [
            'class' => 'backend\modules\datawipingcalculator\Datawipingcalculator',
        ],
        'shreddingcalculator' => [
            'class' => 'backend\modules\shreddingcalculator\Shreddingcalculator',
        ],
        'degaussingcalculator' => [
            'class' => 'backend\modules\degaussingcalculator\Degaussingcalculator',
        ],
        'termsandconditions' => [
            'class' => 'backend\modules\termsandconditions\Termsandconditions',
        ],
        'gateinward' => [
            'class' => 'backend\modules\gateinward\Gateinward',
        ],
        'pickupcalculator' => [
            'class' => 'backend\modules\pickupcalculator\Pickupcalculator',
        ],
        'payments' => [
            'class' => 'backend\modules\payments\Payments',
        ],
        'segregation' => [
            'class' => 'backend\modules\segregation\Segregation',
        ],
        'inventory' => [
            'class' => 'backend\modules\inventory\Inventory',
        ],
        'tagging' => [
            'class' => 'backend\modules\tagging\Tagging',
        ],
        'stickerremoval' => [
            'class' => 'backend\modules\stickerremoval\Stickerremoval',
        ],
        'cleaning' => [
            'class' => 'backend\modules\cleaning\Cleaning',
        ],
        'productdit' => [
            'class' => 'backend\modules\productdit\Productdit',
        ],
        'quotesdit' => [
            'class' => 'backend\modules\quotesdit\Quotesdit',
        ],
        'widget' => [
            'class' => 'backend\modules\widget\Widget',
        ],
        'inventoryageing' => [
            'class' => 'backend\modules\inventoryageing\Inventoryageing',
        ],
        'salesorderdit' => [
            'class' => 'backend\modules\salesorderdit\Salesorderdit',
        ],
        'purchaseorderdit' => [
            'class' => 'backend\modules\purchaseorderdit\Purchaseorderdit',
        ],
        'clubbedinventory' => [
            'class' => 'backend\modules\clubbedinventory\Clubbedinventory',
        ],
        'modelwiseclubbedinventory' => [
            'class' => 'backend\modules\modelwiseclubbedinventory\ModelwiseClubbedInventory',
        ],
        'grndit' => [
            'class' => 'backend\modules\grndit\Grndit',
        ],
        'billingfromaddress' => [
            'class' => 'backend\modules\billingfromaddress\BillingFromAddress',
        ],
        'billingtoaddress' => [
            'class' => 'backend\modules\billingtoaddress\BillingToAddress',
        ],
        'deliveryaddress' => [
            'class' => 'backend\modules\deliveryaddress\DeliveryAddress',
        ],
        'pickupaddress' => [
            'class' => 'backend\modules\pickupaddress\PickupAddress',
        ],
        'servicepricebook' => [
            'class' => 'backend\modules\servicepricebook\ServicePriceBook',
        ],
        'productpricebook' => [
            'class' => 'backend\modules\productpricebook\ProductPriceBook',
        ],
        'deliverychallandit' => [
            'class' => 'backend\modules\deliverychallandit\Deliverychallandit',
        ],
        'packinglistdit' => [
            'class' => 'backend\modules\packinglistdit\Packinglistdit',
        ],
        'focdit' => [
            'class' => 'backend\modules\focdit\Focdit',
        ],

         'invoicedit' => [
            'class' => 'backend\modules\invoicedit\Invoicedit',
        ],
           'paymentdit' => [
            'class' => 'backend\modules\paymentdit\Paymentdit',
        ],
        // 'exportrequest' => [
        //     'class' => 'backend\modules\exportrequest\Exportrequest',
        // ],
        'userloginhistory' => [
            'class' => 'backend\modules\userloginhistory\Userloginhistory',
        ],
        'raiserequestclient' => [
            'class' => 'backend\modules\raiserequestclient\RaiseRequestClient',
        ],
        'raiserequestvendor' => [
            'class' => 'backend\modules\raiserequestvendor\RaiseRequestVendor',
        ],
        'salesorder' => [
            'class' => 'backend\modules\salesorder\SalesOrder',
        ],
        'generatepi' => [
            'class' => 'backend\modules\generatepi\Generatepi',
        ],
        'pod' => [
            'class' => 'backend\modules\pod\Pod',
        ],
        'vehicleloading' => [
            'class' => 'backend\modules\vehicleloading\VehicleLoading',
        ],'gateoutward' => [
            'class' => 'backend\modules\gateoutward\GateOutward',
        ],
        'paymentupdate' => [
            'class' => 'backend\modules\paymentupdate\PaymentUpdate',
        ],        
        'sourcingdealreport' => [
            'class' => 'backend\modules\sourcingdealreport\Sourcingdealreport',
        ],
        'sourcingdealproductdetail' => [
            'class' => 'backend\modules\sourcingdealproductdetail\Sourcingdealproductdetail',
        ],
        'materialissuenotedit' => [
            'class' => 'backend\modules\materialissuenotedit\Materialissuenotedit',
        ],
        'openingstockprod' => [
            'class' => 'backend\modules\openingstockprod\OpeningstockProd',
        ],
        'openingstockproddit' => [
            'class' => 'backend\modules\openingstockproddit\Openingstockproddit',
        ]
        


    ],
    'timeZone' => 'Asia/Kolkata',
     // 🔥 Global Behaviors, added on 11 feb 2026 for multi profile
    'as activeProfile' => [
        'class' => 'backend\components\ActiveProfileValidator',
    ],
    'components' => [
        'assetManager' => [
        'appendTimestamp' => true,//added forvesioning of js and css by deepika on 12 jan 2026 resolves cache issue
        ],
        'view' => [
            'class' => 'common\components\CustomView',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'authTimeout' => 3000,
            'identityCookie' => [
                'name' => '_identity-backend',
                'httpOnly' => true,
                //'path' => '/deshwal/admin',   // ✅ use `/` not `/admin`
            ],
            'loginUrl' => ['/site/login'],
        ],
        'session' => [
            'name' => 'advanced-backend',
            'cookieParams' => [
                //'path' => '/deshwal/admin',   // ✅ use `/`
                'httpOnly' => true,
            ],
        ],
        'request' => [
            'class' => 'common\components\Request',
            'web' => '/backend/web',
            'csrfParam' => '_csrf',
            'csrfCookie' => [
                'httpOnly' => true,
               // 'path' => '/deshwal/admin',  // ✅ backend only
            ],
            'adminUrl' => '/admin'
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => ['application'],
                    'maskVars' => ['_POST.password', '_GET.password'], // Mask passwords
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // Rule to skip the controller name and map it to an action directly
                'admin/<action:\w+>' => 'admin/site/<action>',
                'leads/<action:\w+>' => 'leads/default/<action>',
                'vendoraccount/<action:\w+>' => 'vendoraccount/default/<action>',
                'vendor/<action:\w+>' => 'vendor/default/<action>',
                'documents/<action:\w+>' => 'documents/default/<action>',
                'notes/<action:\w+>' => 'notes/default/<action>',
                'task/<action:\w+>' => 'task/default/<action>',
                'call/<action:\w+>' => 'call/default/<action>',
                'meeting/<action:\w+>' => 'meeting/default/<action>',
                'opportunities/<action:\w+>' => 'opportunities/default/<action>',
                'contacts/<action:\w+>' => 'contacts/default/<action>',
                'vendorlocations/<action:\w+>' => 'vendorlocations/default/<action>',
                'warehouse/<action:\w+>' => 'warehouse/default/<action>',
                'products/<action:\w+>' => 'products/default/<action>',
                'productdetail/<action:\w+>' => 'productdetail/default/<action>',
                'grn/<action:\w+>' => 'grn/default/<action>',
                'purchaseorder/<action:\w+>' => 'purchaseorder/default/<action>',
                'iqclaptop/<action:\w+>' => 'iqclaptop/default/<action>',
                'pickup/<action:\w+>' => 'pickup/default/<action>',
                'iqcdesktop/<action:\w+>' => 'iqcdesktop/default/<action>',
                'iqctft/<action:\w+>' => 'iqctft/default/<action>',
                'iqclaptopgrade/<action:\w+>' => 'iqclaptopgrade/default/<action>',
                'iqcdesktopgrade/<action:\w+>' => 'iqcdesktopgrade/default/<action>',
                'user/<action:\w+>' => 'user/default/<action>',
                'quotes/<action:\w+>' => 'quotes/default/<action>',
                'quotesdit/<action:\w+>' => 'quotesdit/default/<action>',
                'drilling/<action:\w+>' => 'drilling/default/<action>',
                'degaussing/<action:\w+>' => 'degaussing/default/<action>',
                'datawiping/<action:\w+>' => 'datawiping/default/<action>',
                'shredding/<action:\w+>' => 'shredding/default/<action>',
                'inspection/<action:\w+>' => 'inspection/default/<action>',
                'weighing/<action:\w+>' => 'weighing/default/<action>',
                'drillingformat/<action:\w+>' => 'drillingformat/default/<action>',
                'drillingvendordetails/<action:\w+>' => 'drillingvendordetails/default/<action>',
                'degaussingformat/<action:\w+>' => 'degaussingformat/default/<action>',
                'degaussingvendordetails/<action:\w+>' => 'degaussingvendordetails/default/<action>',
                'sourcingdeal/<action:\w+>' => 'sourcingdeal/default/<action>',
                'servicemaster/<action:\w+>' => 'servicemaster/default/<action>',
                'servicedetail/<action:\w+>' => 'servicedetail/default/<action>',
                'contracts/<action:\w+>' => 'contracts/default/<action>',
                'campaign/<action:\w+>' => 'campaign/default/<action>',
                'sourcingdealcontactrole/<action:\w+>' => 'sourcingdealcontactrole/default/<action>',
                'opportunitycontactrole/<action:\w+>' => 'opportunitycontactrole/default/<action>',
                'drillingcalculator/<action:\w+>' => 'drillingcalculator/default/<action>',
                'datawipingcalculator/<action:\w+>' => 'datawipingcalculator/default/<action>',
                'shreddingcalculator/<action:\w+>' => 'shreddingcalculator/default/<action>',
                'degaussingcalculator/<action:\w+>' => 'degaussingcalculator/default/<action>',
                'termsandconditions/<action:\w+>' => 'termsandconditions/default/<action>',
                'gateinward/<action:\w+>' => 'gateinward/default/<action>',
                'pickupcalculator/<action:\w+>' => 'pickupcalculator/default/<action>',
                'payments/<action:\w+>' => 'payments/default/<action>',
                'segregation/<action:\w+>' => 'segregation/default/<action>',
                'inventory/<action:\w+>' => 'inventory/default/<action>',
                'tagging/<action:\w+>' => 'tagging/default/<action>',
                'stickerremoval/<action:\w+>' => 'stickerremoval/default/<action>',
                'cleaning/<action:\w+>' => 'cleaning/default/<action>',
                'productdit/<action:\w+>' => 'productdit/default/<action>',
                'widget/<action:\w+>' => 'widget/default/<action>',
                'inventoryageing/<action:\w+>' => 'inventoryageing/default/<action>',
                'salesorderdit/<action:\w+>' => 'salesorderdit/default/<action>',
                'purchaseorderdit/<action:\w+>' => 'purchaseorderdit/default/<action>',
                'clubbedinventory/<action:\w+>' => 'clubbedinventory/default/<action>',
                'modelwiseclubbedinventory/<action:\w+>' => 'modelwiseclubbedinventory/default/<action>',
                'grndit/<action:\w+>' => 'grndit/default/<action>',
                'billingfromaddress/<action:\w+>' => 'billingfromaddress/default/<action>',
                'billingtoaddress/<action:\w+>' => 'billingtoaddress/default/<action>',
                'deliveryaddress/<action:\w+>' => 'deliveryaddress/default/<action>',
                'pickupaddress/<action:\w+>' => 'pickupaddress/default/<action>',
                'servicepricebook/<action:\w+>' => 'servicepricebook/default/<action>',
                'productpricebook/<action:\w+>' => 'productpricebook/default/<action>',
                'deliverychallandit/<action:\w+>' => 'deliverychallandit/default/<action>',
                'packinglistdit/<action:\w+>' => 'packinglistdit/default/<action>',        
                'focdit/<action:\w+>' => 'focdit/default/<action>',            
                'invoicedit/<action:\w+>' => 'invoicedit/default/<action>',            
                'paymentdit/<action:\w+>' => 'paymentdit/default/<action>',      
                // 'exportrequest/<action:\w+>' => 'exportrequest/default/<action>', 
                'userloginhistory/<action:\w+>' => 'userloginhistory/default/<action>',           
                'raiserequestclient/<action:\w+>' => 'raiserequestclient/default/<action>',
                'raiserequestvendor/<action:\w+>' => 'raiserequestvendor/default/<action>',
                'salesorder/<action:\w+>' => 'salesorder/default/<action>',
                'generatepi/<action:\w+>' => 'generatepi/default/<action>',
                'pod/<action:\w+>' => 'pod/default/<action>',
                'vehicleloading/<action:\w+>' => 'vehicleloading/default/<action>',
                'gateoutward/<action:\w+>' => 'gateoutward/default/<action>',
                'generatepi/<action:\w+>' => 'generatepi/default/<action>',
                'paymentupdate/<action:\w+>' => 'paymentupdate/default/<action>',
                'sourcingdealreport/<action:\w+>' => 'sourcingdealreport/default/<action>',
                'sourcingdealproductdetail/<action:\w+>' => 'sourcingdealproductdetail/default/<action>',
                'materialissuenotedit/<action:\w+>' => 'materialissuenotedit/default/<action>', 
                'openingstockprod/<action:\w+>' => 'openingstockprod/default/<action>',      
                'openingstockproddit/<action:\w+>' => 'openingstockproddit/default/<action>',               
            ],
        ],
        // Add beforeRequest event handler for access control added by deepika
        'as beforeRequest' => [
            'class' => 'yii\filters\AccessControl',
            'rules' => [
                [
                    'actions' => ['login', 'error'],
                    'allow' => true,
                ],
                [
                    'allow' => true,
                    'roles' => ['@'], // Only allow authenticated users
                ],
            ],
        ],



    ],
    'params' => $params,

];
// added by deepika for gii
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1','139.59.9.220'],
        'allowedIPs' => ['*'],
        //'allowedIPs' => ['127.0.0.1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1','139.59.9.220'],
        'allowedIPs' => ['*'],
    ];
}
return $config;
