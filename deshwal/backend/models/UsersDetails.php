<?php

namespace app\models;
use Yii;
use yii\db\Command;
class UsersDetails
{

    public function username($uitype)
    {
        // Get the database connection
        $connection = Yii::$app->db;

        // Create the command and execute the query
        $command = $connection->createCommand('select DISTINCT fieldlabel AS label from field where uitype =' . $uitype);  // Bind parameter in an array format

        // Fetch the result as a single row
        $userrolelistS = $command->queryOne();  // Use queryOne() to fetch a single row
        // print_r($userrolelistS);die;
        // Check if data is returned, then return the 'label'
        if ($userrolelistS) {
            $Columns = $userrolelistS['label'];
        } else {
            // Handle the case where no result is found
            $Columns = null;  // or return a default value
        }

        return $Columns;
    }




    public function users($fieldid, $uitype, $uid,$owner = '')
    {
        // Get profile of user (roleid)
        $profilerr = Yii::$app->db->createCommand("SELECT roleid FROM user2role ur WHERE ur.userid = :uid")
            ->bindValue(':uid', $uid)
            ->queryOne();

        $roleid = Yii::$app->session->get('active_profile_id') ?? '';

        // Get the column details using the helper function (username)
        $Columns = $this->username($uitype); // Assuming this method is defined elsewhere in the class
        $fieldlable = $Columns;
        $rolename = $roleid;

        // Default users access ID (as commented out logic was static - assignusersid = 1)
        $assignusersid = $uid;
        //check if admin then show all users
        $hasadminpower = Yii::$app->db->createCommand("SELECT is_admin FROM `user` where  id = :uid")
            ->bindValue(':uid', $assignusersid)
            ->queryOne();
        $isadmin = $hasadminpower['is_admin'];


        // Determine the user list based on different conditions
        if ($roleid == 'H1' || $roleid == 'H2' || $isadmin == 1) {
            // If role ID is 1, fetch all users
            $userList = Yii::$app->db->createCommand('select id,CONCAT(first_name, " ", if(last_name is null,"",last_name)) AS fullname from user where deleted = 0 order By fullname ASC')
                ->queryAll();
            // print_r($userList);die;
        } else {
            if ($assignusersid == 1) {
                // If assigned users access ID is 1, fetch all users
                $userList = Yii::$app->db->createCommand('select id,CONCAT(first_name, " ", last_name) AS fullname from user where deleted = 0 order By fullname ASC')
                    ->queryAll();
            } else {

                // echo 'select id, user2role.roleid, role.parentrole
                //     from user2role 
                //     join user on user.id = user2role.userid
                //     join role2profile on role2profile.roleid = user2role.roleid
                //     join role on role.roleid = role2profile.roleid
                //     where role.parentrole like "%'.$rolename.'::%" OR role.parentrole LIKE "%'.$rolename.'"
                //     and user.deleted= 0';die;
                // elseif ($assignusersid == 2 || $assignusersid == 3) {
                // If assigned users access ID is 2 or 3, fetch users based on role
                $userroleid = Yii::$app->db->createCommand('select id, user2role.roleid, role.parentrole
                    from user2role 
                    join user on user.id = user2role.userid
                    join role2profile on role2profile.roleid = user2role.roleid
                    join role on role.roleid = role2profile.roleid
                    where ( role.parentrole LIKE :roleid or user.id = :userid or user.id = :owner)
                    and user.deleted= 0')  // Ensure deleted users are not selected
                    ->bindValue(':roleid', "%{$rolename}::%")
                    ->bindValue(':userid', $uid)
                    ->bindValue(':owner', $owner)
                    ->queryAll();

                $userids = [];
                foreach ($userroleid as $ruid) {
                    $userids[] = $ruid['id'];
                }
                $userids = implode(",", $userids);

                // If no user IDs found, return an empty user list
                if (empty($userids)) {
                    $userList = [];
                } else {
                    $userList = Yii::$app->db->createCommand("select id,CONCAT(first_name, ' ', last_name) AS fullname from user where id in ($userids) and deleted = 0 order By fullname ASC")
                        ->queryAll();
                    // $userList = Yii::$app->db->createCommand()
                    //     ->select(['id', 'CONCAT(first_name, " ", last_name) AS fullname'])
                    //     ->from('user')
                    //     ->where(['id' => $userids, 'deleted' => 0])
                    //     ->orderBy('fullname ASC')
                    //     ->queryAll();
                }
            }
            //  else {
            //     // Default case: fetch a specific user based on UID
            //     $userList = Yii::$app->db->createCommand("select id,CONCAT(first_name, ' ', last_name) AS fullname from user where id = $uid and deleted = 0 order By fullname ASC")
            //     ->queryAll();
            //     // $userList = Yii::$app->db->createCommand()
            //     //     ->select(['id', 'CONCAT(first_name, " ", last_name) AS fullname'])
            //     //     ->from('user')
            //     //     ->where(['id' => $uid, 'deleted' => 0])  // Ensure deleted users are not selected
            //     //     ->orderBy('fullname ASC')
            //     //     ->queryAll();
            // }
        }
        // echo "<pre>";
        // print_r($userList);die;
        // Prepare the user detail array for dropdown selection
        // $userDetail = ['Select An Option'];
        $userDetail = [];
        foreach ($userList as $username) {
            $userDetail[$username['id']] = $username['fullname'];
        }
        //    print_r($userDetail);die;


        return $userDetail;
    }


    public function getuserlist($query = '')
    {
        $connection = Yii::$app->db;
    
        // Prepare condition for filtering by name if a query is passed
        $cond = '';
        if (!empty($query)) {
            $cond = " AND CONCAT(first_name, ' ', last_name) LIKE :query";
        }
    
        // Get the user ID
        $uid = Yii::$app->user->id;
    
        // Get the profile and role details of the user
        $profilerr = $connection->createCommand(
            "SELECT profileid, rp.roleid 
             FROM role2profile rp 
             JOIN user2role ur ON rp.roleid = ur.roleid 
             WHERE ur.userid = :uid"
        )
        ->bindValue(':uid', $uid)
        ->queryOne();
    
        $profileid = $profilerr['profileid'];
        $roleid = $profilerr['roleid'];
    
        // Check if the user has global admin power
        $hasadminpower = $connection->createCommand(
            "SELECT COUNT(*) AS cnt 
             FROM profile2globalpermissions 
             WHERE globalactionid IN (1, 2) 
             AND globalactionpermission = 0 
             AND profileid = :profileid"
        )
        ->bindValue(':profileid', $profileid)
        ->queryOne();
    
        // If the user has admin power, set `isadmin` to 1
        $isadmin = 0;
        $access = 0;
        if ($hasadminpower['cnt'] == 2) {
            $isadmin = 1;
            $access = 1;
        }
    
        // If user is admin, fetch all users
        if ($isadmin == 1) {
            $command = $connection->createCommand(
                "SELECT id, email, CONCAT(first_name, ' ', last_name) AS showfield 
                 FROM user 
                 WHERE deleted = 0 $cond 
                 ORDER BY showfield ASC"
            );
    
            // If there's a search query, bind the parameter for `LIKE`
            if (!empty($query)) {
                $command->bindValue(':query', $query . '%');
            }
        } else {
            // If not admin, fetch users based on the role and profile
            $role = $connection->createCommand(
                "SELECT user2role.roleid, depth, rolename
                 FROM user2role
                 JOIN user ON user.id = user2role.userid
                 JOIN role2profile ON role2profile.roleid = user2role.roleid
                 JOIN role ON role.roleid = role2profile.roleid
                 WHERE user.id = :uid AND profileid = :profileid"
            )
            ->bindValue(':uid', $uid)
            ->bindValue(':profileid', $profileid)
            ->queryOne();
    
            $roleid = $role['roleid'];
            $rolename = $role['rolename'];
    
            // Fetch users based on parentrole
            $userroleid = $connection->createCommand(
                "SELECT id, user2role.roleid, role.parentrole 
                 FROM user2role
                 JOIN user ON user.id = user2role.userid
                 JOIN role2profile ON role2profile.roleid = user2role.roleid
                 JOIN role ON role.roleid = role2profile.roleid
                 WHERE user.deleted = 0 AND role.parentrole LIKE :roleid"
            )
            ->bindValue(':roleid', "%{$roleid}%")
            ->queryAll();
    
            // Prepare the list of user IDs
            $userid = [];
            foreach ($userroleid as $ruid) {
                $userid[] = $ruid['id'];
            }
    
            // If we have user IDs, fetch the users
            if (!empty($userid)) {
                $command = $connection->createCommand(
                    "SELECT id, email, CONCAT(first_name, ' ', last_name) AS showfield 
                     FROM user 
                     WHERE deleted = 0 $cond 
                     AND user.id IN (" . implode(',', $userid) . ") 
                     ORDER BY showfield ASC"
                );
    
                // Bind the search query if provided
                if (!empty($query)) {
                    $command->bindValue(':query', $query . '%');
                }
            } else {
                // If no users found in the role, return an empty array
                return [];
            }
        }
    
        // Execute the command and fetch the result
        $Columns = $command->queryAll();
        return $Columns;
    }
    


    function getfolderlist()
    {
        $folderlist = Yii::$app->db->createCommand("SELECT folderid,foldername,af.path FROM attachmentsfolder af order by sequence")
            ->queryAll();
        return $folderlist;
    }



    function blockdropdownvalue()
    {

        $tabid = $_POST['tabid'];
        $criteria = new CDbCriteria();
        if ($tabid == '10') {
            $criteria->addInCondition('blockid', array('79', '21', '20', '19'));
            $criteria->addColumnCondition(array('tabid' => $tabid));
        } else {
            $criteria->addColumnCondition(array('tabid' => $tabid));
        }

        $query = "select blocklable,blockid from block where " . $criteria->condition;
        $command = Yii::$app->db->createCommand($query);
        $arr_picklist = $command->queryAll(true, $criteria->params);
        $option = "<option value='0'>Select An Option</option>";

        foreach ($arr_picklist as $state_data) {
            $option .= "<option value='" . $state_data['blockid'] . "'>" . $state_data['blocklable'] . "</option></br> ";
        }

        echo $option;

    }



}
