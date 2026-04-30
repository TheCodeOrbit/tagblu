<?php
namespace backend\models;
use Yii;
class AccessCheck
{
    function checkpermission($uid, $ModuleName, $action)
    {
        $activeroleId = Yii::$app->session->get('active_profile_id');
        if (Yii::$app->user->isGuest) {
            if ($ModuleName == 'home' || $action == 'login' || Yii::$app->controller->id == 'site/login') {
                return 1;
            }
            return 0; 
        }
        // echo "you are in accesscheck";die;
        $access = 1;
        $isadmin = 0;
        //only approvelist = 5 added  by ptpatel
        // bulkuplaodtagging = 0 added by ptpatel on date 03-01-2026 to resolve tagging bult update issue
        $opratiionarray = array("create" => "0", "edit" => "1", "edititems" => "1", "delete" => "2", "list" => "3", "detail" => "3","detailnew" => "3","approvelist" => "5",'import' => "4",'export' => "6","exportitems"=>6,"report"=>"3",'assetlist' =>"3","bulkuplaodtagging"=>0,'itemlist' =>3,'itemlists' =>3);//view = detail

        $connection = Yii::$app->db;
        //get profile of user
        $profilerr = Yii::$app->db->createCommand("SELECT profileid FROM role2profile rp join user2role ur on rp.roleid = ur.roleid WHERE  ur.userid = :uid and ur.roleid = :roleid")
            ->bindValue(':uid', $uid)
            ->bindValue(':roleid', $activeroleId)
            ->queryOne();
        $profileid = $profilerr['profileid'];
        
        $hasadminpower = Yii::$app->db->createCommand("SELECT is_admin FROM `user` where  id = :uid")
            ->bindValue(':uid', $uid)
            ->queryOne();
        $isadmin = $hasadminpower['is_admin'];
        if ($isadmin == 1) {
            $access = 1;
        } else {

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
        }
        $controller = strtolower(Yii::$app->controller->id);
        //echo "con = $controller";
        //echo "action = $action";exit;
        //$action=="userprofile";
        //as per new flow no one can create account direclty on date 07-10-2025
        if($ModuleName == 'vendoraccount' && $action == "create")
        {
            $access = 0;
            return $access;
        }
        else if ($isadmin == '1' || $action == "InvalidAccessError" || $ModuleName == 'home' || $controller == 'changepassword' || $controller == 'dashboard' || $controller == "user" || ($action == "searchinallmodule") || $action == "getnotifications" || $action =='itemlist' || $action == 'itemslist') {
            return $access;
        } else {
            $ops = $opratiionarray[$action];
            //echo "action  = $action\n";
            if ($action == "List") {

                $tabList = Yii::$app->db->createCommand("
                    SELECT tab.name AS tabname, tab.tablabel AS tablabel, parenttab.parenttab_label AS parentname
                    FROM parenttab
                    JOIN tab ON tab.parent = parenttab.parenttab_label
                    JOIN profile2tab ON profile2tab.tabid = tab.tabid
                   
                    JOIN profile ON profile.profileid = profile2tab.profileid
                    JOIN role2profile ON role2profile.profileid = profile.profileid
                    JOIN role ON role.roleid = role2profile.roleid
                    JOIN user2role ON user2role.roleid = role.roleid
                    JOIN user ON user.id = user2role.userid
                    WHERE visible = 0 AND user.id = :uid AND user2role.roleid = :roleid  AND tab.name = :ModuleName
                    ORDER BY tab.tabsequence
                ")
                    ->bindValue(':uid', $uid)
                    ->bindValue(':roleid', $activeroleId)
                    ->bindValue(':ModuleName', $ModuleName)
                    ->queryAll();

            } elseif (in_array($ops, $opratiionarray)) {
                $ops = $opratiionarray[$action];
                //echo "hello2";exit;
                $query = " SELECT name,operation,profile2standardpermissions.permissions,role2profile.profileid 
                            FROM profile2standardpermissions
                            JOIN role2profile ON role2profile.profileid = profile2standardpermissions.profileid
                            JOIN role ON role.roleid = role2profile.roleid
                            JOIN user2role ON user2role.roleid = role.roleid
                            JOIN tab ON tab.tabid = profile2standardpermissions.tabid
                            WHERE presence=0 and userid=$uid AND user2role.roleid = '$activeroleId'  and name ='$ModuleName' AND operation='$ops' AND permissions=0";
                $command = $connection->createCommand($query);
                $tabList = $command->queryAll();

            } else {
                $tabList = Yii::$app->db->createCommand("
                    SELECT tab.name AS tabname, tab.tablabel AS tablabel, parenttab.parenttab_label AS parentname
                    FROM parenttab
                    JOIN tab ON tab.parent = parenttab.parenttab_label
                    JOIN profile2tab ON profile2tab.tabid = tab.tabid
                   
                    JOIN profile ON profile.profileid = profile2tab.profileid
                    JOIN role2profile ON role2profile.profileid = profile.profileid
                    JOIN role ON role.roleid = role2profile.roleid
                    JOIN user2role ON user2role.roleid = role.roleid
                    JOIN user ON user.id = user2role.userid
                    WHERE visible = 0 AND user.id = :uid AND user2role.roleid = :roleid AND tab.name = :ModuleName
                    ORDER BY tab.tabsequence
                ")
                    ->bindValue(':uid', $uid)
                    ->bindValue(':roleid', $activeroleId)
                    ->bindValue(':ModuleName', $ModuleName)
                    ->queryAll();


            }


            if (!empty($tabList)) {
                $access = 1;
            } else {
                $access = 0;
            }
           


            return $access;
        }



    }
    public function tabs($uid, $ModuleName)
    {
        $activeroleId = Yii::$app->session->get('active_profile_id');

        try {
            //check if admin
            $ModuleName = ucfirst($ModuleName);
            $connection = Yii::$app->db;
            $tabList = Yii::$app->db->createCommand("
                        SELECT tab.tabid 
                        FROM tab
                        JOIN profile2tab ON profile2tab.tabid = tab.tabid
                        JOIN profile ON profile.profileid = profile2tab.profileid
                        JOIN role2profile ON role2profile.profileid = profile.profileid
                        JOIN role ON role.roleid = role2profile.roleid
                        JOIN user2role ON user2role.roleid = role.roleid
                        JOIN user ON user.id = user2role.userid
                        WHERE tab.presence = 0 AND user.id = :uid AND user2role.roleid=:roleid AND tab.name = :ModuleName
                        ORDER BY tab.tabsequence
                    ")
                ->bindValue(':uid', $uid)
                ->bindValue(':roleid', $activeroleId)
                ->bindValue(':ModuleName', $ModuleName)
                ->queryOne();
            // print_r($tabList);die;
            // echo "
            //     SELECT tab.tabid 
            //     FROM tab
            //     JOIN profile2tab ON profile2tab.tabid = tab.tabid
            //     JOIN profile ON profile.profileid = profile2tab.profileid
            //     JOIN role2profile ON role2profile.profileid = profile.profileid
            //     JOIN role ON role.roleid = role2profile.roleid
            //     JOIN user2role ON user2role.roleid = role.roleid
            //     JOIN user ON user.id = user2role.userid
            //     WHERE tab.presence = 0 AND user.id = $uid AND tab.name = '$ModuleName'
            //     ORDER BY tab.tabsequence
            // ";die;

            $tabid = $tabList['tabid'] ?? "";
            return $tabid;
        } catch (Exception $e) {
            echo "some error has occured";
        }
    }
    public function profile($uid, $tabs, $ModuleName)
    {
        $ModuleName = ucfirst($ModuleName);
        $activeroleId = Yii::$app->session->get('active_profile_id');

        try {
            $connection = Yii::$app->db;
            $profilelist = Yii::$app->db->createCommand("select profile2tab.profileid as profileid,profilename from tab join profile2tab on profile2tab.tabid = tab.tabid  
                    join profile on profile.profileid =profile2tab.profileid  
                    join role2profile on role2profile.profileid = profile.profileid  
                    join role on role.roleid = role2profile.roleid  
                    join user2role on user2role.roleid = role.roleid  
                    join user  on user .id=user2role.userid  
                    where presence=0 and id=:uid and user2role.roleid=:roleid and tab.tabid=:tabs
                    order by tab.tabsequence")
                ->bindValue(':uid', $uid)
                ->bindValue(':roleid', $activeroleId)
                ->bindValue(':tabs', $tabs)
                ->queryOne();
            // /  print_r($profilelist);die;

            $profileid = $profilelist['profileid'] ?? "";
            return $profileid;
        } catch (Exception $e) {
            echo "some error has occured";
        }
    }
    public function moduleaccess($uid, $profile, $tabs)
    {
        $connection = Yii::$app->db;
        $activeroleId = Yii::$app->session->get('active_profile_id');
        if (empty($activeroleId)) {
            $response = Yii::$app->response;
            $response->redirect([Yii::$app->getHomeUrl()]);
            Yii::$app->end();
        }
        try{
        //get profile from role2profile
        $getprofile = Yii::$app->db->createCommand("SELECT profileid FROM `role2profile` where roleid=:roleid")
                ->bindValue(':roleid', $activeroleId)
                ->queryOne();
        $profile = $getprofile['profileid'];
        
        } catch (Exception $e) {
            echo "some error has occured";
        }
        
        try {
            $hasadminpower = Yii::$app->db->createCommand("SELECT is_admin FROM `user` where  id = :uid")
                ->bindValue(':uid', $uid)
                ->queryOne();
            $isadmin = $hasadminpower['is_admin'];
            if ($isadmin == 1) {
                return array('opt' => $isadmin, 'name' => '');
            }

            $hasadminpower = Yii::$app->db->createCommand("SELECT count(*) as cnt FROM `profile2globalpermissions` where globalactionid in (1,2) and globalactionpermission=0 and profileid = :profileid")
                ->bindValue(':profileid', $profile)
                ->queryOne();
            // echo $hasadminpower['cnt'];die;
            //print_r($hasadminpower);die;
            if ($hasadminpower['cnt'] == 2) {
                $isadmin = 1;

                return array('opt' => $isadmin, 'name' => '');
            } else {
                // echo "SELECT name,operation,profile2standardpermissions.permissions,role2profile.profileid 
                //                 FROM profile2standardpermissions
                //                 JOIN role2profile ON role2profile.profileid = profile2standardpermissions.profileid
                //                 JOIN role ON role.roleid = role2profile.roleid
                //                 JOIN user2role ON user2role.roleid = role.roleid
                //                 JOIN tab ON tab.tabid = profile2standardpermissions.tabid
                //                 JOIN user  on user .id=user2role.userid  
                //                 WHERE presence=0 and id=$uid and profile2standardpermissions.permissions=0 
                //         and profile2standardpermissions.tabid =$tabs and 
                //         profile2standardpermissions.profileid=$profile";die;
                $query = " SELECT name,operation,profile2standardpermissions.permissions,role2profile.profileid 
                            FROM profile2standardpermissions
                            JOIN role2profile ON role2profile.profileid = profile2standardpermissions.profileid
                            JOIN role ON role.roleid = role2profile.roleid
                            JOIN user2role ON user2role.roleid = role.roleid
                            JOIN tab ON tab.tabid = profile2standardpermissions.tabid
                            JOIN user  on user .id=user2role.userid  
                            WHERE presence=0 and id=:uid and profile2standardpermissions.permissions=0 
                    and profile2standardpermissions.tabid =:tabs and 
                    profile2standardpermissions.profileid=:profile";

                $tabList = $connection->createCommand($query)
                    ->bindValue(':uid', $uid)
                    ->bindValue(':profile', $profile)
                    ->bindValue(':tabs', $tabs)->queryAll();

                //    print_r($tabList);die;
                $operation = "";
                foreach ($tabList as $tab) {
                    $operation .= $tab['operation'] . ",";
                    $name = $tab['name'];
                }
                if (empty($name))
                    $name = "";
                $operation = substr($operation, 0, -1);
                return array('opt' => $operation, 'name' => $name);
            }
        } catch (Exception $e) {
            echo "some error has occured";
        }
    }

    public function rolebasedrecord($uid, $profile)
    {
        try {
            $connection = Yii::$app->db;
            $hasadminpower = Yii::$app->db->createCommand("SELECT is_admin FROM `user` where  id = :uid")
                ->bindValue(':uid', $uid)
                ->queryOne();
            $isadmin = $hasadminpower['is_admin'];
            if ($isadmin == 1) {
                return array('userid' => $isadmin,'isadmin'=>$isadmin, 'roleid' => '');
            }

            $hasadminpower = Yii::$app->db->createCommand("SELECT count(*) as cnt FROM `profile2globalpermissions` where globalactionid in (1,2) and globalactionpermission=0 and profileid = :profileid")
                ->bindValue(':profileid', $profile)
                ->queryOne();
            // echo $hasadminpower['cnt'];die;
            //print_r($hasadminpower);die;
            if ($hasadminpower['cnt'] == 3) {
                $isadmin = 1;
                return array('userid' => $isadmin,'isadmin'=>$isadmin, 'roleid' => '');
            } else {
                // $role = Yii::$app->db->createCommand("select  user2role.roleid,depth,rolename
                // from  user2role
                // join user on user.id = user2role.userid
                // join role2profile on role2profile.roleid = user2role.roleid
                // join role on role.roleid = role2profile.roleid
                // where  id=:uid and profileid=:profile")
                //     ->bindValue(':uid', $uid)
                //     ->bindValue(':profile', $profile)
                //     ->queryAll();
                // if (empty($role))
                //     $role = [];
                // $roleid = $role[0]['roleid'] ?? "";
                // $rolename = $role[0]['rolename'] ?? "";
                // $depth_p = $role[0]['depth'] ?? "";
                // $role_dp = Yii::$app->db->createCommand("select  max(depth) as dp
                // from  role")
                //     ->queryAll();
                // if (empty($role_dp))
                //     $role_dp = [];
                // $depth = $role_dp[0]['dp'];
                // if ($depth != $depth_p) {
                //     $userroleid = Yii::$app->db->createCommand("select  id,user2role.roleid,role.parentrole
                // from  user2role 
                // join user on user.id = user2role.userid
                // join role2profile on role2profile.roleid = user2role.roleid
                // join role on role.roleid = role2profile.roleid
                // where  role.parentrole like '%$roleid%' and role.roleid NOT IN (:roleid)")
                //         ->bindValue(':roleid', $roleid)
                //         ->queryAll();
                //     $userids = "";
                //     foreach ($userroleid as $ruid) {
                //         $userids .= "'" . $ruid['id'] . "',";
                //     }
                //     $useridv = substr($userids, 0, -1);
                //     $uids = "'" . $uid . "'";
                //     if ($useridv != '')
                //         $userid = $useridv . "," . $uids;
                //     else
                //         $userid = $uids;
                // } else {
                //     $userid = $uid;
                // }
                // print_r(array('userid' => $userid, 'roleid' => $roleid, 'rolename' => $rolename));die;
                
                //new logic to show only to reporting officers
                  $role = Yii::$app->db->createCommand("select  is_admin,user2role.roleid,id,rolename
                from  user
                join user2role on user.id = user2role.userid
                join role2profile on role2profile.roleid = user2role.roleid
                join role on role.roleid = role2profile.roleid
                 where  FIND_IN_SET(:uid, REPLACE(reports_to, ' ', ''));")
                    ->bindValue(':uid', $uid)
                    ->queryAll();
                    
                //print_r($role);die;
                if (empty($role))
                $role = [];
                $roleid = $role[0]['roleid'] ?? "";
                $rolename = $role[0]['rolename'] ?? "";
                $id = $role[0]['id'] ?? "";
                $isadmin = $role[0]['is_admin'] ?? "";

                //added for showing records of all juniors to reporting head on 20 sept 2025
                $userid='';                
                $userid .= "'$uid'";                
                foreach($role as $roleuser)
                {
                    $uuid = $roleuser['id'] ?? "";
                    $userid .= ",'$uuid'";
                }
                //end added for showing records of all juniors to reporting head on 20 sept 2025

                 $role = Yii::$app->db->createCommand("select  is_admin,user2role.roleid,rolename
                from  user2role
                join user on user.id = user2role.userid
                join role2profile on role2profile.roleid = user2role.roleid
                join role on role.roleid = role2profile.roleid
                 where  id=:uid")
                    ->bindValue(':uid', $uid)
                    ->queryAll();
                 if (empty($role))
                $role = [];
                $roleid = $role[0]['roleid'] ?? "";
                $rolename = $role[0]['rolename'] ?? "";
                // print_r(array('userid' => $userid, 'roleid' => $roleid, 'rolename' => $rolename));die;


                return array('userid' => $userid,'isadmin'=>$isadmin, 'roleid' => $roleid, 'rolename' => $rolename);
            }
        } catch (Exception $e) {
            echo "some error has occured";
        }
    }

    //field access
    public function fieldacces($uid, $fieldid)
    {
        $activeroleId = Yii::$app->session->get('active_profile_id');

        //    
        // echo "SELECT profile2field.visible,profile2field.readonly from `profile2field` join profile on profile.profileid =profile2field.profileid join role2profile on role2profile.profileid = profile.profileid join role on role.roleid = role2profile.roleid join user2role on user2role.roleid = role.roleid join user on user .id=user2role.userid where id=1 and fieldid=$fieldid";die;
        try {
            $connection = Yii::$app->db;
            $profilelist = Yii::$app->db->createCommand("SELECT profile2field.visible,profile2field.readonly from `profile2field` join profile on profile.profileid =profile2field.profileid join role2profile on role2profile.profileid = profile.profileid join role on role.roleid = role2profile.roleid join user2role on user2role.roleid = role.roleid join user on user .id=user2role.userid where id=:uid and user2role.roleid=:roleid and fieldid=:fieldid")
                ->bindValue(':uid', $uid)
                ->bindValue(':roleid', $activeroleId)
                ->bindValue(':fieldid', $fieldid)
                ->queryOne();
            // print_r($profilelist);die;
            if (empty($profilelist))
                return "Invalid fieldid";
            else
                return $profilelist;
        } catch (Exception $e) {
            echo "some error has occured";
        }
    }
    public function modulepermission($profile, $tabs)
    {
        $activeroleId = Yii::$app->session->get('active_profile_id');
        try{
            //get profile from role2profile
            $getprofile = Yii::$app->db->createCommand("SELECT profileid FROM `role2profile` where roleid=:roleid")
                    ->bindValue(':roleid', $activeroleId)
                    ->queryOne();
            $profile = $getprofile['profileid'];
        
        } catch (Exception $e) {
            echo "some error has occured";
        }
        
        try {
            $hasadminpower = Yii::$app->db->createCommand("SELECT is_admin FROM `user` where  id = :uid")
                ->bindValue(':uid', Yii::$app->user->id)
                ->queryOne();
            $isadmin = $hasadminpower['is_admin'];
            if ($isadmin == 1)
                return array('permission' => 1, 'shareid' => '');

            $hasadminpower = Yii::$app->db->createCommand("SELECT count(*) as cnt FROM `profile2globalpermissions` where globalactionid in (1,2) and globalactionpermission=0 and profileid = :profileid")
                ->bindValue(':profileid', $profile)
                ->queryOne();
            // echo $profile.$hasadminpower['cnt'];die;
            //print_r($hasadminpower);die;
            if ($hasadminpower['cnt'] == 2) {
                $isadmin = 1;


                return array('permission' => 1, 'shareid' => '');
            } else {
                // echo "select org_share_action_mapping.share_action_id,permission 
                //     from org_share_action2tab 
                //     join org_share_action_mapping on org_share_action_mapping.share_action_id = org_share_action2tab.share_action_id 
                //     join tab on tab.tabid=org_share_action2tab.tabid 
                //     where presence=1 and org_share_action2tab.tabid=$tabs and permission=0
                //     order by tabsequence";die;
                // $tabList = Yii::$app->db->createCommand("select org_share_action_mapping.share_action_id,permission 
                // from org_share_action2tab 
                // join org_share_action_mapping on org_share_action_mapping.share_action_id = org_share_action2tab.share_action_id 
                // join tab on tab.tabid=org_share_action2tab.tabid 
                // where   org_share_action2tab.tabid=:tabs and permission=:perm
                // order by tabsequence")
                //     ->bindValue(':perm', 0)
                //     ->bindValue(':tabs', $tabs)
                //     ->queryAll();
                // $permission = $tabList[0]['permission'] ?? "";
                // $shareid = $tabList[0]['share_action_id'] ?? "";
                // return array('permission' => $permission, 'shareid' => $shareid);
                return array('permission' => 1, 'shareid' => '1');

            }
        } catch (Exception $e) {
            echo "some error has occured";
        }
    }
    public function hasadminpower($profileid)
    {
        //get profileid
        $profilerr = Yii::$app->db->createCommand("SELECT profileid FROM role2profile rp join user2role ur on rp.roleid = ur.roleid WHERE  ur.userid = :uid")
        ->bindValue(':uid', Yii::$app->user->id)
        ->queryOne();

        $profileid = $profilerr['profileid'];
        $hasadminpower = Yii::$app->db->createCommand("SELECT count(*) as cnt FROM `profile2globalpermissions` where globalactionid in (1,2) and globalactionpermission=0 and profileid = :profileid")
            ->bindValue(':profileid', $profileid)
            ->queryOne();
        // echo $hasadminpower['cnt'];die;
        //print_r($hasadminpower);die;
        if ($hasadminpower['cnt'] == 2) {
            $isadmin = 1;

        } else {
            $isadmin = 0;
            $hasadminpower = Yii::$app->db->createCommand("SELECT is_admin FROM `user` where  id = :uid")
                ->bindValue(':uid', Yii::$app->user->id)
                ->queryOne();
                if($hasadminpower['is_admin'])
            $isadmin = $hasadminpower['is_admin'];
        }
        return $isadmin;

    }

    public function getadminids($id,$profileid)
    {
        
            $uers = Yii::$app->db->createCommand("SELECT concat(first_name,' ',last_name) as userid, id FROM `user` where  id = 1 or is_admin = 1")
                // ->bindValue(':uid', Yii::$app->user->id)
                ->queryAll();
                
        return $uers;

    }


}


?>