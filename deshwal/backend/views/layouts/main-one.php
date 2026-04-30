<?php

use backend\components\SvgRenderHelper;
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);


use app\models\Tab;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use backend\assets\AdminAsset;
use backend\models\AccessCheck;
use common\widgets\Alert;
use app\models\SiteSetting;

//fetch logo
$siteSetting = SiteSetting::find()
            ->where(['active' => 1])->one();

AdminAsset::register($this);
$baseUrl = Yii::$app->HomeUrl;
$this->registerCssFile($baseUrl . 'thememain/css/theme-override.css', ['depends' => [\backend\assets\AdminAsset::class]]);
$model = new AccessCheck();
$id = Yii::$app->user->id;
$moduleName = Yii::$app->controller->module->id;
if ($moduleName === "app-backend")
   $moduleName = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;
$route  = Yii::$app->controller->route;
$currentTabId = Yii::$app->request->get('tabid');
if (Yii::$app->user->isGuest) {
    Yii::$app->response->redirect(Url::to(['site/login']))->send();
    return;
}
// echo $action;die;
// || $moduleName == 'searchinallmodule' code adde by ptpatel to resolve issue of searchinallmodule module -http://139.84.169.156/deshwal/admin/leads/searchinallmodule?search=ravi
if (($moduleName == "site" && $action = "index") || $action == "profileview" || $moduleName == 'searchinallmodule' || $action == "dashboard")
   $access = 1;
else
   $access = $model->checkpermission($id, ucfirst($moduleName), $action);
