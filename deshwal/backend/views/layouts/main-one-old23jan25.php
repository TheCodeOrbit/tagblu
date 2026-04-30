<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL);

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use backend\assets\AdminAsset;
use backend\models\AccessCheck;

// AdminAsset::register($this);
$baseUrl = Yii::$app->HomeUrl;
$model = new AccessCheck();
$id = Yii::$app->user->id;
$moduleName = Yii::$app->controller->module->id;
if ($moduleName === "app-backend")
   $moduleName = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;
// echo $action;die;
if(($moduleName=="site" && $action="index") || $action == "profileview")
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

<?php
$uid = Yii::$app->user->id;

// Fetch the profile name and user name
$userData = Yii::$app->db->createCommand("
                              SELECT profile.profilename, user.first_name, user.last_name, user.email,user.profilepic 
                              FROM profile
                              JOIN profile2tab ON profile2tab.profileid = profile.profileid
                              JOIN role2profile ON role2profile.profileid = profile.profileid
                              JOIN role ON role.roleid = role2profile.roleid
                              JOIN user2role ON user2role.roleid = role.roleid
                              JOIN user ON user.id = user2role.userid
                              WHERE user.id = :uid
                              LIMIT 1
                          ")
   ->bindValue(':uid', $uid)
   ->queryOne();

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
if(($moduleName == 'site' && $action == "index")|| $action == "profileview")
{
   $activeparent = "home";
}
else{
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
function checkTab($parentId, $id)
{
   // Build the query using Yii 2's ActiveRecord and createCommand
   $submenu = Yii::$app->db->createCommand(" 
         select 
            default_view,
            tab.name AS tabname,
            tab.tablabel AS tablabel,
            parenttab_label AS parentname
        from parenttab
        join  tab on tab.parent = parenttab.sequence
        join  profile2tab on profile2tab.tabid = tab.tabid
        join  profile on profile.profileid = profile2tab.profileid
        join  role2profile on role2profile.profileid = profile.profileid
        join  role on role.roleid = role2profile.roleid
        join  user2role on user2role.roleid = role.roleid
        join  user on user.id = user2role.userid
         where 
            presence = 0 AND
            user.id = $id AND
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

// Query the database to check if the user is an admin
$isadmin = 0;
// echo "SELECT profileid FROM role2profile rp join user2role ur on rp.roleid = ur.roleid WHERE  ur.userid = $id";die;
$profilerr = Yii::$app->db->createCommand("SELECT profileid FROM role2profile rp join user2role ur on rp.roleid = ur.roleid WHERE  ur.userid = :uid")
   ->bindValue(':uid', $id)
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
if($isadmin == 0)
{
   $hasadminpower = Yii::$app->db->createCommand("SELECT is_admin FROM `user` where  id = :uid")
   ->bindValue(':uid', $id)
   ->queryOne();
$isadmin = $hasadminpower['is_admin'];

}

$idres = $isadmin ?? null; // Access is_admin safely


// Get all parent menus
$menu = Yii::$app->db->createCommand(
   "select parenttabid , icon,parenttab_label as name,parenttab_label, sequence
     from parenttab"
)
   ->queryAll();

// If user is not an admin
if ($idres != '1') {
   $parentMenus = $menu;
   $menu = [];
   foreach ($parentMenus as $parentMenu) {
      $parentId = $parentMenu["sequence"];

      // Check if this parent has tabs
      $menuCheck = checkTab($parentId, $id);  // Assuming checkTab() is defined elsewhere
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
                  <a href="<?= $baseUrl; ?>" <?php if($activeparent === "home") echo "class='active'";?>>
                     <img src="<?= $baseUrl; ?>/thememain/img/streamline-home-4.svg">
                     <small>Home</small>
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
                           join tab on tab.parent = parenttab.sequence
                           left join submenu on submenu.submenu_id = tab.submenu
                           where presence = 0 and FIND_IN_SET(:parent, parent)
                           order By tabsequence")
                           ->bindValue(":parent",$value['parenttabid'])
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
                              where presence = 0
                              and id = :id and permissions = 0
                              group by tab.tabid
                           
                              order By tabsequence")
                           ->bindValue(":id",$id)
                              ->queryAll();

                           // Query for the submenu for non-admin user
                           $submenu = Yii::$app->db->createCommand("select tab.tabid, default_view, tab.name as tabname, tab.tablabel as tablabel, parenttab_label as parentname,submenu,submenu_label from parenttab
                           join  tab on tab.parent = parenttab.sequence
                           join  profile2tab on profile2tab.tabid = tab.tabid
                           join  profile on profile.profileid = profile2tab.profileid
                           join  role2profile on role2profile.profileid = profile.profileid
                           join  role on role.roleid = role2profile.roleid
                           join  user2role on user2role.roleid = role.roleid
                           join  user on user.id = user2role.userid
                           left join submenu on submenu.submenu_id = tab.submenu
                           where presence =0
                           and user.id = :id and permissions = 0 and FIND_IN_SET(:parent, parent)
                           group by tab.tabid
                           order By tabsequence")
                           ->bindValue(":parent",$value['parenttabid'])
                           ->bindValue(":id",$id)
                              ->queryAll();


                        }

                     } else
                        $act = '';
                     //get first active tab
                     $sql = "select name,default_view from tab where FIND_IN_SET(:parent, parent) and presence = 0 and visible= 0";
                     $pttab = Yii::$app->db->createCommand($sql)
                        ->bindValue(":parent", $value['parenttabid'])
                        ->queryOne();
                     $lnk = '<a>';
                     if (!empty($pttab)) {
                        $activetab = $pttab['name'];
                        $default_view = $pttab['default_view'];
                        $lnk = "<a href='" . $baseUrl . $activetab . "/".$default_view."'  class='" . $act . "'>";
                     }
                     ?>
                     <li>
                        <?= $lnk; ?>
                        <img src="<?= $baseUrl; ?>/thememain/img/<?= $value['icon']; ?>">
                        <small><?= $value['parenttab_label']; ?></small>
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
         <div class="header-content">
            <label for="menu-toggle">
               <!-- <span class="las la-bars"></span> -->
               <i class="fa fa-bars"></i>
            </label>
            <div class="Logo-1">LOGO</div>
            <div class="header-menu">
               <div class="search_2">
                  <div class="wrapper">
                     <div class="container">
                        <form role="search" method="get" class="search-form form" action="">
                           <input type="search" class="search-field" placeholder="Search..." value="" name="s"
                              title="" />
                           <img src="<?= $baseUrl; ?>/thememain/img/iconamoon-search-thin.svg">
                           <img src="<?= $baseUrl; ?>/thememain/img/vector-73.svg">
                           <img src="<?= $baseUrl; ?>/thememain/img/ic-round-arrow-left-2.svg">
                        </form>
                     </div>
                  </div>
               </div>
               <div class="notify-icon">
                  <img src="<?= $baseUrl; ?>/thememain/img/ph-plus-fill.svg">
                  <!--<span class="notify">3</span>-->
               </div>
               <div class="notify-icon">
                  <img src="<?= $baseUrl; ?>/thememain/img/mingcute-announcement-fill.svg">
                  <!--<span class="notify">3</span>-->
               </div>
               <div class="notify-icon">
                  <img src="<?= $baseUrl; ?>/thememain/img/clarity-notification-solid.svg">
                  <!--<span class="notify">3</span>-->
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
                           <!-- Optional: user details area w/ gray bg -->
                           <li>
                              <div class="user-info">
                                 <div class="avatar-profile"><img
                                       src="<?= $baseUrl; ?>/thememain/profile/<?= Html::encode($userimage); ?>"
                                       width="25" height="25" alt="User Image"></div>

                                 <div class="details">
                                    <div class="name"><?= Html::encode($username); ?></div>
                                    <div class="email"><?= Html::encode($useremail); ?></div>
                                    <div class="role"><?= Html::encode($profilename); ?></div>
                                 </div>
                              </div>

                           </li>
                           <!-- Menu links -->
                           <li>
                              <a href="<?= $baseUrl; ?>passwordupdate/profileview">
                                 <span class="material-symbols-outlined"><i class="fa-solid fa-user"
                                       style="color: #3c77ff;"></i> My Profile</span>
                              </a>
                           </li>
                           <li>
                              <a href="#">
                                 <span class="material-symbols-outlined"><i class="fa-solid fa-gear"
                                       style="color: #3c77ff;"></i> settings</span></a>
                           </li>
                           <li>
                              <a href="<?= $baseUrl; ?>site/logout">
                                 <span class="material-symbols-outlined"><i class="fa-solid fa-arrow-right-from-bracket"
                                       style="color: #3c77ff;"></i> Logout</span>
                              </a>
                           </li>
                           <!-- Optional divider -->


                        </ul>
                     </details>
                  </div>

               </div>

            </div>
         </div>
      </header>
      <main>
         <div class="page-header-d">
            <div id="myDIV">
               <?php
               // echo $moduleName;
               //get all tabs of current parent ab
               // commented on 22 jan 2025
               // $sql_tab = "select * from tab where parent = :parent and presence = 0 and visible=0";
               $sql_tab = "SELECT tabid, name, parent, submenu,submenu_label
FROM tab left join submenu on submenu.submenu_id = tab.submenu where parent = :parent
ORDER BY submenu IS NOT NULL DESC, tabid";
               $tab_result = Yii::$app->db->createCommand($sql_tab)->bindValue(":parent", $activeparent)->queryAll();
               // Arrays to hold menus with and without submenus
$menus_with_submenu = [];
$menus_without_submenu = [];
               if (!empty($submenu)) {
                  foreach ($submenu as $value) {
                     $actcl = '';
                     // print_r($value);die;
                     if ($value['tabname'] === $moduleName)
                        $actcl = "active";


                        if ($value['submenu'] !== null) {
                           // Menu with submenu, add it to the "with submenu" array
                           $menus_with_submenu[] = $value;
                       } else {
                        
                           // Menu without submenu, add it to the "without submenu" array
                           $menus_without_submenu[] = $value;
                       }

                     ?>
                     <button type="button" class="btn-main-nav <?= $actcl; ?>"><a
                           href="<?= $baseUrl . $value['tabname'] ?>/<?= $value['default_view']; ?>"
                           class="active-link <?= $actcl; ?>"><?= $value['tablabel']; ?></a> 
                           <!-- <i class="las la-angle-down"></i> -->
                           <i class="fa-solid fa-angle-down"></i>
                        </button>
                     <?php
                  }
               }
               // echo "<pre>";print_r($menus_without_submenu);
               // Display menus with submenu first
echo "<h2>Menus with Submenus:</h2>";
$grouped_menus = [];

// Group menus with the same parent and submenu
foreach ($menus_with_submenu as $menu) {
    // Grouping by parent
    $grouped_menus[$menu['submenu_label']][] = $menu;  // Use the parent name as the key
}

// Now display the grouped structure

foreach ($grouped_menus as $parent_menu => $submenus) {

?>
    <div class="dropdown">
            <button class="dropbtn"><?= $parent_menu;?> <i class="fa-solid fa-angle-down"></i></button>
            <div class="dropdown-content">
               <?php
                foreach ($submenus as $submenu) {
                 
                echo '<a href="'.$baseUrl . $submenu['tabname'].'/'. $submenu['default_view'].'">'.$submenu['tablabel'] .'</a>';
                
                }?>
            </div>
        </div>
        <?php
}


// Display menus without submenu separately
echo "<h2>Menus without Submenus:</h2>";
echo "<ul>";
foreach ($menus_without_submenu as $menuval) {
    echo "<li>";
   //  echo "TabID: " . $menu['tabid'] . " - " . $menu['tabname'];
    echo '<a href="'.$baseUrl . $menuval['tabname'].'/'. $menuval['default_view'].'">'.$menuval['tablabel'] .'<i class="fa-solid fa-angle-down"></i></a>';
    echo "</li>";
}
echo "</ul>";
               ?>
               
            </div>
         </div>
         <script>
            // Add active class to the current button (highlight it)
            var header = document.getElementById("myDIV");
            var btns = header.getElementsByClassName("btn");
            for (var i = 0; i < btns.length; i++) {
               btns[i].addEventListener("click", function () {
                  var current = document.getElementsByClassName("active");
                  current[0].className = current[0].className.replace(" active", "");
                  this.className += " active";
               });
            }
         </script>
         <div id="loading-overlay">
            <div class="loading-spinner"></div>
         </div>

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


</html>
<?php $this->endPage();
die; ?>