// echo "moduleName = $moduleName <br>";
// echo "action = $action <br>";
// echo "access = $access";exit;
if ($access == 0) {

   Yii::$app->response->redirect(Url::to(['site/error']))->send();
}
?>
<!-- css and js added by ptpatel -->
<!-- <link href="<?= $baseUrl;?>/thememain/css/searchcss.css" rel="stylesheet"> -->
<?php $this->registerCssFile($baseUrl.'thememain/css/searchcss.css');?>
<?php
$uid = Yii::$app->user->id;
$activeroleId = Yii::$app->session->get('active_profile_id');
if (empty($activeroleId)) {
   $response = Yii::$app->response;
   $response->redirect([Yii::$app->getHomeUrl()]);
   Yii::$app->end();
}
// Fetch the profile name and user name
$userData = Yii::$app->db->createCommand("
                              SELECT profile.profileid,profile.profilename, user.first_name, user.last_name, user.email,user.profilepic 
                              FROM profile
                              JOIN profile2tab ON profile2tab.profileid = profile.profileid
                              JOIN role2profile ON role2profile.profileid = profile.profileid
                              JOIN role ON role.roleid = role2profile.roleid
                              JOIN user2role ON user2role.roleid = role.roleid
                              JOIN user ON user.id = user2role.userid
                              WHERE user.id = :uid and user2role.roleid=:roleid
                              LIMIT 1
                          ")
   ->bindValue(':uid', $uid)
   ->bindValue('roleid', $activeroleId)
   ->queryOne();
$profileid = $userData['profileid'];
$profilename = $userData['profilename'] ?? 'Unknown'; // Set 'Unknown' if profile name is not found
$username = $userData['first_name'] . ' ' . $userData['last_name'] ?? 'Unknown';           // Set 'Unknown' if user name is not found
$useremail = $userData['email'];
$userimage = !empty($userData['profilepic']) ? $userData['profilepic'] : 'no-image.png'; // Default to 'no-image.png' if no image


//get menu
$sql = "select * from parenttab where visible = 0 order by sequence";
$resulttab = Yii::$app->db->createCommand($sql)->queryAll();

$id = Yii::$app->user->id;
$moduleName = Yii::$app->controller->module->id;
if ($moduleName === "app-backend")
   $moduleName = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;
// echo $action;die;
if (($moduleName == 'site' && $action == "index") || $action == "profileview" || ($moduleName == 'setting' && $action == "index")) {
   $activeparent = "home";
} else {
   //get parent tabid of module
   $sql = "select parent from tab where name = :modulename ";
   $pttab = Yii::$app->db->createCommand($sql)
      ->bindValue(":modulename", $moduleName)
      ->queryOne();
   $activeparent = $pttab['parent'];
}

// Get base URL
$url = Yii::$app->request->baseUrl;



// Define the checkTab function
function checkTab($parentId, $id,$activeroleId)
{
   // Build the query using Yii 2's ActiveRecord and createCommand
   $submenu = Yii::$app->db->createCommand(" 
         select 
            default_view,
            tab.name AS tabname,
            tab.tablabel AS tablabel,
            parenttab_label AS parentname
        from parenttab
        join  tab on tab.parent = parenttab.parenttabid
        join  profile2tab on profile2tab.tabid = tab.tabid
        join  profile on profile.profileid = profile2tab.profileid
        join  role2profile on role2profile.profileid = profile.profileid
        join  role on role.roleid = role2profile.roleid
        join  user2role on user2role.roleid = role.roleid
        join  user on user.id = user2role.userid
         where 
            presence = 0 AND
            user.id = $id AND
            user2role.roleid = '$activeroleId' AND
            permissions = 0 AND
            tab.parent = $parentId
            group by tab.tabid
         order By tabsequence")
      ->queryAll();

   // Return whether there are submenu items or not
   return count($submenu) > 0 ? 1 : 0;
}

// Access the session
$session = Yii::$app->session;
$session->open();

// Get the user ID from session
$id = Yii::$app->user->id;
$activeroleId = Yii::$app->session->get('active_profile_id');

// Query the database to check if the user is an admin
$isadmin = 0;
// echo "SELECT profileid FROM role2profile rp join user2role ur on rp.roleid = ur.roleid WHERE  ur.userid = $id";die;
$profilerr = Yii::$app->db->createCommand("SELECT profileid FROM role2profile rp join user2role ur on rp.roleid = ur.roleid WHERE  ur.userid = :uid and ur.roleid=:roleid")
   ->bindValue(':uid', $id)
   ->bindValue(':roleid', $activeroleId)
   ->queryOne();

$profileid = $profilerr['profileid'];
//now check for global action
$hasadminpower = Yii::$app->db->createCommand("SELECT count(*) as cnt FROM `profile2globalpermissions` where globalactionid in (1,2) and globalactionpermission=0 and profileid = :profileid")
   ->bindValue(':profileid', $profileid)
   ->queryOne();
// echo $hasadminpower['cnt'];die;
//print_r($hasadminpower);die;
if ($hasadminpower['cnt'] == 2) {
   $isadmin = 1;
   $access = 1;
}
//added on 08 jan 2025
//check from user table if admin
if ($isadmin == 0) {
   $hasadminpower = Yii::$app->db->createCommand("SELECT is_admin FROM `user` where  id = :uid")
      ->bindValue(':uid', $id)
      ->queryOne();
   $isadmin = $hasadminpower['is_admin'];
}

$idres = $isadmin ?? null; // Access is_admin safely


// Get all parent menus
$menu = Yii::$app->db->createCommand(
   "select parenttabid , icon,parenttab_label as name,parenttab_label, sequence
     from parenttab WHERE visible = 0 order by sequence"
)
   ->queryAll();

// If user is not an admin
if ($idres != '1') {
   $parentMenus = $menu;
   $menu = [];
   foreach ($parentMenus as $parentMenu) {
      $parentId = $parentMenu["sequence"];

      // Check if this parent has tabs
      $menuCheck = checkTab($parentId, $id,$activeroleId);  // Assuming checkTab() is defined elsewhere
      if ($menuCheck == 1) {
         $menu[] = array("name" => $parentMenu["name"], "parenttabid" => $parentMenu['parenttabid'], "icon" => $parentMenu['icon'], "parenttab_label" => $parentMenu['name']);
      }
   }
}
// print_r($menu);die;


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
   <title><?= Html::encode($this->title) ?></title>
   <link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/style-one.css">
   <!-- <link rel="stylesheet"
      href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css"
      integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ=="
      crossorigin="anonymous" referrerpolicy="no-referrer" /> -->

   <meta name="csrf-param" content="<?= Yii::$app->request->csrfParam ?>">
   <meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
    <?php
     $themes = (new \yii\db\Query())
    ->select(['id', 'name'])
    ->from('theme')
    ->where(['active' => 1])
    ->all();
   $themeId = Yii::$app->session->get('_theme_id');

   if (!$themeId && !Yii::$app->user->isGuest) {
      $themeId = (new \yii\db\Query())
         ->select('theme')
         ->from('user')
         ->where(['id' => Yii::$app->user->id])
         ->scalar();
   }

   if (!$themeId) {
      $themeId = (new \yii\db\Query())
         ->select('id')
         ->from('theme')
         ->where(['active' => 1])
         ->orderBy(['id' => SORT_ASC])
         ->scalar();
   }

   $themeRow = null;
   if ($themeId) {
      $themeRow = (new \yii\db\Query())
         ->select(['primary', 'secondary', 'tertiary'])
         ->from('theme')
         ->where(['id' => $themeId, 'active' => 1])
         ->one();
   }

   $primary   = $themeRow['primary']   ?? '#5c9cff';
   $secondary = $themeRow['secondary'] ?? '#f1f4f9';
   $tertiary  = $themeRow['tertiary']  ?? '#ffffff';

    $this->registerCss("
        :root {
            --color-primary: {$primary};
            --color-secondary: {$secondary};
            --color-tertiary: {$tertiary};
        }
    ");
    ?>
   <?php $this->head() ?>
</head>
 
<body class="mainbody">
   <?php $this->beginBody() ?>
  

   <input type="checkbox" id="menu-toggle">
   <input type="checkbox" id="menu-toggle">
   <div class="sidebar-d">
      <!--<div class="side-header">
            <h3>M<span>odern</span></h3>
            </div>-->
      <div class="side-content-d">
         <!--<div class="profile">
               <div class="profile-img bg-img" style="background-image: url(img/3.jpeg)"></div>
               <h4>David Green</h4>
               <small>Art Director</small>
               </div>-->
         <div class="side-menu-d">
            <ul class="sm">
               <li>
                  <a href="<?= $baseUrl; ?>" <?php if ($activeparent === "home")
                                                echo "class='active'"; ?>>
                                                <span class="icons-coll">
                                                       <?= SvgRenderHelper::renderIcon('streamline-home-4.svg'); ?>
                                                </span>
                     <small class="sm-dp">Home</small>
                  </a>
               </li>
               <?php
               if (!empty($menu)) {
                  foreach ($menu as $value) {
                     if ($value['parenttabid'] == $activeparent) {
                        $act = "active";
                        // If user is an admin
                        if ($idres == '1') {
                           // Query for the tab list available for admins
                           $tabList = Yii::$app->db->createCommand("select *
                           from tab
                           where presence = 0
                           order By tabsequence")
                              ->queryAll();

                           // Query for the submenu
                           $submenu = Yii::$app->db->createCommand("
                           select tabid, default_view, tab.name as tabname, tab.tablabel as tablabel, parenttab_label as parentname,submenu,submenu_label
                           from parenttab 
                           join tab on tab.parent = parenttab.parenttabid
                           left join submenu on submenu.submenu_id = tab.submenu
                           where presence = 0 and tab.visible=0 and FIND_IN_SET(:parent, parent)
                           order By tabsequence")
                              ->bindValue(":parent", $value['parenttabid'])
                              ->queryAll();
                        } else {
                           // $sql_tab = "select * from tab where parent = :parent and presence = 0 and visible=0";
                           // $sql_tab = "SELECT tabid, name, parent, submenu,submenu_label
                           // FROM tab left join submenu on submenu.submenu_id = tab.submenu where parent = :parent
                           // ORDER BY submenu IS NOT NULL DESC, tabid";
                           // Query for the tab list available for non-admin user
                           $tabList = Yii::$app->db->createCommand("
                              select *
                              from tab
                              join profile2tab on profile2tab.tabid = tab.tabid
                              join profile on profile.profileid = profile2tab.profileid
                              join role2profile on role2profile.profileid = profile.profileid
                              join role on role.roleid = role2profile.roleid
                              join user2role on user2role.roleid = role.roleid
                              join user on user.id = user2role.userid
                              left join submenu on submenu.submenu_id = tab.submenu
                              where presence = 0 and tab.visible=0
                              and id = :id 
                              and user2role.roleid = :roleid 
                              and permissions = 0
                              group by tab.tabid
                           
                              order By tabsequence")
                              ->bindValue(":id", $id)
                              ->bindValue(":roleid", $activeroleId)
                              ->queryAll();

                           // Query for the submenu for non-admin user
                           $submenu = Yii::$app->db->createCommand("select tab.tabid, default_view, tab.name as tabname, tab.tablabel as tablabel, parenttab_label as parentname,submenu,submenu_label from parenttab
                           join  tab on tab.parent = parenttab.parenttabid
                           join  profile2tab on profile2tab.tabid = tab.tabid
                           join  profile on profile.profileid = profile2tab.profileid
                           join  role2profile on role2profile.profileid = profile.profileid
                           join  role on role.roleid = role2profile.roleid
                           join  user2role on user2role.roleid = role.roleid
                           join  user on user.id = user2role.userid
                           left join submenu on submenu.submenu_id = tab.submenu
                           where presence =0 and tab.visible=0
                           and user.id = :id
                           and user2role.roleid = :roleid                            
                           and permissions = 0 and FIND_IN_SET(:parent, parent)
                           group by tab.tabid
                           order By tabsequence")
                              ->bindValue(":parent", $value['parenttabid'])
                              ->bindValue(":id", $id)
                              ->bindValue(":roleid", $activeroleId)
                              ->queryAll();
                        }
                     } else
                        $act = '';
                     //get first active tab
                     if($isadmin == 1)
                     {
                     $sql = "select name,default_view from tab where FIND_IN_SET(:parent, parent) and presence = 0 and visible= 0 ";
                     $pttab = Yii::$app->db->createCommand($sql)
                     ->bindValue(":parent", $value['parenttabid'])
                     ->queryOne();
                     }
                     else
                     {
                     $sql = "select name,default_view from tab where FIND_IN_SET(:parent, parent) and presence = 0 and visible= 0 and tabid in (select tabid from profile2tab where profileid = :profileid)";
                     $pttab = Yii::$app->db->createCommand($sql)
                        ->bindValue(":parent", $value['parenttabid'])
                        ->bindValue(":profileid", $profileid)
                        ->queryOne();
                     }
                     $lnk = '<a>';
                     if (!empty($pttab)) {
                        $activetab = $pttab['name'];
                        $default_view = $pttab['default_view'];
                        $lnk = "<a href='" . $baseUrl . $activetab . "/" . $default_view . "'  class='" . $act . "' title='".$value['parenttab_label']."'>";
                     }
               ?>
                     <li>
                        <?= $lnk; ?>
                         <span class="icons-coll">
                            <?= SvgRenderHelper::renderIcon($value['icon']); ?>
                        </span>
                        <small class="sm-dp"><?= $value['parenttab_label']; ?></small>
                        </a>
                     </li>
               <?php
                  }
               }
               ?>
            </ul>
         </div>
      </div>
   </div>
   <div class="main-content-d">
       <header>
            <label for="menu-toggle">
               <i class="fa fa-bars"></i>
            </label>
             <?php if (!empty($siteSetting->logo_path)): ?>
    <div class="Logo-1">
        <?= Html::img(
            Yii::getAlias('@web') . $siteSetting->logo_path,
            ['alt' => $siteSetting->company, 'style' => '',"class"=>"img-lg-dp"]
        ) ?>
    </div>
<?php endif; ?>

             <form role="search" method="get" id="searchForminallmodule" class="search-form form" action="<?= Yii::$app->request->baseUrl; ?>/leads/searchinallmodule?pageNumber=0">
                
                <!-- Search Icon & Input -->
                <div class="search-input-group">
                   <button type="button" class="search_all_btn" id="search_btn">
                      <span class="icons-col-fill-search">
                         <?= SvgRenderHelper::renderIcon('iconamoon-search-thin.svg'); ?>
                      </span>
                   </button>
                   <input type="search" class="search-field" placeholder="Search..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" name="search" title="" id="searchinallmodule" />
                </div>

                <?php
                if($isadmin == 1){
                   $tabs = Tab::find()->where(['presence' => 0,'search_allowed' =>1])->all();
                }else{
                   $tabs = Tab::find()
                   ->alias('t')
                   ->join('INNER JOIN', 'profile2tab p2t', 'p2t.tabid = t.tabid')
                   ->join('INNER JOIN', 'profile p', 'p.profileid = p2t.profileid')
                   ->join('INNER JOIN', 'role2profile r2p', 'r2p.profileid = p.profileid')
                   ->join('INNER JOIN', 'role r', 'r.roleid = r2p.roleid')
                   ->join('INNER JOIN', 'user2role u2r', 'u2r.roleid = r.roleid')
                   ->join('INNER JOIN', 'user u', 'u.id = u2r.userid')
                   ->join('LEFT JOIN', 'submenu s', 's.submenu_id = t.submenu')
                   ->where([
                      't.presence' => 0,
                      't.visible' => 0,
                      'u.id' => $id,
                      'u2r.roleid' => $activeroleId,
                      'p2t.permissions' => 0,
                      't.search_allowed' => 1
                   ])
                   ->groupBy('t.tabid')
                   ->orderBy('t.tabsequence')
                   ->all();
                }
                $selectedModule = isset($_GET['selectedmodule']) ? $_GET['selectedmodule'] : 'all';
                $seltab =  Tab::find()->select(['tablabel','tabid'])->where(['tablabel'=>$selectedModule])->one();
                $selectedModuleLabel = ($selectedModule == 'all') ? 'All Module' : ($seltab->tablabel ?? 'All Module');
                ?>

                <!-- Module Selection -->
                <div class="module-dropdown-container">
                   <div id="selectedModuleDisplay" class="selected-module">
                         <span class="icons-col-fill-search search-icon" id="moduleDropdownToggle" style="cursor: pointer;">
                              <?= SvgRenderHelper::renderIcon('ic-round-arrow-left-2.svg'); ?> 
                         </span>
                   </div>

                   <div id="moduleDropdown_search" class="module-dropdown search-all-dropdown">
                         <div class="search-all-ui-parent">
                            <ul class="search-all-ui">
                               <li class="module-option <?= ($selectedModule == "all") ? "liselected" : ""; ?>" data-value="all" onclick="selectModule(this)">All Module</li>
                               <?php foreach ($tabs as $tab) : ?>
                                     <li class="module-option <?= ($selectedModule == $tab->tablabel) ? "liselected" : ""; ?>" data-value="<?= $tab->tablabel; ?>" id="<?= $tab->tabid; ?>" onclick="selectModule(this)">
                                        <?= $tab->tablabel; ?>
                                     </li>
                               <?php endforeach; ?>
                            </ul>
                         </div>
                   </div>
                </div>
                <input type="hidden" name="selectedmodule" id="selectedmodule" value="<?= $selectedModule; ?>">
                <input type="hidden" name="tabid" id="selectedid" value="<?= ($selectedModule == 'all') ? 'all' : $seltab->tabid; ?>">
             </form>

             <!-- Notification Icon -->
             <div class="notify-icon">
                <span  class="icons-col-fill-nofity" id="notification-btn">
                  <?= SvgRenderHelper::renderIcon('clarity-notification-solid.svg'); ?>
                </span>
                <span id="notification-count" class="notify" style="background: red;"></span>
             </div>

             <!-- Notification Dropdown -->
             <div id="notification-dropdown" class="dropdown-notif">
                <div class="dropdown-header">
                   <span style="color:var(--color-primary) !important;">Notifications</span>
                   <span id="close-notifications" style="float:right; cursor: pointer;color:var(--color-primary) !important;">&times;</span>
                </div>
                <div id="notification-list">
                   <p style="color:var(--color-primary) !important;" class="no-notifications">You don't have any notifications right now.</p>
                </div>
             </div>

             <div class="Profile-w1">
                <div class="dropdown-container">
                   <details class="dropdown right">
                      <summary class="avatar">
                         <div title="Profile" class="profile-img-div"><img
                               src="<?= $baseUrl; ?>/thememain/profile/<?= Html::encode($userimage); ?>" width="25"
                               height="25" alt="User Image">
                            <br>
                            <div><?= $username ?></div>
                         </div>
                      </summary>

                      <ul>
                         <li>
                            <div class="user-info">
                               <div class="avatar-profile"><img class="search-icon"
                                     src="<?= $baseUrl; ?>/thememain/profile/<?= Html::encode($userimage); ?>"
                                     width="25" height="25" alt="User Image"></div>

                               <div class="details">
                                  <div class="name"><?= Html::encode($username); ?></div>
                                  <div class="email"><?= Html::encode($useremail); ?></div>
                                  <div class="role"><?= Html::encode($profilename); ?></div>
                               </div>
                            </div>
                         </li>
                         <li>
                            <a href="<?= $baseUrl; ?>passwordupdate/profileview">
                               <span class="material-symbols-outlined"><i class="fa-solid fa-user"
                                     style="color: var(--color-primary) !important;"></i> My Profile</span>
                            </a>
                         </li>
                         <li>
                            <a href="#">
                               <span class="material-symbols-outlined"><i class="fa-solid fa-gear"
                                     style="color: var(--color-primary) !important;"></i> settings</span></a>
                         </li>
                         <li>
                            <a href="<?= $baseUrl; ?>site/logout">
                               <span class="material-symbols-outlined"><i class="fa-solid fa-arrow-right-from-bracket"
                                     style="color: var(--color-primary) !important;"></i> Logout</span>
                            </a>
                         </li>
                      </ul>
                   </details>
                </div>
             </div>
      </header>
      <main>
         <div class="page-header-d">
            <div id="myDIV">
               <?php
               $menus_with_submenu = [];
               $menus_without_submenu = [];
               if (!empty($submenu)) {
                  foreach ($submenu as $value) {
                     $actcl = '';
                     if ($value['tabname'] === $moduleName)
                        $actcl = "active";


                     if ($value['submenu'] !== null) {
                        $menus_with_submenu[] = $value;
                     } else {
                        $menus_without_submenu[] = $value;
                     }
                  }
               }

               $grouped_menus = [];

               foreach ($menus_with_submenu as $menu) {
                  $grouped_menus[$menu['submenu_label']][] = $menu;  
               }


               ?>

               <div class="navbar-2">
                  <?php

                  foreach ($grouped_menus as $parent_menu => $submenus) {
                     $actcl = '';
                     // print_r($value);die;
                     // if ($submenu['tabname'] === $moduleName)
                     //    $actcl = "active";
                     $searchTerm = $moduleName;
                     $found = false;

                     foreach ($submenus as $item) {
                        if (strpos($item['tabname'], $searchTerm) !== false) {
                           $found = true;
                           break; // Exit loop as we've found the match
                        }
                     }
                     $actcl = '';
                     if ($found) {
                        $actcl = "active";
                     }

                  ?>
                     <div class="dropdown">
                        <button class="dropbtn <?= $actcl; ?>"><?= $parent_menu; ?> <i
                              class="fa-solid fa-angle-down"></i></button>
                        <div class="dropdown-content">
                           <?php
                           foreach ($submenus as $submenu) {

                              echo '<a href="' . $baseUrl . $submenu['tabname'] . '/' . $submenu['default_view'] . '">' . $submenu['tablabel'] . '</a>';
                           } ?>
                        </div>
                     </div>
                  <?php
                  }
                  
if ($uid !== 1) {
   echo '<div class="navbar-main-items" id="navbarMainItems">';
   foreach ($menus_without_submenu as $menuval) {
      $actcl = '';
      $isSameController = ($menuval['tabname'] === $moduleName);
      $isSameView       = ($menuval['default_view'] === $action);
      if ($isSameController && $isSameView) {
         $actcl = 'active';
      }

      echo '<a class="' . $actcl . '" href="' . $baseUrl . $menuval['tabname'] . '/' . $menuval['default_view'] . '">' . $menuval['tablabel'] . ' <i class="fa-solid fa-angle-down"></i></a>';
      
   }
   echo '</div>';

   ?>
   <div class="dropdown" id="navbarMore" style="display:none;">
      <button class="dropbtn">More <i class="fa-solid fa-angle-down"></i></button>
      <div class="dropdown-content" id="navbarMoreContent"></div>
   </div>
   <?php
} else {
   echo '<div class="navbar-main-items" id="navbarMainItems">';
   foreach ($menus_without_submenu as $menuval) {
      $actcl = '';
      $isSameController = ($menuval['tabname'] === $moduleName);
      $isSameView       = ($menuval['default_view'] === $action);
      if ($isSameController && $isSameView) {
         $actcl = 'active';
      }

      echo '<a class="navbar-item ' . $actcl . '" href="' . $baseUrl . $menuval['tabname'] . '/' . $menuval['default_view'] . '">' . $menuval['tablabel'] . ' <i class="fa-solid fa-angle-down"></i></a>';
   }
   echo '</div>';

   ?>
   <div class="dropdown" id="navbarMore" style="display:none;">
      <button class="dropbtn">More <i class="fa-solid fa-angle-down"></i></button>
      <div class="dropdown-content" id="navbarMoreContent"></div>
   </div>
   <?php
}
?>
                  


               </div>

            </div>
            <!-- cod efor dashboard -->
             <?php if($moduleName == "site")
             {
               // echo "<pre>";print_r(Yii::$app->user->id);die;
               $profilename = Yii::$app->db->createCommand("
                 SELECT 
                           u.*, 
                           r.*, 
                           p.profilename
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        JOIN role r ON r.roleid = ur.roleid
                        JOIN role2profile rp ON rp.roleid = r.roleid
                        JOIN profile p ON p.profileid = rp.profileid
                        WHERE u.id = :uid and  ur.roleid = :roleid
                     ")
                     ->bindValue(':uid',Yii::$app->user->id)
                     ->bindValue(':roleid',$activeroleId)
                     ->queryOne();
                    echo "<div class='navbar-2'>
                           <a href='#' class='active'>".$profilename['profilename']."</a>
                        </div>";?>
            <?php }?>
             <!-- end code for dashboard -->
         </div>
         <script>
            // Add active class to the current button (highlight it)
            var header = document.getElementById("myDIV");
            var btns = header.getElementsByClassName("btn");
            for (var i = 0; i < btns.length; i++) {
               btns[i].addEventListener("click", function() {
                  var current = document.getElementsByClassName("active");
                  current[0].className = current[0].className.replace(" active", "");
                  this.className += " active";
               });
            }
         </script>
         <div id="loading-overlay">
            <div class="loading-spinner"></div>
         </div>
         <?= Alert::widget() ?>
         
         <?= $content; ?>
   </div>

   <?php $this->endBody() ?>
</body>




<!-- modal for call meeting task document -->
<!-- Modal -->
<div class="modal-overlay-1" id="callmodal">
   <div class="modal-1">


   </div>
</div>
<!-- modal for reference -->
<div class="modal fade" id="modal22" tabindex="-1" role="dialog" aria-labelledby="modalreferencelabel"
   aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">



      </div>
   </div>
</div>
<div class="modal fade" id="myModalMulti" tabindex="-1" role="dialog" aria-labelledby="modalreferencelabel"
   aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content recordPopup">



      </div>
   </div>
</div>

<!-- added by deepika for multi select hidden on 17 oct 2025 -->
<input type="hidden" id="selectmultyids" name="d-selectmultyids" value="">
<!-- added by ptpatel on date 29-03-25 -->
<!-- <script type="text/javascript" src="<?=  $baseUrl;?>thememain/js/searchmodule.js"></script> -->
<?php $this->registerJsFile($baseUrl.'thememain/js/searchmodule.js');?>
<script type="text/javascript" nonce="<?= Yii::$app->params['cspNonce'] ?>" src="<?= $baseUrl; ?>thememain/js/flatpickr.js"></script>
<script type="text/javascript" nonce="<?= Yii::$app->params['cspNonce'] ?>" src="<?= $baseUrl; ?>thememain/js/tetra/setparentforpopup.js"></script>
<script type="text/javascript" nonce="<?= Yii::$app->params['cspNonce'] ?>" src="<?= $baseUrl; ?>thememain/js/tetra/setparentformultipopup.js"></script>
<script type="text/javascript" nonce="<?= Yii::$app->params['cspNonce'] ?>" src="<?= $baseUrl; ?>thememain/js/tetra/setdate.js"></script>
<?php //echo "hi";die ?>
<?php if($moduleName == "profile"){?>
   <link href="<?= $baseUrl;?>/thememain/css/bootstrap.min.css" rel="stylesheet">
   <link href="<?= $baseUrl;?>/thememain/css/multiple.css" rel="stylesheet">
   <link href="<?= $baseUrl;?>/thememain/css/select2.min.css" rel="stylesheet">
   <link href="<?= $baseUrl;?>/thememain/css/multilist-dd.css" rel="stylesheet">
   <link href="<?= $baseUrl;?>/thememain/css/jquery.dataTables.min.css">

   <script type="text/javascript" src="<?= $baseUrl;?>thememain/jquery/jquery.min.js"></script>
   <script type="text/javascript" src="<?= $baseUrl;?>thememain/bootstrap/bootstrap.min.js"></script>
   <script type="text/javascript" src="<?= $baseUrl;?>thememain/js/select2.min.js"></script>
   <script type="text/javascript" src="<?= $baseUrl;?>thememain/js/tetra/multilist-dd.js"></script>
   <script type="text/javascript" src="<?= $baseUrl;?>thememain/js/jquery.dataTables.min.js"></script> 

<?php  }
//added for convertlead.js
if($moduleName == "leads" && $action == 'detail'){
   ?>
   <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/select2.min.js"></script>
   <link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/select2.min.css">
   <link rel="stylesheet" href="<?= $baseUrl; ?>/thememain/css/multilist-dd.css">
   <?php if(isset($scriptPath)) {?>
   <script type="text/javascript" src="<?= $scriptPath ?>"></script>
   <?php } ?>
   <script type="text/javascript" src="<?= $baseUrl; ?>theme/libs/pristinejs/pristinejs.min.js"></script>
   <script type="text/javascript" src="<?= $baseUrl; ?>theme/js/pages/form-validation.init.js"></script>
   <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/editview.js"></script>
   <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/single-dd.js"></script>
   <script type="text/javascript" src="<?= $baseUrl; ?>thememain/js/tetra/multilist-dd.js"></script>
   <script src="<?= $baseUrl; ?>thememain/js/tetra/validator.js"></script>
   <script type="text/javascript" src="<?= $baseUrl ?>js/leads/convertlead.js"></script>
   <?php
}
?>
</html>
<?php $this->endPage();
die; ?>