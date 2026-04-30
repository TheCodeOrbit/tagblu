<?php

namespace app\models;

use app\models\Exportrequest;
use Faker\Provider\ar_EG\Payment;
use yii\web\UploadedFile;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * EditModel class.
 * EditModel is the data structure for keeping
 * EditModel form data. It is used by the 'Module' action of 'Controller'.
 */
class EditModel extends \yii\db\ActiveRecord
{
    public $_members = [];
    private static $_tableName; // Static property to hold the dynamic table name
    public $tableName;
    public $fieldId;
    public $moduleName;
    public $recordId;
    public $actionid;
    public $Multiple_Records = [];
    function __construct($tablename, $fieldid = "", $moduleName, $actionid)
    {
        $this->fieldId = $fieldid;
        $this->moduleName = $moduleName;
        $this->actionid = $actionid; // Set the dynamic table name
        $this->setTableName(self::$_tableName);


        self::$_tableName = $tablename; // Set the dynamic table name

        $Columns = $this->getProperty($actionid);
        // print_r($Columns);//show dynamic columns
        // die;
        foreach ($Columns as $Column) {
            $this->_members[$Column["columnname"]] = null;
        }
        $this->_members[$fieldid] = null;
        $this->_members["tableName"] = null;
        $this->_members["moduleName"] = null;
        $this->_members["fieldId"] = null;
        $this->_members["mode"] = null;
        parent::__construct();
    }
    // Static tableName() method required by ActiveRecord
    public static function tableName()
    {
        return self::$_tableName; // Return the dynamic table name
    }
    public function gettabname()
    {
        return $this->moduleName;
    }
    public function setTableName($tablename)
    {
        $this->tableName = $tablename;
    }
    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        $fieldId = $this->fieldId;
        $validator = $this->getValidation();
        $rules = []; // Initialize the rules array

        foreach ($validator as $validatorName) {
            switch ($validatorName) {
                case 'length':
                    $rules[] = [$this->getValidationRule($validatorName), 'string', 'max' => 100];
                    break;

                case 'numerical':
                    $rules[] = [$this->getValidationRule($validatorName), 'integer'];
                    break;

                case 'unique':
                    $rules[] = [$this->getValidationRule($validatorName), 'unique', 'on' => 'insert'];
                    break;

                case 'match':
                    if (Yii::$app->controller->action->id == 'edit') {
                        $rules[] = [
                            $this->getValidationRule($validatorName),
                            'match',
                            'pattern' => '/.*/',
                        ];
                    } else {
                        $rules[] = [
                            $this->getValidationRule($validatorName),
                            'match',
                            'pattern' => '/(?=^.{12,}$)(?=.*[A-Z])(?=.+[!@#$\-*+?._=])(?=.*\d.*\d)/',
                            'message' => 'Password must contain at least one capital letter, one special character such as !@#$\-*+?._=, and at least two numerals. It should also be at least 8 characters long.',
                        ];
                    }
                    break;

                case 'filter':
                    $rules[] = [
                        $this->getValidationRule($validatorName),
                        'filter',
                        'filter' => [$this, 'custom_validation'],
                    ];
                    break;

                default:
                    // Handle other validator types, if necessary
                    break;
            }
        }

        // Add a safe rule for additional attributes
        $rules[] = [['mode', 'tableName', 'fieldId', $fieldId, 'Multiple_Records', 'recordId'], 'safe'];

        return $rules;
    }
    public function getValidation()
    {
        $table_name = $this->tableName();
        $connection = Yii::$app->db;
        $validator = Yii::$app->db->createCommand("select Distinct(validator_name)
                        from field
                        where tablename=:tablename")
            ->bindValue(':tablename', $table_name)
            ->queryAll();

        $final_validator = array();
        foreach ($validator as $validator_name) {
            if (strpos($validator_name['validator_name'], '~') != false) {
                $arr_vali = explode("~", $validator_name['validator_name']);
                foreach ($arr_vali as $vali)
                    $final_validator[] = $vali;
            } else
                $final_validator[] = $validator_name['validator_name'];
        }
        return array_unique($final_validator);
    }
    public function getValidationRule($validator)
    {
        $table_name = $this->tableName();
        $connection = Yii::$app->db;
        // echo "select columnname from field where tablename='$table_name' and (validator_name like '%$validator%')";die;
        $command = $connection->createCommand("select columnname from field where tablename='$table_name' and (validator_name like '%$validator%')");
        $arr_Columns = $command->queryAll();
        $Columns = "";
        foreach ($arr_Columns as $column)
            $Columns .= $column['columnname'] . ",";
        $Columns = substr($Columns, 0, -1);
        return $Columns;
    }
    public function custom_validation($value)
    {
        $value = preg_replace('/[<>\'\";]/', '', $value);
        //$value = htmlspecialchars($value);
        return $value;
    }

    public function getProperty($actionid)
    {
        $where = ' and 1=1 and ';
        if ($actionid == "create")
            $where .= "create_view=1";
        if ($actionid == "edit")
            $where .= "edit_view=1";
        if ($actionid == "detail")
            $where .= "detail_view=1";
        if ($actionid == "quickcreate")
            $where .= "quickcreate!=0";
        if ($actionid == "kanban")
            $where .= "kanbanview!=0";

        $table_name = $this->tableName();
        $Columns = Yii::$app->db
            ->createCommand(
                "SELECT field.columnname,field.fieldlabel FROM field  WHERE tablename=:tablename $where"
            )
            ->bindValue(":tablename", $table_name)
            ->queryAll();
        // (new \yii\db\Query())
        // ->select(['field.columnname', 'field.fieldlabel'])->from('field')->where('tablename = :tablename', [':tablename' => $table_name])->all();
        return $Columns;
    }
    public function attributeLabels()
    {
        $Columns = $this->getProperty();
        $arr_lable = [];
        //print_r($Columns);die;
        foreach ($Columns as $Column) {
            $arr_lable[$Column["columnname"]] = $Column["fieldlabel"];
        }
        /*echo "<br>Lable=";
        print_r($arr_lable);
        die;*/
        return $arr_lable;
    }
    public function getTabDetail($ModuleName)
    {
        $connection = Yii::$app->db;
        $arr_tab = Yii::$app->db
            ->createCommand("SELECT * FROM tab WHERE  name=:name")
            ->bindValue(":name", $ModuleName)
            ->queryOne();
        // $arr_tab = Yii::$app->db->createCommand()
        // ->select()
        // ->from('tab')
        // ->where('name =:name', array(':name' =>$ModuleName))
        // ->queryRow();
        return $arr_tab;
    }
    public function getUserBasedOnRole($role)
    {
        if (empty($role))
            return null;
        $user_data = Yii::$app->db->createCommand("SELECT u.id,u.first_name FROM `user` as u INNER JOIN user2role ur on u.id = ur.userid where ur.roleid = :role and u.deleted = 0")
            ->bindValue(":role", $role)
            ->queryOne();
        return $user_data && $user_data["id"] ? $user_data["id"] : null;
    }
    public function getActionList($ModuleName)
    {
        $ActionList = [];
        $actionName = $ModuleName;
        $arr_tab = $this->getTabDetail($ModuleName);
        $ActionList["ActionName"] = $actionName;
        $ActionList["ModuleName"] = $ModuleName;
        $ActionList["ModuleLabel"] = $arr_tab["tablabel"];
        return $ActionList;
    }
    // public function getFieldDetail($rolebasedrecord)
    function convertToUcfirstOrPascalCase($string)
    {
        // Check if the string contains underscores
        if (strpos($string, '_') !== false) {
            // Convert to PascalCase by splitting, capitalizing each part, and joining
            return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        } else {
            // Capitalize the first letter of the string
            return ucfirst($string);
        }
    }


    //rolebase will be implementd later
    public function getFieldDetail($rolebasedrecord)
    {
        $fieldid = $this->fieldId;
        $fieldids = $this->fieldId;
        $table_name = $this->tableName();
        $RecordId = (int) $this->_members[$fieldid];
        $roleid = $rolebasedrecord['roleid'];
        $isadmin = $rolebasedrecord['isadmin'];

        // $fieldid = $this->fieldId;
        // $fieldids = $this->fieldId;
        // // $table_name=$this->gettablename();
        // $table_name = self::$_tableName;
        $tab_name = $this->moduleName;
        $moduleName = $this->moduleName;
        $actionid = $this->actionid;

        $RecordId = (int) $this->_members[$fieldid];
        $userid = str_replace("'", '', $rolebasedrecord['userid']);
        // If $userid is a string, convert it to an array
        if (is_string($userid)) {
            $userid = explode(',', $userid);  // Convert string '8,12' to array ['8', '12']
        }
        // echo $isadmin.' '.count($userid)." ".$userid[0];die;
        //echo $roleid;die;
        if (!empty($RecordId)) {
            $Record = null;
            $view = "edit_view";
            //echo "<br>$RecordId<br>";
            $modelname = $this->convertToUcfirstOrPascalCase($table_name);

            $tbl = "app\models\\" . $modelname;
            $newmod = new $tbl();
            $uid = Yii::$app->user->id;
            //added DevIt clevel role id to allow view all records
            $role = \app\models\Role::find()
                        ->select([
                            "has_c_level" => new \yii\db\Expression(
                                "CASE WHEN rolename LIKE '%C Level%' THEN 'Yes' ELSE 'No' END"
                            )
                        ])
                        ->where(['roleid' => $roleid])
                        ->asArray()
                        ->one();

            if ($role && $role['has_c_level'] === 'Yes') {
                $alowview =  "Yes";
            } else {
                $alowview = "No";
            }
            //commented on 03 sept 2025 as it is not approved yet
            // if($actionid == "detail" && $alowview == "Yes")
            // {
            //    $isadmin = 1;
            // }
            //end devit c level
            if ($isadmin == 1 || (count($userid) == 1 && $userid[0] == 1))//if admin then show all records
                $Record = $newmod->find()->where([$fieldid => $RecordId])->one();
            else {
                $modulename = $this->moduleName;
                if ($modulename == "pickup") {
                    $past_assigned_records = null;
                    $uid = Yii::$app->user->id;
                    if ($isadmin != 1) {
                        $records_list = Yii::$app->db->createCommand("SELECT distinct module_reference_id FROM `owner_tracker` where module=:module and ownerid=:ownerid and deleted=:deleted")
                            ->bindValue(':module', $modulename)
                            ->bindValue(':ownerid', $uid)
                            ->bindValue(':deleted', 0)
                            ->queryAll();
                        if ($records_list && is_array($records_list) && count($records_list) > 0) {
                            $past_assigned_records = array_map(function ($item) {
                                return $item['module_reference_id'];
                            }, $records_list);
                        }
                    }
                    if (!empty($past_assigned_records) && is_array($past_assigned_records))
                        $past_assigned_records = implode(",", $past_assigned_records);

                    if (empty($past_assigned_records))
                        $past_assigned_records = 0;
                    if (!empty($past_assigned_records)) {
                        // $Query = "select $ColumnKey $join where $TableName.deleted=0 and ( $TableName.ownerid IN (" . $roleid['userid'] . ") ||  $TableName.creatorid IN (" . $roleid['userid'] . ") || $TableName.pickup_id IN(" . $past_assigned_records . ")) $cond $groupby order by $OrderBy $SortOrder";
                        // If $past_assigned_records is a string, convert it to an array
                        if (is_string($past_assigned_records)) {
                            $past_assigned_records = explode(',', $past_assigned_records);  // Convert string '8,12' to array ['8', '12']
                        }
                        // print_r($past_assigned_records);die;

                        // $Record = $newmod->find()->where([$fieldid => $RecordId])->andWhere([
                        //     'or',
                        //     ['in', 'creatorid', $userid],
                        //     ['in', 'ownerid', $userid],
                        //     ['in', $fieldid, $past_assigned_records]
                        // ])
                        // ->one();

                        // Start with the basic query
                        $query = $newmod->find()->where([$fieldid => $RecordId]);

                        // Check if actionid is "edit"
                        if ($actionid === 'edit') {
                            // Add only the ownerid condition
                            $query->andWhere(['in', 'ownerid', $userid]);
                        } else {
                            // Add all conditions if actionid is not "edit"
                            $query->andWhere([
                                'or',
                                ['in', 'creatorid', $userid],
                                ['in', 'ownerid', $userid],
                                ['in', 'modifiedby', $userid],
                                ['in', $fieldid, $past_assigned_records]
                            ]);
                        }
                        // Execute the query
                        $Record = $query->one();
                    } else {
                        // Start with the basic query
                        $query = $newmod->find()->where([$fieldid => $RecordId]);

                        // Check if actionid is "edit"
                        if ($actionid === 'edit') {
                            // Add only the ownerid condition
                            $query->andWhere(['in', 'ownerid', $userid]);
                        } else {
                            // Add all conditions if actionid is not "edit"
                            $query->andWhere([
                                'or',
                                ['in', 'creatorid', $userid],
                                ['in', 'ownerid', $userid],
                                ['in', 'modifiedby', $userid]
                            ]);
                        }
                        // Execute the query
                        $Record = $query->one();
                    }
                } else if ($modulename == "opportunities") {
                    // Start with the basic query
                    $query = $newmod->find()->where([$fieldid => $RecordId]);
                    if ($actionid === 'edit') {
                       
                         // Add all conditions when actionid is not "edit"
                        $query->andWhere([
                            'or',
                            ['in', 'ownerid', $userid],
                            // Add your complex condition using raw SQL
                            new \yii\db\Expression("(
                                FIND_IN_SET('1', opportunity.team_responsible) 
                                AND opportunity.opportunity_stage = '4' 
                                AND {$uid} = IFNULL(opportunity.sa_assigned, 0)
                            )"),
                            new \yii\db\Expression("(
                                FIND_IN_SET('1', opportunity.team_responsible) 
                                AND opportunity.opportunity_stage = '4' 
                                AND {$uid} = IFNULL(opportunity.sf_assigned, 0)
                            )"),
                            new \yii\db\Expression("(
                                FIND_IN_SET('2', opportunity.team_responsible) 
                                AND opportunity.opportunity_stage = '4' 
                                AND {$uid} = IFNULL(opportunity.procurement_team_member, 0)
                            )")
                        ]);
                    } else {
                        // Add all conditions when actionid is not "edit"
                        $query->andWhere([
                            'or',
                            ['in', 'creatorid', $userid],
                            ['in', 'ownerid', $userid],
                            ['in', 'modifiedby', $userid],
                            // Add your complex condition using raw SQL
                            new \yii\db\Expression("(
                                FIND_IN_SET('1', opportunity.team_responsible) 
                                AND opportunity.opportunity_stage = '4' 
                                AND {$uid} = IFNULL(opportunity.sa_assigned, 0)
                            )"),
                            new \yii\db\Expression("(
                                FIND_IN_SET('1', opportunity.team_responsible) 
                                AND opportunity.opportunity_stage = '4' 
                                AND {$uid} = IFNULL(opportunity.sf_assigned, 0)
                            )"),
                            new \yii\db\Expression("(
                                FIND_IN_SET('2', opportunity.team_responsible) 
                                AND opportunity.opportunity_stage = '4' 
                                AND {$uid} = IFNULL(opportunity.procurement_team_member, 0)
                            )")
                        ]);
                    }

                    // Execute the query
                    $Record = $query->one();

                } 
                else if ($modulename == "vendoraccount") {
                    // Start with the basic query
                    $query = $newmod->find()->where([$fieldid => $RecordId]);
                    if ($actionid === 'edit') {
                       
                         // Add all conditions when actionid is not "edit"
                        $query->andWhere([
                            'or',
                            ['in', 'ownerid', $userid]
                        ]);
                    } else {
                        //management section can also see 
                        $sql_v = "SELECT count(*) as cnt FROM `vendor_account_orgaisation_section` where vendoraccid=:RecordId and userid=:uid";
                        $records_list = Yii::$app->db->createCommand($sql_v)
                            ->bindValue(':RecordId', $RecordId)
                            ->bindValue(':uid', $uid)
                            ->queryOne();
                        if($records_list && $records_list['cnt'] > 0)
                        {
                            $org_ids = $records_list['cnt'];
                            if($org_ids == 0)
                            {
                                 $query->andWhere([
                                    'or',
                                    ['in', 'creatorid', $userid],
                                    ['in', 'ownerid', $userid],
                                    ['in', 'modifiedby', $userid],
                                    
                                ]);
                            }
                        }
                        else{
                             $query->andWhere([
                            'or',
                            ['in', 'creatorid', $userid],
                            ['in', 'ownerid', $userid],
                            ['in', 'modifiedby', $userid],
                            
                        ]);

                        }

                       
                    }

                    // Execute the query
                    $Record = $query->one();

                } 
                 else if ($modulename == "vendorlocations" || $modulename == "contacts" || $modulename == "contracts") {
                    // Start with the basic query
                    $query = $newmod->find()->where([$fieldid => $RecordId]);
                    if ($actionid === 'edit') {
                       
                         // Add all conditions when actionid is not "edit"
                        $query->andWhere([
                            'or',
                            ['in', 'ownerid', $userid]
                        ]);
                    } else {
                        //management section can also see 
                        if ($modulename == "vendorlocations" )
                        {
                        $sql_v = "SELECT count(*) as cnt FROM `vendor_account_orgaisation_section` where vendoraccid=(select vendor_account from vendor_locations where $fieldid= :RecordId) and userid=:uid";
                        $records_list = Yii::$app->db->createCommand($sql_v)
                            ->bindValue(':RecordId', $RecordId)
                            ->bindValue(':uid', $uid)
                            ->queryOne();
                        }
                        else if ($modulename == "contacts" )
                        {
                        $sql_v = "SELECT count(*) as cnt FROM `vendor_account_orgaisation_section` where vendoraccid=(select vendor_account_name from contacts where $fieldid= :RecordId) and userid=:uid";
                        $records_list = Yii::$app->db->createCommand($sql_v)
                            ->bindValue(':RecordId', $RecordId)
                            ->bindValue(':uid', $uid)
                            ->queryOne();
                        }
                        else if ($modulename == "contracts" )
                        {
                        $sql_v = "SELECT count(*) as cnt FROM `vendor_account_orgaisation_section` where vendoraccid=(select account_name from contracts where $fieldid= :RecordId) and userid=:uid";
                        $records_list = Yii::$app->db->createCommand($sql_v)
                            ->bindValue(':RecordId', $RecordId)
                            ->bindValue(':uid', $uid)
                            ->queryOne();
                        }
                        if($records_list && $records_list['cnt'] > 0)
                        {
                            $org_ids = $records_list['cnt'];
                            if($org_ids == 0)
                            {
                                 $query->andWhere([
                                    'or',
                                    ['in', 'creatorid', $userid],
                                    ['in', 'ownerid', $userid],
                                    ['in', 'modifiedby', $userid],
                                    
                                ]);
                            }
                        }
                        else{
                             $query->andWhere([
                            'or',
                            ['in', 'creatorid', $userid],
                            ['in', 'ownerid', $userid],
                            ['in', 'modifiedby', $userid],
                            
                        ]);

                        }

                       
                    }

                    // Execute the query
                    $Record = $query->one();

                } 
                else {
                   // Start with the basic query
                    $query = $newmod->find()->where([$fieldid => $RecordId]);

                    // Check if actionid is "edit"
                    if ($actionid === 'edit') {
                        // Add only the ownerid condition
                        $query->andWhere(['in', 'ownerid', $userid]);
                    } else {
                        // Add all conditions if actionid is not "edit"
                        $query->andWhere([
                                'or',
                                ['in', 'creatorid', $userid],
                                ['in', 'ownerid', $userid],
                                ['in', 'modifiedby', $userid],
                        ]);
                    }
                    // Execute the query
                    $Record = $query->one();
                }
            }

            // print_r($Record);die;
            if (empty($Record)) {
                throw new NotFoundHttpException('The requested record could not be found.');
            }
        } else {
            $view = "create_view";
        }
        $Tab = new Tab();
        // echo $table_name;die;
        // $Column=$Tab->with('Blocks')->find('name =:name and Blocks.edit_view =:edit_view and Blocks.presence =:presence',array(':name' =>$table_name,':edit_view' =>1,':presence' =>1));

        // $Column = Tab::find()
        //     ->joinWith("blocks") // Eager loading the related 'Blocks' model
        //     ->where([
        //         "tab.name" => $tab_name,
        //         "blocks.edit_view" => 1,
        //         "blocks.display_status" => 1,
        //     ])
        //     ->one();
        if (isset(Yii::$app->session['edititems'])):

            $Column = Tab::find()
                ->joinWith(['blocks.editfields']) // Eager loading the related models
                ->where([
                    'tab.name' => $tab_name,                   // Condition on Tab
                    //'blocks.edit_view' => 1,                  // Condition on Blocks
                    'blocks.display_status' => 1,             // Condition on Fields
                    //'blocks.blockid' => [145, 141],            // Condition on Fields
                ])
                ->orderBy(['blocks.sequence' => SORT_ASC]) // Add ORDER BY blocks.sequence ASC
                ->one();
            // echo "<pre>";
            // print_r($Column);die;


        else:

            if ($actionid == "edit") {
                $Column = Tab::find()
                    ->joinWith(['blocks.editfields']) // Eager loading the related models
                    ->where([
                        'tab.name' => $tab_name,                   // Condition on Tab
                        //'blocks.edit_view' => 1,                  // Condition on Blocks
                        'blocks.display_status' => 1,             // Condition on Fields
                    ])
                    ->orderBy(['blocks.sequence' => SORT_ASC]) // Add ORDER BY blocks.sequence ASC
                    ->one();
            } else if ($actionid == "create") {
                $Column = Tab::find()
                    ->joinWith(['blocks.createfields']) // Eager loading the related models
                    ->where([
                        'tab.name' => $tab_name,                   // Condition on Tab
                        'blocks.edit_view' => 1,                  // Condition on Blocks
                        'blocks.display_status' => 1,             // Condition on Fields
                    ])
                    ->orderBy([
                        'blocks.sequence' => SORT_ASC,            // First order by blocks.sequence
                        'field.sequence' => SORT_ASC,             // Then order by field.sequence
                    ])
                    ->one();
                // echo "deep";
                // print_r($Column);die;
            } else if ($actionid == "detail") {
                $Column = Tab::find()
                    ->joinWith(['blocks.detailfields']) // Eager loading the related models
                    ->where([
                        'tab.name' => $tab_name,                   // Condition on Tab
                        'blocks.edit_view' => 1,                  // Condition on Blocks
                        'blocks.display_status' => 1,             // Condition on Fields
                    ])
                    ->orderBy([
                        'blocks.sequence' => SORT_ASC,            // First order by blocks.sequence
                        'field.sequence' => SORT_ASC,             // Then order by field.sequence
                    ])
                    ->one();
            } else if ($actionid == "quickcreate") {
                $Column = Tab::find()
                    ->joinWith(['blocks.quickcreatefields']) // Eager loading the related models
                    ->where([
                        'tab.name' => $tab_name,                   // Condition on Tab
                        'blocks.edit_view' => 1,                  // Condition on Blocks
                        'blocks.display_status' => 1,             // Condition on Fields
                    ])
                    ->orderBy([
                        'blocks.sequence' => SORT_ASC,            // First order by blocks.sequence
                        'field.sequence' => SORT_ASC,             // Then order by field.sequence
                    ])
                    ->one();
            } else if ($actionid == "kanban") {
                $Column = Tab::find()
                    ->joinWith(['blocks.kanbanfields']) // Eager loading the related models
                    ->where([
                        'tab.name' => $tab_name,                   // Condition on Tab
                        'blocks.edit_view' => 1,                  // Condition on Blocks
                        'blocks.display_status' => 1,             // Condition on Fields
                    ])
                    ->orderBy([
                        'blocks.sequence' => SORT_ASC,            // First order by blocks.sequence
                        'field.sequence' => SORT_ASC,             // Then order by field.sequence
                    ])
                    ->one();
            } else if ($actionid == "massedit") {
                $Column = Tab::find()
                    ->joinWith(['blocks.masseditfields']) // Eager loading the related models
                    ->where([
                        'tab.name' => $tab_name,                   // Condition on Tab
                        'blocks.edit_view' => 1,                  // Condition on Blocks
                        'blocks.display_status' => 1,             // Condition on Fields
                    ])
                    ->orderBy([
                        'blocks.sequence' => SORT_ASC,            // First order by blocks.sequence
                        'field.sequence' => SORT_ASC,             // Then order by field.sequence
                    ])
                    ->one();
            }
        endif;
        $tab = $Column;
        // do manual sequencing
        if ($Column && $Column->blocks) {
            // Copy blocks to a regular array
            $blocks = $Column->blocks;

            // Sort blocks by sequence
            usort($blocks, function ($a, $b) {
                return $a->sequence <=> $b->sequence;
            });

            // Replace the blocks in the object
            $Column->populateRelation('blocks', $blocks);

            // Sort createfields for each block
            foreach ($blocks as $block) {
                if ($block->createfields) {
                    $fields = $block->createfields;

                    // Sort fields by sequence
                    usort($fields, function ($a, $b) {
                        return $a->sequence <=> $b->sequence;
                    });

                    // Replace createfields in the block
                    if ($actionid == "create")
                        $block->populateRelation($actionid . 'fields', $fields);
                    if ($actionid == "massedit")
                        $block->populateRelation($actionid . 'fields', $fields);
                }
            }
        }
        // foreach ($Column->blocks as $block) {
        //     echo "Block ID: {$block->blockid}, Sequence: {$block->sequence}<br>";
        //     foreach ($block->createfields as $field) {
        //         echo "  Field ID: {$field->fieldid}, Sequence: {$field->sequence}<br>";
        //     }
        // }die;

        //             $query = Tab::find()
        //             ->joinWith(['blocks.createfields']) // Eager load related models
        //             ->where([
        //                 'tab.name' => $tab_name,                   // Condition on Tab
        //                 'blocks.edit_view' => 1,                  // Condition on Blocks
        //                 'blocks.display_status' => 1,             // Condition on Fields
        //             ])
        //             ->orderBy([
        //                 'blocks.sequence' => SORT_ASC,            // First order by blocks.sequence
        //                 'field.sequence' => SORT_ASC,             // Then order by field.sequence
        //             ]);

        //         // Print the raw SQL
        //         echo $query->createCommand()->getRawSql(); // Outputs raw SQL

        // die;        

        // echo "col=<pre>";
        //         print_r($Column);die;
        $tabId = $Column->tabid;
        $model1 = new Reference($table_name, $fieldid);

        //start

        //end

        // echo "<pre>";print_r($Column);die;
        if (!empty($RecordId)) {
            return [$Column, $Record];
        } else {
            return $Column;
        }
    }
    public function saveModule($tabs)
    {
        $modlog = new ModtrackerBasic();
        $auditstatus = 0;
        $mode = $_POST["mode"];
        $module = $_POST["module"];
        $customtablename = $module . "cf";
        $CS = array();
        if (isset($_POST[$customtablename]))
            $CS = $_POST[$customtablename];
        else
            $CS = '';
        // print_r($CS);die;
        //$CS['leadid'] = "1";
        //check uitype for date
        $this->convertdate();


        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($module === "leads") {


                //now save submodules
                $modelleadetail = new Leadinformation();
                // added on 13 jan 2025
                //check if send for approval is checked then update lead status to approval pending
                if (isset($_POST["leadinformation"]['send_for_approval']) && $_POST["leadinformation"]['send_for_approval'] == 1) {
                    $_POST["leadinformation"]['leadstatus'] = '4';
                    //assign to reports to user
                    $reports = User::find()->select('reports_to')->where(['id' => Yii::$app->user->id])->one();
                    // print_r($reports);die;
                    $reportsto = $reports['reports_to'];
                    if (!empty($reportsto)) {
                        $_POST["leadinformation"]['ownerid'] = $reportsto;
                    }
                    // echo $_POST['ownerid'];die;
                }
                // print_r($_POST["leadinformation"]);die;
                $modelleadetail->attributes = $_POST["leadinformation"];
                //edn on 13 jan 2025

                //  echo "<pre>";
                //print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                $modelleadetail->leadname = $modelleadetail->firstname . " " . $modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->leadid, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->leadid);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["leadid" => $modelleadetail->leadid]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->leadid, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);


                        //save to child table
                        $LeadContactsDetail = new LeadContactsDetail();
                        $LeadContactsDetail->saveLeadContactsDetail($modelleadetail->leadid);

                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "call") {


                //now save submodules
                $modelleadetail = new CallInformation();
                $modelleadetail->attributes = $_POST["call_information"];
                // echo "<pre>";
                // print_r($modelleadetail->attributes);die;

                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->callinfo_id, $auditstatus, Yii::$app->user->id);
                        $modlog->auditlog('', '', $modelleadetail->related_to, $modelleadetail->related_to_id, 3, $modelleadetail->creatorid, $this->moduleName, $modelleadetail->callinfo_id);

                        $this->updateCRMSequence($module, $modelleadetail->callinfo_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["callinfo_id" => $modelleadetail->callinfo_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->callinfo_id, $auditstatus, Yii::$app->user->id);
                        }

                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "meeting") {

                //now save submodules
                $modelleadetail = new MeetingInformation();
                $data = $_POST["meeting_information"];
                if (!empty($data['internal_participants']) && is_array($data['internal_participants']))
                    $data['internal_participants'] = implode(', ', $data['internal_participants']);
                if (!empty($data['external_participants']) && is_array($data['external_participants']))
                    $data['external_participants'] = implode(', ', $data['external_participants']);
                // echo $data['notify_by'];die;
                $modelleadetail->attributes = $data;
                //echo "<pre>";
                //print_r($modelleadetail->attributes);die;

                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->meetinginfo_id, $auditstatus, Yii::$app->user->id);
                        $modlog->auditlog('', '', $modelleadetail->related_to, $modelleadetail->related_to_id, 3, $modelleadetail->creatorid, $this->moduleName, $modelleadetail->meetinginfo_id);

                        $this->updateCRMSequence($module, $modelleadetail->meetinginfo_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["meetinginfo_id" => $modelleadetail->meetinginfo_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->meetinginfo_id, $auditstatus, Yii::$app->user->id);
                        }

                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "task") {


                //now save submodules
                $modelleadetail = new TaskInformation();
                $data = $_POST["task_information"];
                if (!empty($data['notify_by']))
                    $data['notify_by'] = implode(', ', $data['notify_by']);
                // echo $data['notify_by'];die;
                $modelleadetail->attributes = $data;
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;


                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->taskinfo_id, $auditstatus, Yii::$app->user->id);
                        $modlog->auditlog('', '', $modelleadetail->related_to, $modelleadetail->related_to_id, 3, $modelleadetail->creatorid, $this->moduleName, $modelleadetail->taskinfo_id);

                        $this->updateCRMSequence($module, $modelleadetail->taskinfo_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["taskinfo_id" => $modelleadetail->taskinfo_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->taskinfo_id, $auditstatus, Yii::$app->user->id);
                        }

                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "documents") {


                //now save submodules
                $modelleadetail = new Documents();
                $modelleadetail->attributes = $_POST["documents"];
                // echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;

                //first save doc
                $file = UploadedFile::getInstanceByName('documents[filename]'); // Optional file upload
                // echo $file;
                // print_r($_FILES);die;
                $documents = Yii::$app->request->post('documents'); // Text content

                if (!$file || empty($documents)) {
                    echo 'You must provide file and fill required fields';
                    die;

                    return ['success' => false, 'message' => 'You must provide file and fill required fields'];
                }

                $fileUrl = null;



                // Handle file upload if a file is provided
                if ($file) {
                    // Security: Validate file extension and MIME type
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'zip'];
                    $allowedMimeTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/pdf',
                        'application/vnd.ms-excel',
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                    // print_r($file);
                    // echo $file->type;die;
                    if (!in_array($file->extension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
                        echo 'Invalid file type.';
                        die;
                        return ['success' => false, 'message' => 'Invalid file type.'];
                    }
                    // Maximum file size allowed (5GB)
                    $maxFileSize = 5 * 1024 * 1024 * 1024; // 5GB in bytes
                    // Check if file exceeds maximum allowed size (5GB)
                    if ($file->size > $maxFileSize) {
                        return ['success' => false, 'message' => 'File size exceeds the maximum allowed size of 5GB.'];
                    }

                    // Determine the directory structure based on year, month, and week
                    $year = date('Y');
                    $month = date('m');
                    $week = date('W'); // Week of the year

                    //get folder name from id
                    $command = Yii::$app->db->createCommand("select * from `attachmentsfolder`  where folderid=:folderid")->bindValue(':folderid', $modelleadetail->folderid);
                    $folder = $command->queryOne();
                    //print_r($folder);die;
                    $foldername = $folder['path'];

                    // Define the upload base path
                    $baseUploadPath = Yii::getAlias('@webroot');
                    $targetPath = $baseUploadPath . "/" . $foldername . "/$year/$month/week_$week/";


                    // Create directories if they do not exist
                    if (!is_dir($targetPath)) {
                        if (!mkdir($targetPath, 0755, true)) {
                            return ['success' => false, 'message' => 'Failed to create upload directories.'];
                        }
                    }

                    // Generate a secure unique file name
                    $fileName = uniqid() . '.' . $file->extension;
                    $filePath = $targetPath . $fileName;
                    $filesavepath = $foldername . "/$year/$month/week_$week/" . $fileName;


                    //save to attachments
                    $modelatach = new Attachments();
                    $modelatach->name = $file->name;
                    $modelatach->type = $file->type;
                    $modelatach->path = $filesavepath;
                    $modelatach->storedname = $fileName;
                    if ($modelatach->validate()) {
                        if ($modelatach->save()) {
                            $modelleadetail->filename = $modelatach->attachmentsid;
                        }
                    }


                    // Save the file
                    if ($file->saveAs($filePath)) {
                        $fileUrl = Yii::getAlias('@web') . "/" . $foldername . "/$year/$month/week_$week/" . $fileName;
                    } else {
                        $message = 'Failed to save the file.';
                        die;
                    }
                }


                if ($modelleadetail->validate()) {

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->docid, $auditstatus, Yii::$app->user->id);
                        $modlog->auditlog('', '', $modelleadetail->related_to, $modelleadetail->related_to_id, 3, $modelleadetail->creatorid, $this->moduleName, $modelleadetail->docid);
                        $this->updateCRMSequence($module, $modelleadetail->docid);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["docid" => $modelleadetail->docid]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->docid, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "opportunities") {

                $data = $_POST["opportunity"];
                if (!empty($data['team_responsible']))
                    $data['team_responsible'] = implode(',', $data['team_responsible']);
                if (isset($data['team_responsible'])) {
                    if (!is_string($data['team_responsible'])) {
                        $data['team_responsible'] = (string) $data['team_responsible'];
                    }
                }

                if (isset($data['submit_for_screening']) && $data['submit_for_screening'] == 1) {
                    $data['opportunity_stage'] = 2; //screening

                    //assign to solution team
                    // $reports = "SELECT id 
                    //                 FROM user 
                    //                 JOIN user2role ON user2role.userid = user.id 
                    //                 WHERE user.deleted = 0 
                    //                 AND status = 10 
                    //                 AND user2role.roleid = 'H67' 
                    //                 ORDER BY RAND() 
                    //                 LIMIT 1;
                    //                 ";
                      //assign to screening team added on 4 sept 2025 as per CR Points
                     $reports = "SELECT id 
                                    FROM user 
                                    JOIN user2role ON user2role.userid = user.id 
                                    WHERE user.deleted = 0 
                                    AND status = 10 
                                    AND user2role.roleid = 'H99' 
                                    ORDER BY RAND() 
                                    LIMIT 1;
                                    ";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }

                }
                $modelleadetail = new Opportunity();
                $modelleadetail->attributes = $data;
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->opportunity_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->opportunity_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["opportunity_id" => $modelleadetail->opportunity_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->opportunity_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);

                        //save to child table
                        $child = new OpportunityProductDetail();
                        $child->saveOpportunityProductDetail($modelleadetail->opportunity_id);

                        //save to child table
                        $child = new OpportunityShipDetail();
                        $child->saveOpportunityShipDetail($modelleadetail->opportunity_id);

                        if (isset($data['submit_for_screening']) && $data['submit_for_screening'] == 1) {
                            $message = "Opportunity No " . $modelleadetail->{$autoField} . " is submitted for screening. Please check";
                            $this->sendnotification($ownerid, $message, $this->moduleName, $modelleadetail->opportunity_id);
                        }


                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "sourcingdeal") {

                $data = $_POST["sourcingdeal"];
                // check f stage = won then auto save closure month ad date
                if (isset($data['stage']) && $data['stage'] == 14) //won stage
                {
                    $data['closing_date'] = date("Y-m-d");
                    $data['closure_month'] = date("m");
                    $data['closure_week'] = date("W", strtotime($data['closing_date']));
                }
                //end stage
                if (isset($data['submit_for_pricing']) && $data['submit_for_pricing'] == 1) {
                    $data['stage'] = 6; //pricing pending

                    //assign to pricing team
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H20' limit 1";
                    $reports = "-- First, get the next higher user ID after the last modifier
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H20'
                                        AND u.id > (
                                            SELECT whodid
                                            FROM modtracker_basic
                                            WHERE module = '" . ucfirst($this->moduleName) . "' AND status = 2
                                            ORDER BY changedon DESC
                                            LIMIT 1
                                        )
                                        ORDER BY u.id ASC
                                        LIMIT 1
                                    )
                                    UNION ALL
                                    -- If none, wrap around to the lowest ID (still excluding the last modifier)
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H20'
                                        AND u.id != (
                                            SELECT whodid
                                            FROM modtracker_basic
                                            WHERE module = '" . ucfirst($this->moduleName) . "' AND status = 2
                                            ORDER BY changedon DESC
                                            LIMIT 1
                                        )
                                        ORDER BY u.id ASC
                                        LIMIT 1
                                    )
                                    LIMIT 1";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                        //also assign product and service to pricing team
                        $reports = "Update servicedetail set ownerid = :ownerid where related_to = 51 and related_to_id = :recordid ";
                        $rest = Yii::$app->db->createCommand($reports)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":recordid", $RecordId)
                            ->execute();

                        $reports = "Update product_costing set ownerid = :ownerid where related_to = 51 and related_to_id = :recordid ";
                        $rest = Yii::$app->db->createCommand($reports)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":recordid", $RecordId)
                            ->execute();
                    }
                }
                if (isset($data['special_pricing']) && $data['special_pricing'] == 1) {
                    $data['stage'] = 8; //price pending

                    //assign to c level special pricing
                    //$reports = "select id from user join user2role on user2role.userid = user.id  join role2profile on user2role.roleid = role2profile.roleid where user.deleted = 0 and status = 10 and role2profile.profileid='25' limit 1";
                    //added by deepika on 19 june
                    $reports = "-- First, get the next higher user ID after the last modifier
                        (
                            SELECT u.id
                            FROM user u
                            JOIN user2role ur ON ur.userid = u.id
                            JOIN role2profile rp ON ur.roleid = rp.roleid
                            WHERE u.deleted = 0
                            AND u.status = 10
                            AND rp.profileid = '25'
                            AND u.id > (
                                SELECT whodid
                                FROM modtracker_basic
                                WHERE module = '" . ucfirst($this->moduleName) . "' 
                                AND status = 2
                                ORDER BY changedon DESC
                                LIMIT 1
                            )
                            ORDER BY u.id ASC
                            LIMIT 1
                        )
                        UNION ALL
                        -- If none, wrap around to the lowest ID (excluding the last modifier)
                        (
                            SELECT u.id
                            FROM user u
                            JOIN user2role ur ON ur.userid = u.id
                            JOIN role2profile rp ON ur.roleid = rp.roleid
                            WHERE u.deleted = 0
                            AND u.status = 10
                            AND rp.profileid = '25'
                            AND u.id != (
                                SELECT whodid
                                FROM modtracker_basic
                                WHERE module = '" . ucfirst($this->moduleName) . "' 
                                AND status = 2
                                ORDER BY changedon DESC
                                LIMIT 1
                            )
                            ORDER BY u.id ASC
                            LIMIT 1
                        )
                        LIMIT 1;
                        ";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                        //also assign product and service to pricing team
                        $reports = "Update servicedetail set ownerid = :ownerid where related_to = 51 and related_to_id = :recordid ";
                        $rest = Yii::$app->db->createCommand($reports)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":recordid", $RecordId)
                            ->execute();

                        $reports = "Update product_costing set ownerid = :ownerid where related_to = 51 and related_to_id = :recordid ";
                        $rest = Yii::$app->db->createCommand($reports)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":recordid", $RecordId)
                            ->execute();
                    }
                }
                if (isset($data['submit_for_logistics']) && $data['submit_for_logistics'] == 1) {
                    // $data['stage'] = 6; //logistics pending

                    //assign to pricing team
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H52' limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H52'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H52'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H52'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }
                }
                if (isset($data['costing_done']) && $data['costing_done'] == 1) {
                    $data['stage'] = 10; //pricing done
                    $data['ownerid'] = $data['creatorid'];//assign back to creator


                }
                ///////ceo approval////////////
                if (isset($data['ceo_approval']) && $data['ceo_approval'] == 1) {
                    $data['stage'] = 29; //CEO Price Approval Pending

                    //assign to ceo
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H62' limit 1";
                    //added by deepika on 19 june
                    $reports = "--- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H62'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H62'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H62'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }
                }


                $modelleadetail = new Sourcingdeal();
                $modelleadetail->attributes = $data;
                //set probability based on stage
                $modelleadetail->probability = (string) $modelleadetail->getprobability($data['stage']);
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $this->SaveSourcingdealTotal($modelleadetail->sourcingdeal_id);
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->sourcingdeal_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->sourcingdeal_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["sourcingdeal_id" => $modelleadetail->sourcingdeal_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->sourcingdeal_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "products") {


                //now save submodules
                $modelleadetail = new Products();
                $modelleadetail->attributes = $_POST["products"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->products_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->products_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["products_id" => $modelleadetail->products_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->products_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "productdit") {


                //now save submodules
                $modelleadetail = new ProductDit();
                $modelleadetail->attributes = $_POST["product_dit"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->productdit_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->productdit_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["productdit_id" => $modelleadetail->productdit_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->productdit_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "servicemaster") {


                //now save submodules
                $modelleadetail = new Servicemaster();
                $modelleadetail->attributes = $_POST["servicemaster"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->servicemaster_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->servicemaster_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["servicemaster_id" => $modelleadetail->servicemaster_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->servicemaster_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "vendoraccount") {
                // Now save vendor account details
                $modelvendoraccount = new VendorAccount();
                $data = $_POST["vendor_account"]; // Array of key-value pairs to update
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                // added on 17 jan 2025
                //check if kyc_completed is checked then update acc_status to kyc_completed
                // echo $_POST["vendor_account"]['kyc_completed'];die;
                if (isset($_POST["vendor_account"]['kyc_completed']) && $_POST["vendor_account"]['kyc_completed'] == 1) {
                    $data['acc_status'] = '6';
                    //assign to finance manager
                    $sql = "select id from user 
                join user2role on user2role.userid = user.id
                where user2role.roleid='H19' and deleted =0 and status=10 limit 1";
                    $userresult = Yii::$app->db->createCommand($sql)
                        ->queryOne();
                    if ($userresult) {
                        $data['ownerid'] = $userresult['id'];
                        $data['kyc_completed_by'] = Yii::$app->user->id;
                    }
                    // echo $data['ownerid'];die;
                }
                //check if submitted_for_kyc is checked then update acc_status to submitted_for_kyc
                if (isset($_POST["vendor_account"]['submitted_for_kyc']) && $_POST["vendor_account"]['submitted_for_kyc'] == 1) {
                    $data['acc_status'] = '5';
                    //assign to compliance manager
                    $sql = "select id from user 
                join user2role on user2role.userid = user.id
                where user2role.roleid='H17' and deleted =0 and status=10 limit 1";
                    $userresult = Yii::$app->db->createCommand($sql)
                        ->queryOne();
                    if ($userresult) {
                        $data['ownerid'] = $userresult['id'];
                        $data['kyc_submitted_by'] = (string) Yii::$app->user->id;
                    }
                }
                //check if credit_stage is No Credit or Approved then update acc_status to Active
                if (isset($_POST["vendor_account"]['credit_stage']) && ($_POST["vendor_account"]['credit_stage'] == 1 || $_POST["vendor_account"]['credit_stage'] == 2)) {
                    $data['acc_status'] = '2';
                    // echo "<br>";

                    //get kyc submitted by assign to submitted by user                   
                    $data['ownerid'] = $data['kyc_submitted_by'];
                    // echo "<br>";
                    // $data['finance_detail_submitted_date']=date("Y-m-d");


                    // $data['finance_detail_submitted_by'] = Yii::$app->user->id;

                }
                //check if credit_stage is Hold then update acc_status to Hold
                if (isset($_POST["vendor_account"]['credit_stage']) && ($_POST["vendor_account"]['credit_stage'] == 3)) {
                    $data['acc_status'] = '3';
                    // echo "<br>";
                    //get kyc submitted by assign to submitted by user
                    // $data['ownerid'] = $data['kyc_submitted_by'];
                    // echo "<br>";


                }
                //check if recheck_kyc is checked then update acc_status to kyc recheck and reset submitted_for_kyc
                if (isset($_POST["vendor_account"]['recheck_kyc']) && $_POST["vendor_account"]['recheck_kyc'] == 1) {
                    $data['acc_status'] = '7';
                    $data['submitted_for_kyc'] = '0';
                    //get kyc submitted by assign to submitted by user                   
                    $data['ownerid'] = $data['kyc_submitted_by'];
                }
                //check if finance detail completed is checkd then save finnace detail submited date and finance detail submitted by 
                if (isset($_POST["vendor_account"]['finance_detail_completed']) && $_POST["vendor_account"]['finance_detail_completed'] == 1) {
                    $data['finance_detail_submitted_date'] = date("Y-m-d");

                    $data['finance_detail_submitted_by'] = Yii::$app->user->id;
                }
                // set account code
                $code = '';
                if (in_array(2, $_POST['vendor_account']['account_category']))
                    $code .= "C";
                if (in_array(1, $_POST['vendor_account']['account_category']))
                    $code .= "V";
                if (in_array(3, $_POST['vendor_account']['account_category']))
                    $code .= "P";
                $data['cust_code'] = $this->getaccountcode($code);
                //echo $data['cust_code'];die;

                ///////save attachments////////////////
                $fileInstance = 'vendor_account';
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = (string) $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }
                //end set account code
                //code added by ptpatel to resolve ownerid issue 
                if ($data['finance_detail_submitted_by'] != '') {
                    $data['finance_detail_submitted_by'] = (string) $data['finance_detail_submitted_by'];
                }
                if ($data['ownerid'] == '') {
                    $data['ownerid'] = Yii::$app->user->id;
                }
                //end code added by ptpatel 
                $modelvendoraccount->attributes = $data;
                // Debug: Check the data to ensure 'acc_owner' is not missing
                //  print_r($_POST["vendor_account"]);
                //die();
                // Ensure vendor_no is handled correctly
                if ($autoField = $this->checkAutoNo()) {
                    $modelvendoraccount->{$autoField} = $this->getAutoNo($tabs);
                }

                // Validate the model
                if ($modelvendoraccount->validate()) {
                    // If valid, save the vendor account details
                    if ($modelvendoraccount->save()) {

                        //save to child table
                        $child = new VendorAccountOrgaisationSection();
                        $child->saveVendorAccountOrgaisationSection($modelvendoraccount->vendoraccid);

                        //save to child table
                        $child = new VendorAccountOemManagerDetail();
                        $child->saveVendorAccountOemManagerDetail($modelvendoraccount->vendoraccid);

                        // Audit log for vendor account
                        $modlog->auditlog($modelvendoraccount->oldAttributes, $modelvendoraccount->attributes, $this->moduleName, $modelvendoraccount->vendoraccid, $auditstatus, Yii::$app->user->id);

                        // You can add additional functionality like updating CRM sequence or other custom logic if necessary
                        $this->updateCRMSequence($module, $modelvendoraccount->vendoraccid);

                        // If custom fields are provided, save them
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["vendoraccid" => $modelvendoraccount->vendoraccid]);
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            // Audit log for custom fields
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelvendoraccount->vendoraccid, $auditstatus, Yii::$app->user->id);
                        }

                        // Handle auto field reset if necessary
                        if ($autoField = $this->checkAutoNo()) {
                            $this->setAutoNo($tabs);
                        }

                        // Commit the transaction
                        $transaction->commit();

                        return true; // Indicate success
                    } else {
                        // If save fails, print errors and log the failure
                        print_r($modelvendoraccount->getErrors());
                        Yii::error("Failed to save vendor account: " . json_encode($modelvendoraccount->getErrors()));
                        return false; // Indicate failure
                    }
                } else {
                    // If validation fails, print errors and log the failure
                    print_r($modelvendoraccount->getErrors());
                    Yii::error("Validation errors for vendor account: " . json_encode($modelvendoraccount->getErrors()));
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "productdetail") {


                //now save submodules
                $modelleadetail = new ProductCosting();
                $modelleadetail->attributes = $_POST["product_costing"];

                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId; //echo $fieldId;die;

                    if ($modelleadetail->save()) {
                        if ($_POST['product_costing']['related_to'] == 51 && !empty($_POST['product_costing']['related_to_id'])) { //update soucing deal stage
                            $dtsrc = array();
                            $dtsrc['stage'] = 5;
                            $output = Sourcingdeal::updateAll($dtsrc, 'sourcingdeal_id = :sourcingdeal_id', [':sourcingdeal_id' => $_POST['product_costing']['related_to_id']]);
                            $modelsrc = new Sourcingdeal();

                            $modelsrc->oldAttributes = Yii::$app->db->createCommand("select * from `sourcingdeal` where sourcingdeal_id=:sourcingdeal_id")
                                ->bindValue(":sourcingdeal_id", $_POST['product_costing']['related_to_id'])
                                ->queryOne();

                            $modlog->auditlog($modelsrc->oldAttributes, $dtsrc, 'sourcingdeal', $_POST['product_costing']['related_to_id'], 2, Yii::$app->user->id);
                        }
                        //save to child table
                        $child = new ProductCostingDetail();
                        $child->saveProductCostingDetail($modelleadetail->$fieldId);

                        //save total sourcing deal
                        if ($_POST['product_costing']['related_to'] == 51) {
                            $this->SaveSourcingdealTotal($_POST['product_costing']['related_to_id']);
                        }

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "notes") {


                //now save submodules
                $modelleadetail = new Modnotes();
                $modelleadetail->attributes = $_POST["modnotes"];
                // echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;

                //first save doc
                $file = UploadedFile::getInstanceByName('modnotes[filename]'); // Optional file upload
                // echo $file;
                // print_r($_FILES);die;
                $notes = Yii::$app->request->post('modnotes'); // Text content

                if (!$file || empty($notes)) {
                    return ['success' => false, 'message' => 'You must provide file and fill required fields'];
                }

                $fileUrl = null;



                // Handle file upload if a file is provided
                if ($file) {
                    // Security: Validate file extension and MIME type
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx'];
                    $allowedMimeTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/pdf',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                    if (!in_array($file->extension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
                        return ['success' => false, 'message' => 'Invalid file type.'];
                    }

                    // Determine the directory structure based on year, month, and week
                    $year = date('Y');
                    $month = date('m');
                    $week = date('W'); // Week of the year


                    // Define the upload base path
                    $baseUploadPath = Yii::getAlias('@webroot/uploads');
                    $targetPath = $baseUploadPath . "/$year/$month/week_$week/";


                    // Create directories if they do not exist
                    if (!is_dir($targetPath)) {
                        if (!mkdir($targetPath, 0755, true)) {
                            return ['success' => false, 'message' => 'Failed to create upload directories.'];
                        }
                    }

                    // Generate a secure unique file name
                    $fileName = uniqid() . '.' . $file->extension;
                    $filePath = $targetPath . $fileName;
                    $filesavepath = "uploads/$year/$month/week_$week/" . $fileName;


                    //save to attachments
                    $modelatach = new Attachments();
                    $modelatach->name = $file->name;
                    $modelatach->type = $file->type;
                    $modelatach->path = $filesavepath;
                    $modelatach->storedname = $fileName;
                    if ($modelatach->validate()) {
                        if ($modelatach->save()) {
                            $modelleadetail->filename = $modelatach->attachmentsid;
                        }
                    }


                    // Save the file
                    if ($file->saveAs($filePath)) {
                        $fileUrl = Yii::getAlias('@web') . "/uploads/$year/$month/week_$week/" . $fileName;
                    } else {
                        $message = 'Failed to save the file.';
                        die;
                    }
                }


                if ($modelleadetail->validate()) {

                    if ($modelleadetail->save()) {
                        // print_r($modelleadetail);
                        // die;
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->modnotesid, $auditstatus, Yii::$app->user->id);
                        $modlog->auditlog('', '', $modelleadetail->related_to, $modelleadetail->related_to_id, 3, $modelleadetail->creatorid, $this->moduleName, $modelleadetail->modnotesid);
                        $this->updateCRMSequence($module, $modelleadetail->modnotesid);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["modnotesid" => $modelleadetail->modnotesid]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->modnotesid, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "grn") {
                //now save submodules
                $modelleadetail = new Grn();
                $data = $_POST["grn"];
                $fileInstance = 'grn'; // Assuming 'grn' is the key in $_FILES
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                //get lot number also
                $modelleadetail->lot_number = $modelleadetail->getLotNo();
                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId; //echo $fieldId;die;

                    if ($modelleadetail->save()) {
                        //save to child table
                        $grn_shipped = new GrnShippedDetails();
                        $grn_shipped->saveGrnShippedDetails($modelleadetail->$fieldId);

                        $grn_documents = new GrnDocumentDetails();
                        $grn_documents->saveGrnDocumentsDetails($modelleadetail->$fieldId);

                        $grn_assets = new GrnAssetDetail();
                        $grn_assets->saveGrnAssets($modelleadetail->$fieldId);

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);


                        //save to vp reports for grn
                        $modelleadetail->save_vp_grn($modelleadetail->$fieldId);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "purchaseorder") {
                //now save submodules
                $modelleadetail = new PurchaseOrder();
                $data = $_POST["purchase_order"];
                // echo "<pre>";print_r($data);die;
                $submit_approval = $data["submit_approval"] ?? null;
                if (isset($data['type']) && is_array($data['type'])) {
                    $data['type'] = implode(",", $data['type']);
                }

                // echo $submit_approval;
                // print_r($data);
                // exit;

                //custom validation
                $modelleadetail->validateDataIntegrityForPoApproval($data['stage']);
                $fileInstance = 'purchase_order';
                if (isset($_FILES[$fileInstance])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $userresult = '';
                if ($submit_approval == 1) {
                    //assign to finance executive
                    /*$sql = "select id from user 
                    join user2role on user2role.userid = user.id
                    where user2role.roleid='H63' and deleted =0 and status=10 limit 1";
                    */
                    // changed logic on 12 july 2025, assign to finance manager 'H19'
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H19'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H19'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H19'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $userresult = Yii::$app->db->createCommand($reports)
                        ->queryOne();
                    if ($userresult) {
                        $data['ownerid'] = $userresult['id'];
                        $data['stage'] = '2';


                    }
                }
                $modelleadetail->attributes = $data;
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId; //echo $fieldId;die;



                    if ($modelleadetail->save()) {

                        if ($submit_approval == 1 && $userresult) {


                            $purchaseinfo = PurchaseOrder::find()->select('purchase_order_no')->where(['purchase_order_id' => $modelleadetail->purchase_order_id])->one();
                            $notification = new Notifications();
                            $notification->userid = $data['ownerid'];
                            $notification->message = "Purchase Order " . $purchaseinfo['purchase_order_no'] . "  has been submitted for approval.Please check";
                            $notification->read_status = 0; // Unread notification
                            $notification->display_status = 0;
                            $notification->source_link = Yii::$app->request->baseUrl . "/" . $this->moduleName . "/detail?Record=" . $modelleadetail->purchase_order_id;
                            ;
                            $notification->createdtime = date('Y-m-d H:i:s');
                            $notification->modifiedtime = date('Y-m-d H:i:s');
                            if (!$notification->save()) {
                                echo 'save failed';
                                exit;
                            }
                        }

                        //save to child table
                        $child = new PurchaseOrderItemsdetail();
                        $child->savePurchaseOderItems($modelleadetail->$fieldId);

                        /////cancel older po of same soucing deal
                        $sql_get = "SELECT * FROM purchase_order WHERE opportunity_name = :opportunity_name and purchase_order_id not in(:nw_purchase_order) ";
                        $res = Yii::$app->db->createCommand($sql_get, [':opportunity_name' => $data['opportunity_name'], ':nw_purchase_order' => $modelleadetail->$fieldId])->queryAll();

                        if ($res) {
                            $i = 1;
                            foreach ($res as $val) {
                                $purchase_order_id = $val['purchase_order_id'];
                                // echo "Processing record with quotes_no: " . $quotes_no . "\n";


                                // Now update the quotes_no and quote_stage
                                $sqlcancel = "UPDATE purchase_order SET stage = :stage  WHERE purchase_order_id  = :purchase_order_id";
                                Yii::$app->db->createCommand($sqlcancel, [
                                    ':stage' => 5,
                                    ':purchase_order_id' => $purchase_order_id,
                                ])->execute();

                                $i++;  // Increment the version number
                            }
                        } else {
                            // echo "No records found\n";
                        }

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);

                        //save to vp reports for purchase_order
                        $modelleadetail->saveToVpReports($modelleadetail->purchase_order_id);

                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "pickup") {
                $modelleadetail = new Pickup();
                $data = $_POST["pickup"];
                $sourcingdeal_id = $data["opportuity_name"] ?? "";
                $additional_info = $data["additional_info"] ?? "";
                if ($additional_info && is_array($additional_info)) {
                    $additional_info = implode(",", $additional_info);
                    $data["additional_info"] = $additional_info;
                }

                $vehicle_size1 = $data["vehicle_size1"] ?? "";
                if ($vehicle_size1 && is_array($vehicle_size1)) {
                    $vehicle_size1 = implode(",", $vehicle_size1);
                    $data["vehicle_size1"] = $vehicle_size1;
                }

                $current_pickup_stage = $modelleadetail->pickupStageCalc($data["pickup_status"] ?? null);
                $data["pickup_status"] = $current_pickup_stage;
                $fileInstance = 'pickup';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId; //echo $fieldId;die;

                    if ($modelleadetail->save()) {
                        $data = $_POST["pickup_document_details"] ?? [];
                        $fileInstance = 'pickup_document_details';
                        // echo "<pre>";
                        // print_r($_FILES[$fileInstance]);

                        // Loop through the array of files (assuming you're uploading multiple files)
                        if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                            foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                                // If the key is 1, the nested data is inside $files['attachment']
                                foreach ($files as $fileName => $file) {
                                    // Now, $file is the actual file name, and $fileName is 'attachment'
                                    // echo "<br>Processing file $key - $fileName: $file<br>";

                                    // Check if there is an upload error
                                    if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                        echo "File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Check if $fileName is empty (it shouldn't be, but good to check)
                                    if (empty($file)) {
                                        echo "File name for file $key is empty.<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Debugging: File passed all checks
                                    // echo "Conditions passed for file $key. File upload will proceed.<br>";

                                    // Create an UploadedFile instance
                                    $fileInstanceData = new \yii\web\UploadedFile([
                                        'name' => $file, // Use the correct file name
                                        'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                        'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                        'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                        'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                                    ]);

                                    // Call your method to save the file
                                    $result = $this->saveAttachedFiles($fileInstanceData);

                                    // Debugging: Output result of saving file
                                    // print_r($result);
                                    // die(); // Remove this when you're done debugging

                                    // Check if file saving was successful
                                    if ($result['success']) {
                                        // echo "File uploaded successfully. File name: " . $result['fileName'] . "<br>";
                                        $_POST["pickup_document_details"][$key]['attachment'] = $result['fileName'];
                                        // die(); // End execution after success
                                    } else {
                                        // echo "Issue in file saving for file $key. Error message: " . ($result['message'] ?? "Unknown error") . "<br>";
                                        continue; // Continue processing the next file
                                    }
                                }
                            }
                        }


                        // print_r($_POST["pickup_document_details"]);
                        //die; 

                        $fileInstance = 'pickup_vehicle_details';
                        // echo "<pre>";
                        // print_r($_FILES[$fileInstance]);

                        // Loop through the array of files (assuming you're uploading multiple files)
                        if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                            foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                                // If the key is 1, the nested data is inside $files['attachment']
                                foreach ($files as $fileName => $file) {
                                    // Now, $file is the actual file name, and $fileName is 'attachment'
                                    // echo "<br>Processing file $key - $fileName: $file<br>";

                                    // Check if there is an upload error
                                    if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                        echo "File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Check if $fileName is empty (it shouldn't be, but good to check)
                                    if (empty($file)) {
                                        echo "File name for file $key is empty.<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Debugging: File passed all checks
                                    // echo "Conditions passed for file $key. File upload will proceed.<br>";

                                    // Create an UploadedFile instance
                                    $fileInstanceData = new \yii\web\UploadedFile([
                                        'name' => $file, // Use the correct file name
                                        'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                        'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                        'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                        'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                                    ]);

                                    // Call your method to save the file
                                    $result = $this->saveAttachedFiles($fileInstanceData);

                                    // Debugging: Output result of saving file
                                    // print_r($result);
                                    // die(); // Remove this when you're done debugging

                                    // Check if file saving was successful
                                    if ($result['success']) {
                                        // echo "File uploaded successfully. File name: " . $result['fileName'] . "<br>";
                                        $_POST["pickup_vehicle_details"][$key]['attach'] = $result['fileName'];
                                        // die(); // End execution after success
                                    } else {
                                        // echo "Issue in file saving for file $key. Error message: " . ($result['message'] ?? "Unknown error") . "<br>";
                                        continue; // Continue processing the next file
                                    }
                                }
                            }
                        }

                        //start saving vehicle_planning files
                        $fileInstance = 'vehicle_planning';

                        // Loop through the array of files (assuming you're uploading multiple files)
                        // Check if the $_FILES array for this field exists and has files uploaded
                        if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                            foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                                // Handle each file in this specific file input (attachment)
                                foreach ($files as $fileName => $file) {
                                    // If file name is empty, fallback to the hidden file or another backup option
                                    if (empty($file)) {
                                        echo "No file selected for file vp $key.<br>";
                                        // Check if there is a hidden file (previously uploaded)
                                        if (isset($_POST["vehicle_planning"][$key][$fileName . '_hidden']) && !empty($_POST["vehicle_planning"][$key][$fileName . '_hidden'])) {
                                            // Assign the hidden file value to the attachment field
                                            $_POST["vehicle_planning"][$key][$fileName] = $_POST["vehicle_planning"][$key][$fileName . '_hidden'];
                                        }

                                        continue; // Skip this file and move to the next one
                                    }
                                    // Check for errors in file upload
                                    if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                        echo "vehicle_planning File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Create an UploadedFile instance
                                    $fileInstanceData = new \yii\web\UploadedFile([
                                        'name' => $file,
                                        'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                        'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                        'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                        'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                                    ]);

                                    // Call your method to save the file
                                    $result = $this->saveAttachedFiles($fileInstanceData);

                                    // Check if file saving was successful
                                    if ($result['success']) {
                                        $_POST["vehicle_planning"][$key][$fileName] = $result['fileName'];
                                        //delete old file
                                        if (isset($_POST["vehicle_planning"][$key][$fileName . '_hidden'])) {
                                            $records = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $_POST["vehicle_planning"][$key][$fileName . '_hidden']])
                                                ->one();
                                            $fileid = $records->attachmentsid;
                                            // print_r($records);die;
                                            $model = new Attachments();
                                            $records = $model->find()
                                                ->where(['attachmentsid' => $fileid])
                                                ->one();
                                            $fileName = $records['path'];
                                            //print_r($records);die;

                                            // Define the base directory for files
                                            $filePath = Yii::getAlias('@webroot/' . $fileName);
                                            // unlink($filePath);
                                            // Check if the file exists before attempting to delete it
                                            if (file_exists($filePath)) {
                                                // Attempt to delete the file
                                                if (unlink($filePath)) {
                                                    // echo "File removed successfully.";
                                                } else {
                                                    //  echo "Error: Unable to delete the file.";
                                                }
                                            } else {
                                                // echo "File does not exist.";
                                            }
                                        }
                                    } else {
                                        // Handle the failure, log the message if needed
                                        continue; // Continue processing the next file
                                    }
                                }
                            }
                        }
                        // end of saving vehicle_planning files


                        //start saving details_packing_list files
                        $fileInstance = 'details_packing_list';

                        // Loop through the array of files (assuming you're uploading multiple files)
                        // Check if the $_FILES array for this field exists and has files uploaded
                        if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                            foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                                // Handle each file in this specific file input (attachment)
                                foreach ($files as $fileName => $file) {
                                    // If file name is empty, fallback to the hidden file or another backup option
                                    if (empty($file)) {
                                        echo "No file selected for file packling_list $key.<br>";
                                        // Check if there is a hidden file (previously uploaded)
                                        if (isset($_POST["details_packing_list"][$key][$fileName . '_hidden']) && !empty($_POST["details_packing_list"][$key][$fileName . '_hidden'])) {
                                            // Assign the hidden file value to the attachment field
                                            $_POST["details_packing_list"][$key][$fileName] = $_POST["details_packing_list"][$key][$fileName . '_hidden'];
                                        }

                                        continue; // Skip this file and move to the next one
                                    }
                                    // Check for errors in file upload
                                    if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                        echo "details_packing_list File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Create an UploadedFile instance
                                    $fileInstanceData = new \yii\web\UploadedFile([
                                        'name' => $file,
                                        'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                        'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                        'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                        'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                                    ]);

                                    // Call your method to save the file
                                    $result = $this->saveAttachedFiles($fileInstanceData);

                                    // Check if file saving was successful
                                    if ($result['success']) {
                                        $_POST["details_packing_list"][$key][$fileName] = $result['fileName'];
                                        //delete old file
                                        if (isset($_POST["details_packing_list"][$key][$fileName . '_hidden'])) {
                                            $records = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $_POST["details_packing_list"][$key][$fileName . '_hidden']])
                                                ->one();
                                            $fileid = $records->attachmentsid;
                                            // print_r($records);die;
                                            $model = new Attachments();
                                            $records = $model->find()
                                                ->where(['attachmentsid' => $fileid])
                                                ->one();
                                            $fileName = $records['path'];
                                            //print_r($records);die;

                                            // Define the base directory for files
                                            $filePath = Yii::getAlias('@webroot/' . $fileName);
                                            // unlink($filePath);
                                            // Check if the file exists before attempting to delete it
                                            if (file_exists($filePath)) {
                                                // Attempt to delete the file
                                                if (unlink($filePath)) {
                                                    // echo "File removed successfully.";
                                                } else {
                                                    //  echo "Error: Unable to delete the file.";
                                                }
                                            } else {
                                                // echo "File does not exist.";
                                            }
                                        }
                                    } else {
                                        // Handle the failure, log the message if needed
                                        continue; // Continue processing the next file
                                    }
                                }
                            }
                        }
                        //end saving details_packing_list files

                        // start processing child table of packing_material
                        $packing_materials = new PackingMaterial();
                        $packing_materials->savePackingMaterials($modelleadetail->$fieldId);
                        // end processing child table of packing_material

                        // start processing child table of vehicle_planning
                        $vehicle_planning = new VehiclePlanning();
                        $vehicle_planning->saveVehiclePlanning($modelleadetail->$fieldId);
                        // end processing child table of vehicle_planning
                        // start processing child table of shipped_details
                        $shipped_details = new ShippedDetails();
                        $shipped_details->saveShippedDetails($modelleadetail->$fieldId);
                        // end processing child table of shipped_details

                        // start processing child table of details_packing_list
                        $details_packing_list = new DetailsPackingList();
                        $details_packing_list->saveDetailsPackingList($modelleadetail->$fieldId);
                        // end processing child table of details_packing_list
                        //save to child tables
                        $child1 = new PickupDocumentDetails();
                        $child1->savePickupDocumentDetails($modelleadetail->$fieldId);

                        $child2 = new PickupAssetDetail();
                        $child2->savePickupAssetDetail($modelleadetail->$fieldId);

                        $child3 = new PickupVehicleDetails();
                        $child3->savePickupVehicleDetails($modelleadetail->$fieldId);

                        $child1 = new PickupManualAssetDetail();
                        $child1->savePickupManualAssetDetail($modelleadetail->$fieldId);

                        // PickupDocumentDetails
                        //PickupAssetDetail
                        //PickupVehicleDetails
                        /**start of inspection data at the time of pickup create */
                        $modelleadetail->inspectionRelatedDataCreate($sourcingdeal_id, $modelleadetail->$fieldId);
                        /**end of inspection data at the time of pickup create */

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "iqclaptop") {
                // echo '<pre>';
                // print_r($_POST["iqc_laptop"]);exit;
                //now save submodules
                $modelleadetail = new IqcLaptop();
                $data = $_POST["iqc_laptop"];
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId; //echo $fieldId;die;
                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());
                        die();
                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {
                    print_r($modelleadetail->getErrors());
                    die();
                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }
                return false; // Indicate validation failure
            } else if ($module === "warehouse") {


                //now save submodules
                $modelleadetail = new Warehouse();
                $modelleadetail->attributes = $_POST["warehouse"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->warehouse_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->warehouse_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["warehouse_id" => $modelleadetail->warehouse_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->warehouse_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "vendorlocations") {
                //now save submodules
                $modelleadetail = new VendorLocations();
                $data = $_POST["vendor_locations"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->vendorloc_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->vendorloc_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["vendorloc_id" => $modelleadetail->vendorloc_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->vendorloc_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "iqcdesktop") {
                //now save submodules
                $modelleadetail = new IqcDesktop();
                $data = $_POST["iqc_desktop"];
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $a_val = $this->getAutoNo($tabs);
                    $modelleadetail->{$autoField} = $a_val;
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId; //echo $fieldId;die;
                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());
                        die();
                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {
                    print_r($modelleadetail->getErrors());
                    die();
                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }
                return false; // Indicate validation failure
            } else if ($module === "iqctft") {
                //now save submodules
                $modelleadetail = new IqcTft();
                $data = $_POST["iqc_tft"];
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $a_val = $this->getAutoNo($tabs);
                    $modelleadetail->{$autoField} = $a_val;
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId; //echo $fieldId;die;
                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());
                        die();
                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {
                    print_r($modelleadetail->getErrors());
                    die();
                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }
                return false; // Indicate validation failure
            } else if ($module === "iqclaptopgrade") {
                //now save submodules
                $modelleadetail = new IqcLaptopGrade();
                $data = $_POST["iqc_laptop_grade"];
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $a_val = $this->getAutoNo($tabs);
                    $modelleadetail->{$autoField} = $a_val;
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId; //echo $fieldId;die;
                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());
                        die();
                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {
                    print_r($modelleadetail->getErrors());
                    die();
                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }
                return false; // Indicate validation failure
            } else if ($module === "contacts") {
                $data = $_POST["contacts"];
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }

                //now save submodules
                $modelleadetail = new Contacts();
                $modelleadetail->attributes = $data;

                // Hash the password before validation
                if (!empty($modelleadetail->password)) {
                    $modelleadetail->password = Yii::$app->security->generatePasswordHash($modelleadetail->password);
                }
                // Generate a new auth_key for the user
                $modelleadetail->auth_key = Yii::$app->security->generateRandomString();
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;

                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->contacts_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->contacts_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["contacts_id" => $modelleadetail->contacts_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->contacts_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "iqcdesktopgrade") {
                $modelleadetail = new IqcDesktopGrade();
                $data = $_POST["iqc_desktop_grade"];
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $a_val = $this->getAutoNo($tabs);
                    $modelleadetail->{$autoField} = $a_val;
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId; //echo $fieldId;die;
                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());
                        die();
                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {
                    print_r($modelleadetail->getErrors());
                    die();
                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }
                return false; // Indicate validation failure
            } else if ($module === "user") {

                //now save submodules
                $modelleadetail = new User();
                $modelleadetail->attributes = $_POST["user"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;

                // Handle the uploaded profile picture
                $imageFile = UploadedFile::getInstanceByName('user[profilepic]');
                if ($imageFile) {
                    // Generate unique filename to avoid overwriting
                    $uniqueFileName = uniqid('profile_') . '.' . $imageFile->extension;

                    // Define the upload path
                    $uploadDir = Yii::getAlias('@webroot/thememain/profile/');
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
                    }

                    $filePath = $uploadDir . $uniqueFileName;

                    // Save the file and set the profilepic attribute
                    if ($imageFile->saveAs($filePath)) {
                        $modelleadetail->profilepic = 'thememain/profile/' . $uniqueFileName; // Save relative path
                    } else {
                        Yii::error('Failed to save uploaded file.');
                        return false; // Exit on file save failure
                    }
                }


                // Hash the password before validation
                if (!empty($modelleadetail->password_hash)) {
                    $modelleadetail->password_hash = Yii::$app->security->generatePasswordHash($modelleadetail->password_hash);
                }
                // Generate a new auth_key for the user
                $modelleadetail->auth_key = Yii::$app->security->generateRandomString();

                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;


                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["id" => $modelleadetail->id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->id, $auditstatus, Yii::$app->user->id);
                        }

                        // Save or Update the Role in user2role Table
                        $userid = $modelleadetail->id; // Get the saved user's ID
                        $role = $_POST["user"]["role"]; // Get the role from the form data

                        // Check if user2role already exists for this user
                        $user2role = User2role::findOne(['userid' => $userid]);

                        if ($user2role) {
                            // Update existing role
                            $user2role->roleid = $role;
                        } else {
                            // Create a new role entry
                            $user2role = new User2role();
                            $user2role->userid = $userid;
                            $user2role->roleid = $role;
                        }

                        if (!$user2role->save()) {
                            // Handle role save error
                            Yii::error("Failed to save role: " . json_encode($user2role->getErrors()));
                            $transaction->rollBack(); // Roll back the transaction
                            return false;
                        }

                        if (isset($_POST['user_targets'])) {
                            //code added for add target of user
                            $user_target = new UserTargets();
                            $user_target->saveUserTarget($_POST['user_targets'], $modelleadetail->id);
                        }
                        //end code added for add target of user
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "drilling") {
                //now save submodules
                $modelleadetail = new Drilling();
                $data = $_POST["drilling"];
                $data["drilling_status"] = '2';
                $fileInstance = 'drilling';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        // start processing child table of assets
                        $fileInstance = 'drilling_asset_details';
                        // Check if the $_FILES array for this field exists and has files uploaded
                        if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                            foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                                // Handle each file in this specific file input (attachment)
                                foreach ($files as $fileName => $file) {
                                    // If file name is empty, fallback to the hidden file or another backup option
                                    if (empty($file)) {
                                        echo "No file selected for file drilling_asset $key.<br>";
                                        // Check if there is a hidden file (previously uploaded)
                                        if (isset($_POST["drilling_asset_details"][$key][$fileName . '_hidden']) && !empty($_POST["drilling_asset_details"][$key][$fileName . '_hidden'])) {
                                            // Assign the hidden file value to the attachment field
                                            $_POST["drilling_asset_details"][$key][$fileName] = $_POST["drilling_asset_details"][$key][$fileName . '_hidden'];
                                        }

                                        continue; // Skip this file and move to the next one
                                    }
                                    // Check for errors in file upload
                                    if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                        echo "drilling_asset_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Create an UploadedFile instance
                                    $fileInstanceData = new \yii\web\UploadedFile([
                                        'name' => $file,
                                        'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                        'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                        'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                        'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                                    ]);

                                    // Call your method to save the file
                                    $result = $this->saveAttachedFiles($fileInstanceData);

                                    // Check if file saving was successful
                                    if ($result['success']) {
                                        $_POST["drilling_asset_details"][$key][$fileName] = $result['fileName'];
                                        //delete old file
                                        if (isset($_POST["drilling_asset_details"][$key][$fileName . '_hidden'])) {
                                            $records = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $_POST["drilling_asset_details"][$key][$fileName . '_hidden']])
                                                ->one();
                                            $fileid = $records->attachmentsid;
                                            // print_r($records);die;
                                            $model = new Attachments();
                                            $records = $model->find()
                                                ->where(['attachmentsid' => $fileid])
                                                ->one();
                                            $fileName = $records['path'];
                                            $filePath = Yii::getAlias('@webroot/' . $fileName);
                                            if (file_exists($filePath)) {
                                                // Attempt to delete the file
                                                if (unlink($filePath)) {
                                                    // echo "File removed successfully.";
                                                } else {
                                                    //  echo "Error: Unable to delete the file.";
                                                }
                                            } else {
                                                // echo "File does not exist.";
                                            }
                                        }
                                    } else {
                                        // Handle the failure, log the message if needed
                                        continue; // Continue processing the next file
                                    }
                                }
                            }
                        }
                        $drilling_assets = new DrillingAssetDetails();
                        $drilling_assets->saveDrillingAssets($modelleadetail->$fieldId);
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->drilling_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->drilling_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["drilling_id" => $modelleadetail->drilling_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->drilling_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        //save to vp reports for drilling
                        $modelleadetail->saveToVpReports($modelleadetail->drilling_id);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "quotes") {


                //now save submodules
                $modelleadetail = new Quotes();
                // as per client change on date 21-06-25 added by ptpatel
                if (isset($_POST["quotes"]["po_type"]) && is_array($_POST["quotes"]["po_type"])) {
                    $_POST["quotes"]["po_type"] = implode(",", $_POST["quotes"]["po_type"]);
                }
                $modelleadetail->attributes = $_POST["quotes"];
                ///////change sourcing deal sages based on quotes stages/////
                if ($_POST['quotes']['related_to'] == 51) {
                    $related_to_id = $_POST['quotes']['related_to_id'];
                    $quotestage = $_POST['quotes']['quote_stage'];
                    $srcstage = 0;

                    if ($quotestage == 1) //approved
                        $srcstage = 14; //won
                    if ($quotestage == 2) //negotiation
                        $srcstage = 13; //negotiation
                    if ($quotestage == 6) //quote rejected by customer
                        $srcstage = 27; //lost
                    if ($quotestage == 7) //quoted to customer
                        $srcstage = 12; //quote sent to client
                    if ($srcstage) {
                        $oldAttributessrc = Yii::$app->db->createCommand("select * from `sourcingdeal` where sourcingdeal_id=:sourcingdeal_id")
                            ->bindValue(":sourcingdeal_id", $related_to_id)
                            ->queryOne();
                        //update sourcing deal
                        $sql = "Update sourcingdeal set stage = :srcstage where sourcingdeal_id = :sourcingdeal_id";
                        $updt = Yii::$app->db->createCommand($sql)
                            ->bindValue(":srcstage", $srcstage)
                            ->bindValue(":sourcingdeal_id", $related_to_id)
                            ->execute();

                        $newattributessrc = array("stage" => $srcstage);

                        $modlog->auditlog($oldAttributessrc, $newattributessrc, "sourcingdeal", $related_to_id, 2, Yii::$app->user->id);
                    }
                    //if quote already created for this sourcing deal then udate it to cancelled
                    //also update quote no version
                    // Retrieve the quotes
                    $sql_get = "SELECT * FROM quotes WHERE related_to_id = :related_to_id AND related_to = 51 ORDER BY quotes_id ASC";
                    $res = Yii::$app->db->createCommand($sql_get, [':related_to_id' => $related_to_id])->queryAll();

                    if ($res) {
                        $i = 1;
                        foreach ($res as $val) {
                            // Debugging to confirm the loop is processing each row
                            $quotes_no = $val['quotes_no'] . " V$i"; // Modify quotes_no as needed
                            $quotes_id = $val['quotes_id'];
                            // echo "Processing record with quotes_no: " . $quotes_no . "\n";


                            // Now update the quotes_no and quote_stage
                            $sqlcancel = "UPDATE quotes SET quote_stage = :quote_stage, quotes_no = :quotes_no 
                      WHERE related_to_id = :related_to_id AND related_to = 51 AND quotes_id = :quotes_id";
                            Yii::$app->db->createCommand($sqlcancel, [
                                ':quote_stage' => 4,
                                ':quotes_no' => $quotes_no,
                                ':related_to_id' => $related_to_id,
                                ':quotes_id' => $quotes_id,
                            ])->execute();

                            $i++;  // Increment the version number
                        }
                    } else {
                        // echo "No records found\n";
                    }

                    // die;

                    // $sqlcancel = "Update quotes set quote_stage='4' where related_to_id = $related_to_id and related_to = 51";
                    // Yii::$app->db->createCommand($sqlcancel)->execute();
                }

                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId; //echo $fieldId;die;

                    if ($modelleadetail->save()) {


                        //save to child table
                        $child = new QuotedItemsDetail();
                        $child->saveQuotedItemsDetail($modelleadetail->$fieldId);

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "degaussing") {
                //now save submodules
                $modelleadetail = new Degaussing();
                $data = $_POST["degaussing"];
                $data["degaussing_status"] = 2;
                $fileInstance = 'degaussing';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $modelleadetail->attributes = $data;

                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        // start processing child table of assets
                        $fileInstance = 'degaussing_asset_details';
                        // Check if the $_FILES array for this field exists and has files uploaded
                        if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                            foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                                // Handle each file in this specific file input (attachment)
                                foreach ($files as $fileName => $file) {
                                    // If file name is empty, fallback to the hidden file or another backup option
                                    if (empty($file)) {
                                        echo "No file selected for file degaussing_asset $key.<br>";
                                        // Check if there is a hidden file (previously uploaded)
                                        if (isset($_POST["degaussing_asset_details"][$key][$fileName . '_hidden']) && !empty($_POST["degaussing_asset_details"][$key][$fileName . '_hidden'])) {
                                            // Assign the hidden file value to the attachment field
                                            $_POST["degaussing_asset_details"][$key][$fileName] = $_POST["degaussing_asset_details"][$key][$fileName . '_hidden'];
                                        }

                                        continue; // Skip this file and move to the next one
                                    }
                                    // Check for errors in file upload
                                    if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                        echo "degaussing_asset_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Create an UploadedFile instance
                                    $fileInstanceData = new \yii\web\UploadedFile([
                                        'name' => $file,
                                        'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                        'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                        'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                        'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                                    ]);

                                    // Call your method to save the file
                                    $result = $this->saveAttachedFiles($fileInstanceData);

                                    // Check if file saving was successful
                                    if ($result['success']) {
                                        $_POST["degaussing_asset_details"][$key][$fileName] = $result['fileName'];
                                        //delete old file
                                        if (isset($_POST["degaussing_asset_details"][$key][$fileName . '_hidden'])) {
                                            $records = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $_POST["degaussing_asset_details"][$key][$fileName . '_hidden']])
                                                ->one();
                                            $fileid = $records->attachmentsid;
                                            // print_r($records);die;
                                            $model = new Attachments();
                                            $records = $model->find()
                                                ->where(['attachmentsid' => $fileid])
                                                ->one();
                                            $fileName = $records['path'];
                                            $filePath = Yii::getAlias('@webroot/' . $fileName);
                                            if (file_exists($filePath)) {
                                                // Attempt to delete the file
                                                if (unlink($filePath)) {
                                                    // echo "File removed successfully.";
                                                } else {
                                                    //  echo "Error: Unable to delete the file.";
                                                }
                                            } else {
                                                // echo "File does not exist.";
                                            }
                                        }
                                    } else {
                                        // Handle the failure, log the message if needed
                                        continue; // Continue processing the next file
                                    }
                                }
                            }
                        }
                        $data_wiping_assets = new DegaussingAssetDetails();
                        $data_wiping_assets->saveDegaussingAssets($modelleadetail->$fieldId);
                        //audit log
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->degaussinginfo_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->degaussinginfo_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["degaussinginfo_id" => $modelleadetail->degaussinginfo_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->degaussinginfo_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        //save to vp reports for degaussing
                        $modelleadetail->saveToVpReports($modelleadetail->degaussinginfo_id);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "datawiping") {
                //now save submodules
                $modelleadetail = new DataWiping();
                $data = $_POST["data_wiping"];
                $data["wiping_status"] = 2;
                $fileInstance = 'data_wiping';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        // start processing child table of assets
                        $fileInstance = 'data_wiping_asset_details';
                        // Check if the $_FILES array for this field exists and has files uploaded
                        if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                            foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                                // Handle each file in this specific file input (attachment)
                                foreach ($files as $fileName => $file) {
                                    // If file name is empty, fallback to the hidden file or another backup option
                                    if (empty($file)) {
                                        echo "No file selected for file $key.<br>";
                                        // Check if there is a hidden file (previously uploaded)
                                        if (isset($_POST["data_wiping_asset_details"][$key][$fileName . '_hidden']) && !empty($_POST["data_wiping_asset_details"][$key][$fileName . '_hidden'])) {
                                            // Assign the hidden file value to the attachment field
                                            $_POST["data_wiping_asset_details"][$key][$fileName] = $_POST["data_wiping_asset_details"][$key][$fileName . '_hidden'];
                                        }

                                        continue; // Skip this file and move to the next one
                                    }
                                    // Check for errors in file upload
                                    if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                        echo "data_wiping_asset_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Create an UploadedFile instance
                                    $fileInstanceData = new \yii\web\UploadedFile([
                                        'name' => $file,
                                        'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                        'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                        'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                        'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                                    ]);

                                    // Call your method to save the file
                                    $result = $this->saveAttachedFiles($fileInstanceData);

                                    // Check if file saving was successful
                                    if ($result['success']) {
                                        $_POST["data_wiping_asset_details"][$key][$fileName] = $result['fileName'];
                                        //delete old file
                                        if (isset($_POST["data_wiping_asset_details"][$key][$fileName . '_hidden'])) {
                                            $records = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $_POST["data_wiping_asset_details"][$key][$fileName . '_hidden']])
                                                ->one();
                                            $fileid = $records->attachmentsid;
                                            // print_r($records);die;
                                            $model = new Attachments();
                                            $records = $model->find()
                                                ->where(['attachmentsid' => $fileid])
                                                ->one();
                                            $fileName = $records['path'];
                                            $filePath = Yii::getAlias('@webroot/' . $fileName);
                                            if (file_exists($filePath)) {
                                                // Attempt to delete the file
                                                if (unlink($filePath)) {
                                                    // echo "File removed successfully.";
                                                } else {
                                                    //  echo "Error: Unable to delete the file.";
                                                }
                                            } else {
                                                // echo "File does not exist.";
                                            }
                                        }
                                    } else {
                                        // Handle the failure, log the message if needed
                                        continue; // Continue processing the next file
                                    }
                                }
                            }
                        }
                        $data_wiping_assets = new DataWipingAssetDetails();
                        $data_wiping_assets->saveDataWippingAssets($modelleadetail->$fieldId);
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->datawiping_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->datawiping_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["datawiping_id" => $modelleadetail->datawiping_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->datawiping_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        //save to vp reports for datawiping
                        $modelleadetail->saveToVpReports($modelleadetail->datawiping_id);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "shredding") {
                //now save submodules
                $modelleadetail = new Shredding();
                $data = $_POST["shredding"];
                $data["shredding_status"] = '2';
                $fileInstance = 'shredding';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        // start processing child table of assets
                        $fileInstance = 'shredding_asset_details';
                        // Check if the $_FILES array for this field exists and has files uploaded
                        if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                            foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                                // Handle each file in this specific file input (attachment)
                                foreach ($files as $fileName => $file) {
                                    // If file name is empty, fallback to the hidden file or another backup option
                                    if (empty($file)) {
                                        echo "No file selected for file $key.<br>";
                                        // Check if there is a hidden file (previously uploaded)
                                        if (isset($_POST["shredding_asset_details"][$key][$fileName . '_hidden']) && !empty($_POST["shredding_asset_details"][$key][$fileName . '_hidden'])) {
                                            // Assign the hidden file value to the attachment field
                                            $_POST["shredding_asset_details"][$key][$fileName] = $_POST["shredding_asset_details"][$key][$fileName . '_hidden'];
                                        }

                                        continue; // Skip this file and move to the next one
                                    }
                                    // Check for errors in file upload
                                    if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                        echo "shredding_asset_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                        continue; // Skip this file and move to the next one
                                    }

                                    // Create an UploadedFile instance
                                    $fileInstanceData = new \yii\web\UploadedFile([
                                        'name' => $file,
                                        'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                        'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                        'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                        'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                                    ]);

                                    // Call your method to save the file
                                    $result = $this->saveAttachedFiles($fileInstanceData);

                                    // Check if file saving was successful
                                    if ($result['success']) {
                                        $_POST["shredding_asset_details"][$key][$fileName] = $result['fileName'];
                                        //delete old file
                                        if (isset($_POST["shredding_asset_details"][$key][$fileName . '_hidden'])) {
                                            $records = \app\models\Attachments::find()
                                                ->where(['attachmentsid' => $_POST["shredding_asset_details"][$key][$fileName . '_hidden']])
                                                ->one();
                                            $fileid = $records->attachmentsid;
                                            // print_r($records);die;
                                            $model = new Attachments();
                                            $records = $model->find()
                                                ->where(['attachmentsid' => $fileid])
                                                ->one();
                                            $fileName = $records['path'];
                                            $filePath = Yii::getAlias('@webroot/' . $fileName);
                                            if (file_exists($filePath)) {
                                                // Attempt to delete the file
                                                if (unlink($filePath)) {
                                                    // echo "File removed successfully.";
                                                } else {
                                                    //  echo "Error: Unable to delete the file.";
                                                }
                                            } else {
                                                // echo "File does not exist.";
                                            }
                                        }
                                    } else {
                                        // Handle the failure, log the message if needed
                                        continue; // Continue processing the next file
                                    }
                                }
                            }
                        }
                        $shredding_assets = new ShreddingAssetDetails();
                        $shredding_assets->saveShreddingAssets($modelleadetail->$fieldId);
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->shredding_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->shredding_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["shredding_id" => $modelleadetail->shredding_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->shredding_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        //save to vp reports for drilling
                        $modelleadetail->saveToVpReports($modelleadetail->shredding_id);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "inspection") {
                //now save submodules
                $modelleadetail = new Inspection();
                $data = $_POST["inspection"];
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                // print_r($data);die;
                //check if product added or not
                if (empty($data['insection_type'])) {
                    throw new Exception('Inspection type cannot be blank');

                }
                if (isset($data['submit_for_logistics']) && $data['submit_for_logistics'] == 1) {
                    $data['stages'] = 2; //logistics pending
                    //assign to Logistic manager
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H52' limit 1";
                    //added by deepika on 19 june 2025

                    // $reports = "-- If only one user exists in the role, return that user
                    // (
                    //     SELECT u.id
                    //     FROM user u
                    //     JOIN user2role ur ON ur.userid = u.id
                    //     WHERE u.deleted = 0
                    //     AND u.status = 10
                    //     AND ur.roleid = 'H52'
                    //     LIMIT 1
                    // )

                    // UNION ALL

                    // -- If there are multiple users, find the next higher user ID after the last modifier
                    // (
                    //     SELECT u.id
                    //     FROM user u
                    //     JOIN user2role ur ON ur.userid = u.id
                    //     WHERE u.deleted = 0
                    //     AND u.status = 10
                    //     AND ur.roleid = 'H52'
                    //     AND u.id > (
                    //         SELECT whodid
                    //         FROM modtracker_basic
                    //         WHERE module = '".$this->moduleName."'
                    //         AND status = 2
                    //         ORDER BY changedon DESC
                    //         LIMIT 1
                    //     )
                    //     ORDER BY u.id ASC
                    //     LIMIT 1
                    // )

                    // UNION ALL

                    // -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    // (
                    //     SELECT u.id
                    //     FROM user u
                    //     JOIN user2role ur ON ur.userid = u.id
                    //     WHERE u.deleted = 0
                    //     AND u.status = 10
                    //     AND ur.roleid = 'H52'
                    //     AND u.id != (
                    //         SELECT whodid
                    //         FROM modtracker_basic
                    //         WHERE module ='".$this->moduleName."'
                    //         AND status = 2
                    //         ORDER BY changedon DESC
                    //         LIMIT 1
                    //     )
                    //     ORDER BY u.id ASC
                    //     LIMIT 1
                    // )

                    // LIMIT 1;";


                    // $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // // print_r($rest);die;
                    // if (isset($rest['id']) && !empty($rest['id'])) {
                    //     $data['ownerid'] = $rest['id'];
                    //     $ownerid = $data['ownerid'];
                    // }
                    //new logic assign to logistic spoc added on 5 july 2025

                    if (isset($data['logistics_spoc']) && !empty($data['logistics_spoc'])) {
                        $data['ownerid'] = $data['logistics_spoc'];
                        $ownerid = $data['ownerid'];
                    } else {
                        Yii::$app->session->setFlash('error', 'Info: Invalid request. Logistic SPOC cannot be blank when submitted for logistics');
                        // Throw a BadRequestHttpException
                        throw new Exception('Info: Invalid request. Logistic SPOC cannot be blank when submitted for logistics');
                    }
                }

                $modelleadetail->attributes = $data;
                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;

                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // echo $autoField;
                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->inspection_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->inspection_id);


                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["inspection_id" => $modelleadetail->inspection_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->inspection_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "weighing") {
                //now save submodules
                $modelleadetail = new Weighing();
                $modelleadetail->attributes = $_POST["weighing"];
                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;

                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // echo $autoField;
                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->weighing_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->weighing_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["weighing_id" => $modelleadetail->weighing_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->weighing_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "drillingformat") {
                //now save submodules
                $modelleadetail = new DrillingFormat();
                $data = $_POST["drilling_format"];
                $fileInstance = 'drilling_format';
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    //audit log
                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->drilling_format_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->drilling_format_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["drilling_format_id" => $modelleadetail->drilling_format_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->drilling_format_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "drillingvendordetails") {
                //now save submodules
                $modelleadetail = new DrillingVendorDetails();
                $modelleadetail->attributes = $_POST["drilling_vendor_details"];
                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;

                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // echo $autoField;
                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId; // echo $fieldId;die;
                    if ($modelleadetail->save()) {

                        //save to child table
                        $child = new DrillingVendorCosting();
                        $child->saveDrillingVendorCosting($modelleadetail->$fieldId);


                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "degaussingformat") {
                //now save submodules
                $modelleadetail = new DegaussingFormat();
                $data = $_POST["degaussing_format"];
                $fileInstance = 'degaussing_format';
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }
                if (isset($data["image_available"]) && $data["image_available"] != 2) {
                    $data["image"] = "";
                }
                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    //audit log
                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->degaussing_format_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->degaussing_format_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["degaussing_format_id" => $modelleadetail->degaussing_format_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->degaussing_format_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "degaussingvendordetails") {
                //now save submodules
                $modelleadetail = new DegaussingVendorDetails();
                $modelleadetail->attributes = $_POST["degaussing_vendor_details"];
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId; // echo $fieldId;die;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $child = new DegaussingVendorCosting();
                        $child->saveDegaussingVendorCosting($modelleadetail->$fieldId);

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "contracts") {


                //now save submodules
                $modelleadetail = new Contracts();
                $modelleadetail->attributes = $_POST["contracts"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    //audit log

                    //added by ptpatel on date 17-06-25 code to send contract to RSM user for approval and notification
                    if ($modelleadetail->send_for_review == 1) {
                        // && $modelleadetail->contract_status == 1 //1 = draft
                        $modelleadetail->contract_status = "2"; //in review
                        ///assign to RSM/ZM
                        // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H51' ORDER BY RAND()   limit 1";
                        $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H51'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H51'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H51'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                        $rest = Yii::$app->db->createCommand($reports)->queryOne();
                        // print_r($reports);die;
                        if (isset($rest['id']) && !empty($rest['id'])) {
                            $modelleadetail->ownerid = $rest['id'];
                            $ownerid = $modelleadetail->ownerid;
                        }
                        // $message = "Contract No. " . $modelleadetail->contract_no . " is submitted for Review. Please check";
                        // $this->sendnotification($ownerid, $message, $this->moduleName, $modelleadetail->contract_id);

                    }
                    // echo "<pre>";print_r($modelleadetail->attributes);die;
                    //end code added by ptpatel
                    if ($modelleadetail->save()) {
                        // echo "<pre>";print_r($modelleadetail->attributes);die;
                        if ($modelleadetail->contract_status == 2) {
                            $message = "Contract No. " . $modelleadetail->contract_no . " is submitted for Review. Please check";
                            $this->sendnotification($modelleadetail->ownerid, $message, $this->moduleName, $modelleadetail->contract_id);

                        }
                        ///check if contract_status =3 approved the setvbilling type of accoun to RC
                        // if ($modelleadetail->contract_status == 3) {
                        //     $sql = "update vendor_account set billing_type=1 where vendoraccid =:vendoraccid";
                        //     Yii::$app->db->createCommand($sql)->bindValue(":vendoraccid", $modelleadetail->account_name)->execute();
                        // }
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->contract_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->contract_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["contract_id" => $modelleadetail->contract_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->contract_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "servicedetail") {
                //now save submodules
                $modelleadetail = new Servicedetail();
                $modelleadetail->attributes = $_POST["servicedetail"];
                $data = $_POST["servicedetail"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId;
                    // echo $fieldId;
                    // die;

                    if ($modelleadetail->save()) {

                        if ($_POST['servicedetail']['related_to'] == 51 && !empty($_POST['servicedetail']['related_to_id'])) { //update soucing deal stage
                            $dtsrc = array();
                            $dtsrc['stage'] = 5;
                            $output = Sourcingdeal::updateAll($dtsrc, 'sourcingdeal_id = :sourcingdeal_id', [':sourcingdeal_id' => $_POST['servicedetail']['related_to_id']]);
                            $modelsrc = new Sourcingdeal();

                            $modelsrc->oldAttributes = Yii::$app->db->createCommand("select * from `sourcingdeal` where sourcingdeal_id=:sourcingdeal_id")
                                ->bindValue(":sourcingdeal_id", $_POST['servicedetail']['related_to_id'])
                                ->queryOne();

                            $modlog->auditlog($modelsrc->oldAttributes, $dtsrc, 'sourcingdeal', $_POST['servicedetail']['related_to_id'], 2, Yii::$app->user->id);
                        }
                        // echo $modelleadetail->$fieldId;
                        // die;
                        //save to child table
                        $child = new ServicedetailDetails();
                        $child->saveServicedetailDetails($modelleadetail->$fieldId);

                        if ($data['related_to'] == 51) {
                            $this->SaveSourcingdealTotal($data['related_to_id']);
                        }

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "campaign") {


                //now save submodules
                $modelleadetail = new Campaign();
                $modelleadetail->attributes = $_POST["campaign"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->campaign_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->campaign_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["campaign_id" => $modelleadetail->campaign_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->campaign_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "sourcingdealcontactrole") {
                // echo "<pre>";
                //    print_r($_POST);die;
                //now save submodules
                $modelleadetail = new Sourcingdealcontactrole();
                $modelleadetail->attributes = $_POST["sourcingdeal_contact_role"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;

                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->contact_roleid, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->contact_roleid);

                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "opportunitycontactrole") {
                // echo "<pre>";
                //    print_r($_POST);die;
                //now save submodules
                $modelleadetail = new Opportunitycontactrole();
                $modelleadetail->attributes = $_POST["opportunity_contact_role"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;

                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->contact_roleid, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->contact_roleid);

                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "drillingcalculator") {
                //now save submodules
                $modelleadetail = new DrillingCalculatorParents();
                $modelleadetail->attributes = $_POST["drilling_calculator_parents"];
                $data = $_POST["drilling_calculator_parents"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId;
                    // echo $fieldId;
                    // die;

                    if ($modelleadetail->save()) {

                        // echo $modelleadetail->$fieldId;
                        // die;
                        //save to child table
                        $child = new DrillingCalculator();
                        $child->saveDrillingCalculator($modelleadetail->$fieldId);

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "datawipingcalculator") {
                //now save submodules
                $modelleadetail = new DatawipingCalculatorParents();
                $modelleadetail->attributes = $_POST["datawiping_calculator_parents"];
                $data = $_POST["datawiping_calculator_parents"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId;
                    // echo $fieldId;
                    // die;

                    if ($modelleadetail->save()) {

                        // echo $modelleadetail->$fieldId;
                        // die;
                        //save to child table
                        $child = new DatawipingCalculator();
                        $child->saveDatawipingCalculator($modelleadetail->$fieldId);


                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "shreddingcalculator") {
                //now save submodules
                $modelleadetail = new ShreddingCalculatorParents();
                $modelleadetail->attributes = $_POST["shredding_calculator_parents"];
                $data = $_POST["shredding_calculator_parents"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId;
                    // echo $fieldId;
                    // die;

                    if ($modelleadetail->save()) {

                        // echo $modelleadetail->$fieldId;
                        // die;
                        //save to child table
                        $child = new ShreddingCalculator();
                        $child->saveShreddingCalculator($modelleadetail->$fieldId);


                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "degaussingcalculator") {
                //now save submodules
                $modelleadetail = new DegaussingCalculatorParents();
                $modelleadetail->attributes = $_POST["degaussing_calculator_parents"];
                $data = $_POST["degaussing_calculator_parents"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId;
                    // echo $fieldId;
                    // die;

                    if ($modelleadetail->save()) {

                        // echo $modelleadetail->$fieldId;
                        // die;
                        //save to child tabl
                        $child = new DegaussingCalculator();
                        $child->saveDegaussingCalculator($modelleadetail->$fieldId);


                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "termsandconditions") {
                //now save submodules
                $modelleadetail = new TermsAndConditions();
                $modelleadetail->attributes = $_POST["terms_and_conditions"];
                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;

                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                // echo $autoField;
                // echo "<pre>";
                // print_r($modelleadetail->attributes);
                // die;
                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->terms_conditions_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->terms_conditions_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["terms_conditions_id" => $modelleadetail->terms_conditions_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->terms_conditions_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);
                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "gateinward") {
                //now save submodules
                $modelleadetail = new Gateinward();
                $data = $_POST["gateinward"];
                $docket_number = $data["docket_number"];
                $fileInstance = 'gateinward';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $modelleadetail->attributes = $data;
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        // start processing child table of assets
                        $gateinward_details = new GateinwardDetails();
                        $gateinward_details->saveGateinwardDetails($modelleadetail->$fieldId);
                        //audit log
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->gateinward_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->gateinward_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["gateinward_id" => $modelleadetail->gateinward_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->gateinward_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);

                        //update status of pickup shipped details items 
                        $modelleadetail->updateStatusOfPickupShippedDetailsItems($docket_number);
                        $transaction->commit();
                        return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "pickupcalculator") {
                //now save submodules
                $modelleadetail = new PickupCalculatorParent();
                $modelleadetail->attributes = $_POST["pickup_calculator_parent"];
                $data = $_POST["pickup_calculator_parent"];
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId;
                    // echo $fieldId;
                    // die;

                    if ($modelleadetail->save()) {

                        // echo $modelleadetail->$fieldId;
                        // die;
                        //save to child tabl

                        $child = new PickupCalculator();
                        $child->savePickupCalculator($modelleadetail->$fieldId);


                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "segregation") {
                //now save submodules
                //now save submodules
                $modelleadetail = new Segregation();
                $data = $_POST["segregation"];
                $modelleadetail->attributes = $_POST["segregation"];
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->save_as_draft == 1) {
                    $fieldId = $this->fieldId;
                    //save code
                    $inventory = new Inventory();
                    $inventory->saveInventory($_POST['segregation'], $_POST['segregation_detail'], Yii::$app->request->get('itemid'));

                    $isupdate = Yii::$app->db->createCommand("UPDATE grn_asset_detail set grn_status = 0 where grn_asset_detail_id=:id")
                        ->bindValue(":id", Yii::$app->request->get('itemid'))
                        ->execute();

                    $transaction->commit();
                    return true; // Indicate success
                } else {
                    if ($modelleadetail->validate()) {
                        $fieldId = $this->fieldId;
                        if ($modelleadetail->save()) {
                            //save to child table
                            $segregation_detail = new SegregationDetail();
                            $segregation_detail->saveSegregationDetail($modelleadetail->$fieldId);

                            $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                            //now save custom fields 
                            if (!empty($CS)) {
                                $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                                echo "CS=";
                                //print_r($CS);echo "<br>";die;
                                $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                                $command->execute();
                                $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                            }
                            $transaction->commit();
                            return true; // Indicate success

                        } else {
                            print_r($modelleadetail->getErrors());

                            die();

                            Yii::error(
                                "Failed to save model: " .
                                json_encode(
                                    $modelleadetail->getErrors()
                                )
                            );
                            return false; // Indicate failure
                        }
                    } else {

                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Validation errors: " .
                            json_encode($modelleadetail->getErrors())
                        );
                        return false; // Indicate validation failure
                    }
                }

                return false; // Indicate validation failure
            } else if ($module === "payments") {
                $data = $_POST["payments"];
                if (isset($data['submit_approval']) && $data['submit_approval'] == 1) {
                    // $data['stage'] = 6; //logistics pending

                    //assign to pricing team
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H16' limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H16'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H16'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H16'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $data['stage'] = "2";
                        $ownerid = $data['ownerid'];


                    }
                }

                //now save submodules
                $modelleadetail = new Payments();
                $modelleadetail->attributes = $data;
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    // eelscho $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }


                // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                if ($modelleadetail->validate()) {
                    //audit log
                    $fieldId = $this->fieldId; //echo $fieldId;die;


                    if ($modelleadetail->save()) {

                        //send notifications
                        $paymentinfo = Payments::find()->select('payment_no')->where(['payments_id' => $modelleadetail->payments_id])->one();
                        $notification = new Notifications();
                        $notification->userid = $data['ownerid'];
                        $notification->message = "Payment No " . $paymentinfo['payment_no'] . " has been submitted for approval. Please check";
                        $notification->read_status = 0; // Unread notification
                        $notification->display_status = 0;
                        $notification->source_link = Yii::$app->request->baseUrl . "/" . $this->moduleName . "/detail?Record=" . $modelleadetail->payments_id;
                        ;
                        $notification->createdtime = date('Y-m-d H:i:s');
                        $notification->modifiedtime = date('Y-m-d H:i:s');
                        if (!$notification->save()) {
                            echo 'save failed';
                            exit;
                        }

                        //save to child table
                        $child = new PaymentsInvoiceDetails();
                        $child->savePaymentsInvoiceDetails($modelleadetail->payments_id);

                        //save to child table
                        $child = new PaymentDetails();
                        $child->savePaymentDetails($modelleadetail->payments_id);
                        //
                        $child = new PaymentsAttachmentDetail();
                        $child->savePaymentsAttachmentDetail($modelleadetail->payments_id);

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->payments_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->payments_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["payments_id" => $modelleadetail->payments_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->payments_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);

                        ///save to reports
                        $modelleadetail->savetoreports($modelleadetail->payments_id);

                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "tagging") {
                $data = Yii::$app->request->post('tagging_product_detail');
                foreach ($data as $item) {
                    if (!$transaction->isActive) {
                        $transaction = Yii::$app->db->beginTransaction();
                    }
                    try {
                        $tagExists = Inventory::find()
                            ->where(['tag_number' => $item['tag_number']])
                            ->exists();

                        $serialExists = Inventory::find()
                            ->where(['serial_number' => $item['serial_number']])
                            ->exists();

                        if (!$tagExists && !$serialExists) {
                            $modelleadetail = new Inventory();
                            if ($modelleadetail->updateInventoryStatus($item, $module)) {
                                $transaction->commit();
                                // return true; // Indicate success
                            } else {
                                print_r($modelleadetail->getErrors());
                                die();
                                Yii::error(
                                    "Validation errors: " .
                                    json_encode($modelleadetail->getErrors())
                                );
                                return false;
                            }
                        } else {
                            if ($tagExists) {
                                echo "Tag number already exists.";
                            }
                            if ($serialExists) {
                                echo "Serial number already exists.";
                            }
                        }
                    } catch (Exception $e) {
                        Yii::error("Exception occurred: " . $e->getMessage(), __METHOD__);
                        echo "An error occurred: " . $e->getMessage();
                    }
                }
                return true;
            }
            //this code is not used because it autosave after checking tag no
            else if ($module === "stickerremoval") {
                // echo "<pre>";print_r($_POST);die;
                $data = Yii::$app->request->post('sticker_removal_detail');
                foreach ($data as $item) {
                    if (!$transaction->isActive) {
                        $transaction = Yii::$app->db->beginTransaction();
                    }
                    $modelleadetail = new Inventory();
                    if ($modelleadetail->updateInventoryStatus($item, $module)) {
                        $transaction->commit();
                        // return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());
                        die();
                        Yii::error(
                            "Validation errors: " .
                            json_encode($modelleadetail->getErrors())
                        );
                        return false;
                    }
                }
                return true;
            }
            //this code is not used because it autosave after checking tag no
            else if ($module === "cleaning") {
                // echo "<pre>";print_r($_POST);die;
                $data = Yii::$app->request->post('cleaning_detail');
                foreach ($data as $item) {
                    if (!$transaction->isActive) {
                        $transaction = Yii::$app->db->beginTransaction();
                    }
                    $modelleadetail = new Inventory();
                    if ($modelleadetail->updateInventoryStatus($item, $module)) {
                        $transaction->commit();
                        // return true; // Indicate success
                    } else {
                        print_r($modelleadetail->getErrors());
                        die();
                        Yii::error(
                            "Validation errors: " .
                            json_encode($modelleadetail->getErrors())
                        );
                        return false;
                    }
                }
                return true;
            } else if ($module === "quotesdit") {


                //now save submodules
                $modelleadetail = new QuotesDit();

                $data = $_POST["quotes_dit"]; // Array of key-value pairs to update

                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }


                if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {

                    $data['quote_stage'] = 2; //First Approval pending
                    //    echo  $data['quote_stage'];die;

                    ///assign to business head
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H83' ORDER BY RAND()   limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H83'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H83'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H83'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }

                }
                // print_r($data);die;
                $modelleadetail->attributes = $data;

                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->quotes_dit_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->quotes_dit_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["quotes_dit_id" => $modelleadetail->quotes_dit_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->quotes_dit_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);


                        //save to child table
                        $QuotesditShipDetail = new QuotesditShipDetail();
                        $QuotesditShipDetail->saveQuotesditShipDetail($modelleadetail->quotes_dit_id);

                        //save to child table
                        $QuotesditProductDetail = new QuotesditProductDetail();
                        $QuotesditProductDetail->saveQuotesditProductDetail($modelleadetail->quotes_dit_id);

                        if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {



                            $message = "Quote No. " . $modelleadetail->{$autoField} . " is submitted for First Approval. Please check";
                            $this->sendnotification($data['ownerid'], $message, $this->moduleName, $modelleadetail->quotes_dit_id);

                        }


                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "widget") {
                //now save submodules
                $modelleadetail = new Widget();
                $data = $_POST["widget"];
                $modelleadetail->attributes = $_POST["widget"];
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        $transaction->commit();
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "salesorderdit") {


                //now save submodules
                $modelleadetail = new SalesorderDit();

                $data = $_POST["salesorder_dit"]; // Array of key-value pairs to update

                if ($autoField = $this->checkAutoNo()) {
                    // echo $autoField;die;
                    // print_r($model);die;
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {

                    $data['so_stage'] = 2; //First Approval pending
                    //    echo  $data['quote_stage'];die;

                    ///assign to devit cx
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H86' ORDER BY RAND()   limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H86'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H86'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H86'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }


                }

                ///////save attachments////////////////
                $fileInstance = 'salesorder_dit';
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = (string) $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }
                // print_r($data);die;
                $modelleadetail->attributes = $data;
                // echo "<pre>";
                // print_r($modelleadetail->attributes);die;

                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->salesorder_dit_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->salesorder_dit_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["salesorder_dit_id" => $modelleadetail->salesorder_dit_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->salesorder_dit_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        //save to child table
                        $SalesorderditProductDetails = new SalesorderditProductDetails();
                        $SalesorderditProductDetails->saveSalesorderditProductDetails($modelleadetail->salesorder_dit_id);

                        //save to child table
                        $SalesorderditShipToAddress = new SalesorderditShipToAddress();
                        $SalesorderditShipToAddress->saveSalesorderditShipToAddress($modelleadetail->salesorder_dit_id);

                        if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {



                            $message = "Sales Order No. " . $modelleadetail->{$autoField} . " is submitted for First Approval. Please check";
                            $this->sendnotification($data['ownerid'], $message, $this->moduleName, $modelleadetail->salesorder_dit_id);

                        }


                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "purchaseorderdit") {


                //now save submodules
                $modelleadetail = new PurchaseOrderDit();

                $data = $_POST["purchase_order_dit"]; // Array of key-value pairs to update

                if ($autoField = $this->checkAutoNo()) {
                    //get std code of bill address 
                    $bill_entitiy_name = $data["bill_entitiy_name"];
                    $command = Yii::$app->db->createCommand("SELECT std_code FROM warehouse join city on city.cityid = warehouse.city WHERE warehouse_id = :business_entity
                    ")->bindValue(":business_entity", $bill_entitiy_name);
                    $columns = $command->queryOne();
                    $modelleadetail->{$autoField} = $this->getPOnuber($columns['std_code']);
                    // $this->getAutoNo($tabs);
                }

                //echo $modelleadetail->{$autoField};die;
                if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {

                    $data['stage'] = 2; //First Approval pending 
                    //    echo  $data['quote_stage'];die;

                    ///assign to devit Finance Manger 
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H87' ORDER BY RAND()   limit 1";
                    //added by deepika on 19 june 
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H87'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H87'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H87'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";

                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }


                }

                // print_r($data);die;
                $modelleadetail->attributes = $data;
                // echo "<pre>";
                // print_r($modelleadetail->attributes);die;

                if ($modelleadetail->validate()) {
                    //audit log

                    if ($modelleadetail->save()) {
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->purchaseorder_dit_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->purchaseorder_dit_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["purchaseorder_dit_id" => $modelleadetail->purchaseorder_dit_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->purchaseorder_dit_id, $auditstatus, Yii::$app->user->id);
                        }
                        if ($autoField = $this->checkAutoNo())
                            $this->setAutoNo($tabs);



                        //save to child table
                        $PurchaseorderditProductDetails = new PurchaseorderditProductDetails();
                        $PurchaseorderditProductDetails->savePurchaseorderditProductDetails($modelleadetail->purchaseorder_dit_id);

                        if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {



                            $message = "Purchase Order No. " . $modelleadetail->{$autoField} . " is submitted for First Approval. Please check";
                            $this->sendnotification($data['ownerid'], $message, $this->moduleName, $modelleadetail->purchaseorder_dit_id);

                        }


                        $transaction->commit();

                        // print_r($CS);die;
                        //die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }


                return false; // Indicate validation failure
            } else if ($module === "productpricebook") {
                //now save submodules
                $modelleadetail = new ProductPriceBook();
                $modelleadetail->attributes = $_POST["product_price_book"];
                // echo "<pre>";print_r($modelleadetail);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "servicepricebook") {
                //now save submodules
                $modelleadetail = new ServicePriceBook();
                $modelleadetail->attributes = $_POST["service_price_book"];
                // echo "<pre>";print_r($modelleadetail);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "pickupaddress") {
                //now save submodules
                $modelleadetail = new PickupAddress();
                $modelleadetail->attributes = $_POST["pickup_address"];
                // echo "<pre>";print_r($_POST);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "billingtoaddress") {
                //now save submodules
                $modelleadetail = new BillingToAddress();
                $modelleadetail->attributes = $_POST["billing_to_address"];
                // echo "<pre>";print_r($_POST);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "billingfromaddress") {
                //now save submodules
                $modelleadetail = new BillingFromAddress();
                $modelleadetail->attributes = $_POST["billing_from_address"];
                // echo "<pre>";print_r($_POST);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "deliveryaddress") {
                //now save submodules
                $modelleadetail = new DeliveryAddress();
                $modelleadetail->attributes = $_POST["delivery_address"];
                // echo "<pre>";print_r($_POST);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "deliverychallandit") {
                //now save submodules
                $modelleadetail = new DeliveryChallandit();
                // echo "<pre>";print_r($_POST);die;

                $modelleadetail->attributes = $_POST["delivery_challandit"];
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->status == 1 && $modelleadetail->send_for_approval == 1) {  //1 = draft
                    $modelleadetail->status = "2"; //in Approval Pending
                    ///assign to finance 
                    // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H51' ORDER BY RAND()   limit 1";
                    $reports = "-- If only one user exists in the role, return that user
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If there are multiple users, find the next higher user ID after the last modifier
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    AND u.id > (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module = '" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    AND u.id != (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module ='" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($reports);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $modelleadetail->ownerid = $rest['id'];
                        $ownerid = $modelleadetail->ownerid;
                    }
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {

                        if ($modelleadetail->status == 2) {
                            $message = "Delivery Challan No. " . $modelleadetail->deliverychallan_no . " is submitted for Approval. Please check";
                            $this->sendnotification($ownerid, $message, $this->moduleName, $modelleadetail->deliverychallan_id);
                        }


                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->deliverychallan_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->deliverychallan_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["deliverychallan_id" => $modelleadetail->deliverychallan_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->deliverychallan_id, $auditstatus, Yii::$app->user->id);
                        }

                        //save to child table
                        $DeliverychallanditProductDetails = new DeliverychallanditProductDetails();
                        $DeliverychallanditProductDetails->saveDeliverychallanditProductDetails($modelleadetail->deliverychallan_id);
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "packinglistdit") {
                //now save submodules
                $modelleadetail = new PackingListDit();
                // echo "<pre>";print_r($_POST);
                // die;

                $modelleadetail->attributes = $_POST["packing_list_dit"];
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->packinglist_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->packinglist_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["packinglist_id" => $modelleadetail->packinglist_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->packinglist_id, $auditstatus, Yii::$app->user->id);
                        }

                        //save to child table
                        $PackinglistditProductDetails = new PackinglistditProductDetails();
                        $PackinglistditProductDetails->savePackinglistditProductDetails($modelleadetail->packinglist_id);

                        //when packing is created then of approved DC,then DC status should change to packing list generated from here  
                        $modelleadetail->updateStatusOfDeliveychallan($_POST["packing_list_dit"]["dc_number"]);

                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "focdit") {
                //now save submodules
                $modelleadetail = new FocDit();
                $modelleadetail->attributes = $_POST["foc_dit"];
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->stage == 1 && $modelleadetail->submit_for_approval == 1) {  //1 = draft
                    $modelleadetail->stage = "2"; //in submit_for_approval
                    ///assign to finance 
                    // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H51' ORDER BY RAND()   limit 1";
                    $reports = "-- If only one user exists in the role, return that user
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H91'
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If there are multiple users, find the next higher user ID after the last modifier
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H91'
                                    AND u.id > (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module = '" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H91'
                                    AND u.id != (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module ='" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($reports);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $modelleadetail->ownerid = $rest['id'];
                        $ownerid = $modelleadetail->ownerid;
                    }
                }

                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->focdit_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->focdit_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["focdit_id" => $modelleadetail->focdit_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->focdit_id, $auditstatus, Yii::$app->user->id);
                        }

                        //save to child table
                        $FocditProductDetails = new FocditProductDetails();
                        $FocditProductDetails->saveFocditProductDetails($modelleadetail->focdit_id);
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "grndit") {

                //now save submodules
                $modelleadetail = new GrnDit();
                // echo "<pre>";print_r($_POST);die;
                //start saving grn_dit files
                $data = $_POST["grn_dit"];
                $fileInstance = 'grn_dit';

                // Loop through the array of files (assuming you're uploading multiple files)
                if (isset($_FILES[$fileInstance])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {

                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                // end of saving grn_dit files
                $data["status"] = $modelleadetail->grnStageCalc($data["status"] ?? null);

                $modelleadetail->attributes = $data;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {
                        //save to child table
                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->$fieldId);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, [$fieldId => $modelleadetail->$fieldId]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->$fieldId, $auditstatus, Yii::$app->user->id);
                        }

                        //save to child table
                        $GrnditProductDetails = new GrnditProductDetails();
                        $GrnditProductDetails->saveGrnditProductDetails($modelleadetail->grndit_id);

                        //save to child table
                        $GrnditBarcodes = new GrnditBarcodes();
                        $GrnditBarcodes->saveGrnditBarcodes($modelleadetail->grndit_id);

                        //$modelleadetail->Savetoinventory($modelleadetail->grndit_id);

                        $transaction->commit();
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "invoicedit") {
                //now save submodules
                $modelleadetail = new Invoicedit();
                $modelleadetail->attributes = $_POST["invoicedit"];
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }
                if ($modelleadetail->invoice_status == 1 && $modelleadetail->send_for_approval == 1) {  //1 = draft
                    $modelleadetail->invoice_status = "2"; //pending for approval
                    ///assign to dev finance manager
                    // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H51' ORDER BY RAND()   limit 1";
                    $reports = "-- If only one user exists in the role, return that user
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If there are multiple users, find the next higher user ID after the last modifier
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    AND u.id > (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module = '" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    AND u.id != (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module ='" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($reports);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $modelleadetail->ownerid = $rest['id'];
                        $ownerid = $modelleadetail->ownerid;
                    }
                }

                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->invoicedit_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->invoicedit_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["invoicedit_id" => $modelleadetail->invoicedit_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->invoicedit_id, $auditstatus, Yii::$app->user->id);
                        }

                        //save to child table
                        $InvoiceditProductDetails = new InvoiceditProductDetails();
                        $InvoiceditProductDetails->saveInvoiceditProductDetails($modelleadetail->invoicedit_id);

                        if ($modelleadetail->send_for_approval == 1) {
                            //send notification to finance manager
                            $message = "Invoice DevIT No. " . $modelleadetail->invoicedit_no . " is submitted for Approval. Please check";
                            $this->sendnotification($ownerid, $message, $this->moduleName, $modelleadetail->invoicedit_id);
                        }

                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "paymentdit") {
                //now save submodules
                $modelleadetail = new Paymentdit();
                $modelleadetail->attributes = $_POST["paymentdit"];
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }


                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->paymentdit_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->paymentdit_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["paymentdit_id" => $modelleadetail->paymentdit_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->paymentdit_id, $auditstatus, Yii::$app->user->id);
                        }
                        ///save to reports
                        $modelleadetail->savetoreports($modelleadetail->paymentdit_id);

                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } else if ($module === "exportrequest") {
                //now save submodules
                $modelleadetail = new Exportrequest();
                // echo "<pre>";print_r($_POST);
                $modelleadetail->attributes = $_POST["exportrequest"];
                $modelleadetail->status = 1;
                // echo "<pre>";print_r($modelleadetail->attributes);die;
                if ($autoField = $this->checkAutoNo()) {
                    $modelleadetail->{$autoField} = $this->getAutoNo($tabs);
                }

                if ($modelleadetail->validate()) {
                    $fieldId = $this->fieldId;
                    if ($modelleadetail->save()) {

                        $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, $this->moduleName, $modelleadetail->export_request_id, $auditstatus, Yii::$app->user->id);
                        $this->updateCRMSequence($module, $modelleadetail->export_request_id);
                        //now save custom fields 
                        if (!empty($CS)) {
                            $CS = array_merge($CS, ["export_request_id" => $modelleadetail->export_request_id]);
                            echo "CS=";
                            //print_r($CS);echo "<br>";die;
                            $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                            $command->execute();
                            $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelleadetail->export_request_id, $auditstatus, Yii::$app->user->id);
                        }
                        
                        $transaction->commit();
                        // echo "save";die;
                        return true; // Indicate success

                    } else {
                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Failed to save model: " .
                            json_encode(
                                $modelleadetail->getErrors()
                            )
                        );
                        return false; // Indicate failure
                    }
                } else {

                    print_r($modelleadetail->getErrors());

                    die();

                    Yii::error(
                        "Validation errors: " .
                        json_encode($modelleadetail->getErrors())
                    );
                    return false; // Indicate validation failure
                }

                return false; // Indicate validation failure
            } 



        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            $transaction->rollBack();
            echo "Failed to save data: " . $e->getMessage() . "<br>";
            echo "<pre>";
            print_r($e->getTraceAsString());
            die;
            Yii::$app->session->setFlash(
                "error",
                "Failed to save data: " . $e->getMessage()
            );
        }

        // return false; // Indicate no action taken
    }

    public function updateModule($RecordId)
    {
        $modlog = new ModtrackerBasic();
        $auditstatus = 2;
        $mode = $_POST["mode"];
        $module = $_POST["module"];
        $customtablename = $module . "cf";
        $CS = array();
        if (isset($_POST[$customtablename]))
            $CS = $_POST[$customtablename];
        else
            $CS = '';
        // print_r($CS);die;

        //$CS['leadid'] = "1";
        //check uitype for date
        // echo "<pre>";
        // print_r($_POST);
        $this->convertdate();
        // echo "<pre>";
        // print_r($_POST);
        // die;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $uid = Yii::$app->user->id;
            if ($module === "leads") {

                //now update submodules
                $modelleadetail = new Leadinformation();

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `leadinformation` where leadid=:leadid")
                    ->bindValue(":leadid", $RecordId)
                    ->queryOne();

                // added on 13 jan 2025
                //check if send for approval is checked then update lead status to approval pending
                if (isset($_POST["leadinformation"]['send_for_approval']) && $_POST["leadinformation"]['send_for_approval'] == 1) {
                    $_POST["leadinformation"]['leadstatus'] = '4';
                    //assign to reports to user
                    $reports = User::find()->select('reports_to')->where(['id' => Yii::$app->user->id])->one();
                    // print_r($reports);die;
                    $reportsto = $reports['reports_to'];
                    if (!empty($reportsto)) {
                        $_POST["leadinformation"]['ownerid'] = $reportsto;
                    }

                }

                //check if data_validated is checked then update lead status to ready for calling
                if (isset($_POST["leadinformation"]['data_validated']) && $_POST["leadinformation"]['data_validated'] == 1) {
                    $_POST["leadinformation"]['leadstatus'] = '16';
                    //assign round robin to cold calling
                    // $reports = "SELECT id 
                    //                 FROM user 
                    //                 JOIN user2role ON user2role.userid = user.id 
                    //                 WHERE user.deleted = 0 
                    //                 AND status = 10 
                    //                 AND user2role.roleid = 'H73' 
                    //                 ORDER BY RAND() 
                    //                 LIMIT 1;
                    // ";
                    $reports = "-- First, get the next higher user ID after the last modifier
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H73'
                                        AND u.id > (
                                            SELECT whodid
                                            FROM modtracker_basic
                                            WHERE module = 'Leads' AND status = 2
                                            ORDER BY changedon DESC
                                            LIMIT 1
                                        )
                                        ORDER BY u.id ASC
                                        LIMIT 1
                                    )
                                    UNION ALL
                                    -- If none, wrap around to the lowest ID (still excluding the last modifier)
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H73'
                                        AND u.id != (
                                            SELECT whodid
                                            FROM modtracker_basic
                                            WHERE module = 'Leads' AND status = 2
                                            ORDER BY changedon DESC
                                            LIMIT 1
                                        )
                                        ORDER BY u.id ASC
                                        LIMIT 1
                                    )
                                    LIMIT 1";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    //print_r($rest);
                    // echo $rest['id'];die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $_POST["leadinformation"]['ownerid'] = $rest['id'];

                    }
                    //echo $_POST["leadinformation"]['ownerid'];
                    $message = "Lead No " . $modelleadetail->oldAttributes['lead_no'] . " is ready for calling.Please check";
                    $this->sendnotification($_POST["leadinformation"]['ownerid'], $message, $this->moduleName, $RecordId);
                }

                //check if ready_to_pitch is checked then update lead status to lead created
                if (isset($_POST["leadinformation"]['ready_to_pitch']) && $_POST["leadinformation"]['ready_to_pitch'] == 1) {
                    $_POST["leadinformation"]['leadstatus'] = '1';

                }


                // echo $_POST["leadinformation"]['ownerid'];die;
                $data = $_POST["leadinformation"]; // Array of key-value pairs to update
                //edn on 13 jan 2025
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                //print_r($data);die;

                //check if stage == new ad ownerid is changed
                if ($modelleadetail->oldAttributes['ownerid'] != $data['ownerid'] && $modelleadetail->oldAttributes['leadstatus'] == 14) {
                    //then update stage to 15 = Data Verification
                    $data['leadstatus'] = 15;
                    $message = "Lead No " . $modelleadetail->oldAttributes['lead_no'] . " is sent for Data Verification. Please check";
                    $this->sendnotification($data['ownerid'], $message, $this->moduleName, $RecordId);
                }
                $output = Leadinformation::updateAll($data, 'leadid = :leadid', [':leadid' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;



                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

                //save to child table
                $LeadContactsDetail = new LeadContactsDetail();
                $LeadContactsDetail->saveLeadContactsDetail($RecordId);

            } else if ($module === "call") {


                //now save submodules
                $modelleadetail = new CallInformation();
                $modelleadetail->attributes = $_POST["call_information"];
                $data = $_POST["call_information"]; // Array of key-value pairs to update
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `call_information` where callinfo_id=:callinfo_id")
                    ->bindValue(":callinfo_id", $RecordId)
                    ->queryOne();
                $output = CallInformation::updateAll($data, 'callinfo_id = :callinfo_id', [':callinfo_id' => $RecordId]);
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "meeting") {
                //now update submodules
                $modelleadetail = new MeetingInformation();
                $data = $_POST["meeting_information"]; // Array of key-value pairs to update
                if (!empty($data['internal_participants']) && is_array($data['internal_participants']))
                    $data['internal_participants'] = implode(', ', $data['internal_participants']);
                if (!empty($data['external_participants']) && is_array($data['external_participants']))
                    $data['external_participants'] = implode(', ', $data['external_participants']);
                //print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `meeting_information` where meetinginfo_id=:meetinginfo_id")
                    ->bindValue(":meetinginfo_id", $RecordId)
                    ->queryOne();
                $output = MeetingInformation::updateAll($data, 'meetinginfo_id = :meetinginfo_id', [':meetinginfo_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "task") {

                //now update submodules
                $modelleadetail = new TaskInformation();
                $data = $_POST["task_information"]; // Array of key-value pairs to update
                // print_r($data);die;
                if (!empty($data['notify_by']))
                    $data['notify_by'] = implode(', ', $data['notify_by']);
                // echo $data['notify_by'];die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `task_information` where taskinfo_id=:taskinfo_id")
                    ->bindValue(":taskinfo_id", $RecordId)
                    ->queryOne();
                $output = TaskInformation::updateAll($data, 'taskinfo_id = :taskinfo_id', [':taskinfo_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "documents") {

                //now update submodules
                $modelleadetail = new Documents();
                $data = $_POST["documents"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `documents` where docid=:docid")
                    ->bindValue(":docid", $RecordId)
                    ->queryOne();
                // print_r($modelleadetail->oldAttributes['folderid']);die;
                //first save doc
                $file = UploadedFile::getInstanceByName('documents[filename]'); // Optional file upload
                // echo $file;
                // print_r($_FILES);die;
                $documents = Yii::$app->request->post('documents'); // Text content

                if (!$file || empty($documents)) {
                    // return ['success' => false, 'message' => 'You must provide file and fill required fields'];
                }

                $fileUrl = null;



                // Handle file upload if a file is provided
                if ($file) {
                    // Security: Validate file extension and MIME type
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'zip'];
                    $allowedMimeTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/pdf',
                        'application/zip',
                        'application/x-zip-compressed',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                    // Maximum file size allowed (5GB)
                    $maxFileSize = 5 * 1024 * 1024 * 1024; // 5GB in bytes
                    // Check if file exceeds maximum allowed size (5GB)
                    if ($file->size > $maxFileSize) {
                        return ['success' => false, 'message' => 'File size exceeds the maximum allowed size of 5GB.'];
                    } else if (!in_array($file->extension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
                        return ['success' => false, 'message' => 'Invalid file type.'];
                    } else {
                        //delete old file
                        $records = \app\models\Attachments::find()
                            ->where(['attachmentsid' => $modelleadetail->oldAttributes['filename']])
                            ->one();
                        $fileid = $records->attachmentsid;
                        // print_r($records);die;
                        $model = new Attachments();
                        $records = $model->find()
                            ->where(['attachmentsid' => $fileid])
                            ->one();
                        $fileName = $records['path'];
                        //print_r($records);die;

                        // Define the base directory for files
                        $filePath = Yii::getAlias('@webroot/' . $fileName);
                        // unlink($filePath);
                        // Check if the file exists before attempting to delete it
                        if (file_exists($filePath)) {
                            // Attempt to delete the file
                            if (unlink($filePath)) {
                                // echo "File removed successfully.";
                            } else {
                                //  echo "Error: Unable to delete the file.";
                            }
                        } else {
                            // echo "File does not exist.";
                        }
                    }

                    // Determine the directory structure based on year, month, and week
                    $year = date('Y');
                    $month = date('m');
                    $week = date('W'); // Week of the year

                    //get folder name from id
                    // echo $modelleadetail->folderid;die;
                    // echo $data['folderid'];die;
                    $command = Yii::$app->db->createCommand("select * from `attachmentsfolder`  where folderid=:folderid")->bindValue(':folderid', $data['folderid']);
                    $folder = $command->queryOne();
                    // print_r($folder);die;
                    $foldername = $folder['path'];

                    // Define the upload base path
                    $baseUploadPath = Yii::getAlias('@webroot');
                    $targetPath = $baseUploadPath . "/" . $foldername . "/$year/$month/week_$week/";


                    // Create directories if they do not exist
                    if (!is_dir($targetPath)) {
                        if (!mkdir($targetPath, 0755, true)) {
                            return ['success' => false, 'message' => 'Failed to create upload directories.'];
                        }
                    }

                    // Generate a secure unique file name
                    $fileName = uniqid() . '.' . $file->extension;
                    $filePath = $targetPath . $fileName;
                    $filesavepath = $foldername . "/$year/$month/week_$week/" . $fileName;


                    //save to attachments
                    $modelatach = new Attachments();
                    $modelatach->name = $file->name;
                    $modelatach->type = $file->type;
                    $modelatach->path = $filesavepath;
                    $modelatach->storedname = $fileName;
                    if ($modelatach->validate()) {
                        if ($modelatach->save()) {
                            $data['filename'] = $modelatach->attachmentsid;
                        }
                    }


                    // Save the file
                    if ($file->saveAs($filePath)) {
                        $fileUrl = Yii::getAlias('@web') . "/" . $foldername . "/$year/$month/week_$week/" . $fileName;
                    } else {
                        $message = 'Failed to save the file.';
                        die;
                    }
                }
                $output = Documents::updateAll($data, 'docid = :docid', [':docid' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "opportunities") {


                //now update submodules
                $modelleadetail = new Opportunity();
                $data = $_POST["opportunity"]; // Array of key-value pairs to update                

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `opportunity` where opportunity_id=:opportunity_id")
                    ->bindValue(":opportunity_id", $RecordId)
                    ->queryOne();
                // echo "<pre>";
                // print_r( $modelleadetail->oldAttributes);die;
                //if(isset($data['team_responsible'])){ to resolve issue when submit priceing by devitisr code added by ptpatel on date 04-07-25

                if (!empty($data['team_responsible']))
                    $data['team_responsible'] = implode(',', $data['team_responsible']);
                if (isset($data['team_responsible'])) {
                    if (!is_string($data['team_responsible'])) {
                        $data['team_responsible'] = (string) $data['team_responsible'];
                    }
                }
                //check if user id not equal to ownerid and check if userid is sa_assinged,sf_assigned or procurement_team_member
                // echo $uid;
                // echo $data['sa_assigned'];die;
                $pricing_done = 0;
                if ($modelleadetail->oldAttributes['ownerid'] != $data['ownerid'] && ($uid == $data['sa_assigned'] || $uid == $data['sf_assigned'] || $uid == $data['procurement_team_member'])) {
                    $data['ownerid'] = $modelleadetail->oldAttributes['ownerid'];
                    //now check if pricing_done is checked
                    if (isset($data['pricing_done']) && $data['pricing_done'] == 1) {
                        $pricing_done = $data['pricing_done'];

                        //first check if product are added.
                        $condprod = $modelleadetail->checkProducts($RecordId);
                        //echo $condprod;die;
                        // If no records were found, throw an error
                        if (!$condprod) {
                            //echo "Error: Invalid request. Add Products in Laptop, Desktop, TFT, or General Inspection detail.";die;
                            Yii::$app->session->setFlash('error', 'Error: Invalid request.Add Products before pricing done');
                            // Throw a BadRequestHttpException
                            throw new Exception('Invalid request.Add Products before pricing done');
                        }

                        //check if  product validity or rejected 
                        $condprod = $modelleadetail->checkProductPricing($RecordId);
                        //check if exist in opportunity_pricing_done
                        $ex_pd = "Select count(*) as cnt from `opportunity_pricing_done` where userid=:userid and opportunity_id=:opportunity_id";
                        $exs = Yii::$app->db->createCommand($ex_pd)->bindValue(":userid", $uid)->bindValue(":opportunity_id", $RecordId)->queryOne();
                        $cntpd = $exs['cnt'];
                        if (!$cntpd) {
                            //save to tables
                            $sql_pd = "Insert into `opportunity_pricing_done` set userid=:userid,opportunity_id=:opportunity_id";
                            Yii::$app->db->createCommand($sql_pd)->bindValue(":userid", $uid)->bindValue(":opportunity_id", $RecordId)->execute();

                        }
                        //now check if all team responsile have done pricing
                        $team = $modelleadetail->oldAttributes['team_responsible']; // String representation of team
                        $teamArray = explode(',', $team); // Convert the string into an array
                        $cnt_s = 0;
                        $cnt_t = 0;
                        //print_r($teamArray);die;
                        $cnarr = count($teamArray);
                        if (in_array(1, $teamArray) || in_array(3, $teamArray)) {//solution team/solution factory

                            $ex_pd = "Select count(*) as cnt from  `opportunity_pricing_done` where userid=:userid and opportunity_id=:opportunity_id";
                            $exs = Yii::$app->db->createCommand($ex_pd)->bindValue(":userid", $data['sa_assigned'])->bindValue(":opportunity_id", $RecordId)->queryOne();
                            $cnt_s += $exs['cnt'];

                            $ex_pd = "Select count(*) as cnt from `opportunity_pricing_done` where userid=:userid and opportunity_id=:opportunity_id";
                            $exs = Yii::$app->db->createCommand($ex_pd)->bindValue(":userid", $data['sf_assigned'])->bindValue(":opportunity_id", $RecordId)->queryOne();
                            $cnt_s += $exs['cnt'];
                            // echo $cnt_s;die;

                            if ($cnt_s == 0)
                                $data['pricing_done'] = 0;

                        }
                        if (in_array(2, $teamArray)) {//procurement
                            $ex_pd = "Select count(*) as cnt from  `opportunity_pricing_done` where userid=:userid and opportunity_id=:opportunity_id";
                            $exs = Yii::$app->db->createCommand($ex_pd)->bindValue(":userid", $data['procurement_team_member'])->bindValue(":opportunity_id", $RecordId)->queryOne();
                            $cnt_t += $exs['cnt'];

                            if ($cnt_t == 0)
                                $data['pricing_done'] = 0;
                        }

                        if (($cnarr == 2 || $cnarr == 3) && $cnt_s == 0 && $cnt_t == 0)
                            $data['pricing_done'] = 0;
                        if ($data['pricing_done']) {
                            //change opportunity stage to purchagse price received = 5
                            $data['opportunity_stage'] = 5;
                        }
                        // echo $data['pricing_done'];die;


                    }

                }


                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                if (isset($data['submit_for_screening']) && $data['submit_for_screening'] == 1) {
                    $data['opportunity_stage'] = 2; //screening

                    //assign to solution team
                    // $reports = "SELECT id 
                    //                 FROM user 
                    //                 JOIN user2role ON user2role.userid = user.id 
                    //                 WHERE user.deleted = 0 
                    //                 AND status = 10 
                    //                 AND user2role.roleid = 'H67' 
                    //                 ORDER BY RAND() 
                    //                 LIMIT 1;
                    //                 ";
                    //assign to screening team added on 4 sept 2025 as per CR Points
                     $reports = "SELECT id 
                                    FROM user 
                                    JOIN user2role ON user2role.userid = user.id 
                                    WHERE user.deleted = 0 
                                    AND status = 10 
                                    AND user2role.roleid = 'H99' 
                                    ORDER BY RAND() 
                                    LIMIT 1;
                                    ";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }
                    $message = "Opportunity No " . $modelleadetail->oldAttributes['opportunity_no'] . " is submitted for screening.Please check";
                    $this->sendnotification($ownerid, $message, $this->moduleName, $RecordId);
                }
                if (isset($data['submit_for_pricing']) && $data['submit_for_pricing'] == 1) {


                    $data['opportunity_stage'] = 4; //submit for pricing

                    //assign to SA team
                    // echo $data['sa_assigned'];die;
                    // if (isset($data['sa_assigned']) && !empty($data['sa_assigned'])) {
                    //     $data['ownerid'] = $data['sa_assigned'];
                    //     $ownerid = $data['ownerid'];
                    // }

                    //now check if all team responsile have done pricing
                    $team = $modelleadetail->oldAttributes['team_responsible']; // String representation of team
                    $teamArray = explode(',', $team); // Convert the string into an array

                    $cnarr = count($teamArray);
                    if (in_array(1, $teamArray)) {//solution team
                        $message = "Opportunity No " . $modelleadetail->oldAttributes['opportunity_no'] . " is submitted for pricing. Please check";
                        $this->sendnotification($data['sa_assigned'], $message, $this->moduleName, $RecordId);

                        $message = "Opportunity No " . $modelleadetail->oldAttributes['opportunity_no'] . " is submitted for pricing. Please check";
                        $this->sendnotification($data['sf_assigned'], $message, $this->moduleName, $RecordId);
                    }
                    if (in_array(2, $teamArray)) {//procurement team
                        $message = "Opportunity No " . $modelleadetail->oldAttributes['opportunity_no'] . " is submitted for pricing. Please check";
                        $this->sendnotification($data['procurement_team_member'], $message, $this->moduleName, $RecordId);
                    }

                }

                $output = Opportunity::updateAll($data, 'opportunity_id = :opportunity_id', [':opportunity_id' => $RecordId]);
                // Build the update query using createCommand
                // $command = Opportunity::find()->createCommand();

                // Set the raw SQL query and parameters
                // $sql = $command->update('opportunity', $data, 'opportunity_id = :opportunity_id', [':opportunity_id' => $RecordId]);

                // Output the SQL query
                // echo $sql->getRawSql();die;
                // print_r($modelleadetail->oldAttributes);die;
                if ($pricing_done)
                    $data['pricing_done'] = 1;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

                Yii::$app->db->createCommand("delete from `opportunity_product_detail` where opportunity_id=:opportunity_id")->bindValue(":opportunity_id", $RecordId)->execute();
                //save to child table
                $OpportunityProductDetail = new OpportunityProductDetail();
                $OpportunityProductDetail->saveOpportunityProductDetail($RecordId);

                Yii::$app->db->createCommand("delete from `opportunity_ship_detail` where opportunity_id=:opportunity_id")->bindValue(":opportunity_id", $RecordId)->execute();
                //save to child table
                $OpportunityShipDetail = new OpportunityShipDetail();
                $OpportunityShipDetail->saveOpportunityShipDetail($RecordId);

            } else if ($module === "sourcingdeal") {


                //now update submodules
                $modelleadetail = new Sourcingdeal();
                $data = $_POST["sourcingdeal"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                // check f stage = won then auto save closure month ad date
                if (isset($data['stage']) && $data['stage'] == 14) //won stage
                {
                    $data['closing_date'] = date("Y-m-d");
                    $data['closure_month'] = date("m");
                    $data['closure_week'] = date("W", strtotime($data['closing_date']));
                }
                //end stage
                if (isset($data['submit_for_pricing']) && $data['submit_for_pricing'] == 1) {
                    $data['stage'] = 6; //pricing pending

                    //assign to pricing team
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H20' limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H20'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H20'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H20'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;
                    ";
                    //echo $reports;die;
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    //print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                        //also assign product and service to pricing team
                        $reports = "Update servicedetail set ownerid = :ownerid where related_to = 51 and related_to_id = :recordid ";
                        $rest = Yii::$app->db->createCommand($reports)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":recordid", $RecordId)
                            ->execute();

                        $reports = "Update product_costing set ownerid = :ownerid where related_to = 51 and related_to_id = :recordid ";
                        $rest = Yii::$app->db->createCommand($reports)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":recordid", $RecordId)
                            ->execute();
                    }
                }
                if (isset($data['special_pricing']) && $data['special_pricing'] == 1) {
                    $data['stage'] = 8; //price pending

                    //assign to c level special pricing
                    //$reports = "select id from user join user2role on user2role.userid = user.id  join role2profile on user2role.roleid = role2profile.roleid where user.deleted = 0 and status = 10 and role2profile.profileid='25' limit 1";
                    //added by deepika on 19 june
                    $reports = "-- First, get the next higher user ID after the last modifier
(
    SELECT u.id
    FROM user u
    JOIN user2role ur ON ur.userid = u.id
    JOIN role2profile rp ON ur.roleid = rp.roleid
    LEFT JOIN modtracker_basic mtb ON mtb.whodid = u.id
    WHERE u.deleted = 0
    AND u.status = 10
    AND rp.profileid = '25'
    AND (
        -- Check for the next user ID after the last modifier
        u.id > (
            SELECT whodid
            FROM modtracker_basic
            WHERE module = '" . ucfirst($this->moduleName) . "' 
            AND status = 2
            ORDER BY changedon DESC
            LIMIT 1
        )
        OR mtb.status IS NULL -- Handle case when there's no last modifier (i.e., no matching record in modtracker_basic)
    )
    ORDER BY u.id ASC
    LIMIT 1
)
UNION ALL
-- If none, wrap around to the lowest ID (excluding the last modifier)
(
    SELECT u.id
    FROM user u
    JOIN user2role ur ON ur.userid = u.id
    JOIN role2profile rp ON ur.roleid = rp.roleid
    LEFT JOIN modtracker_basic mtb ON mtb.whodid = u.id
    WHERE u.deleted = 0
    AND u.status = 10
    AND rp.profileid = '25'
    AND u.id != (
        SELECT whodid
        FROM modtracker_basic
        WHERE module = '" . ucfirst($this->moduleName) . "' 
        AND status = 2
        ORDER BY changedon DESC
        LIMIT 1
    )
    ORDER BY u.id ASC
    LIMIT 1
)
LIMIT 1;
";

                    //    echo $reports;die;
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                        //also assign product and service to pricing team
                        $reports = "Update servicedetail set ownerid = :ownerid where related_to = 51 and related_to_id = :recordid ";
                        $rest = Yii::$app->db->createCommand($reports)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":recordid", $RecordId)
                            ->execute();

                        $reports = "Update product_costing set ownerid = :ownerid where related_to = 51 and related_to_id = :recordid ";
                        $rest = Yii::$app->db->createCommand($reports)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":recordid", $RecordId)
                            ->execute();
                    }
                }
                if (isset($data['submit_for_logistics']) && $data['submit_for_logistics'] == 1) {
                    // $data['stage'] = 6; //logistics pending

                    //assign to pricing team
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H52' limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H52'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H52'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H52'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }
                }
                if (isset($data['costing_done']) && $data['costing_done'] == 1) {
                    $data['stage'] = 10; //pricing done
                    $data['ownerid'] = $data['creatorid'];//assign back to creator

                }
                ///////ceo approval////////////
                if (isset($data['ceo_approval']) && $data['ceo_approval'] == 1) {
                    $data['stage'] = 29; //CEO Price Approval Pending

                    //assign to ceo
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H62' limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H62'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H62'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H62'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }
                }
                //set probability based on stage
                $data['probability'] = $modelleadetail->getprobability($data['stage']);
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `sourcingdeal` where sourcingdeal_id=:sourcingdeal_id")
                    ->bindValue(":sourcingdeal_id", $RecordId)
                    ->queryOne();
                $output = Sourcingdeal::updateAll($data, 'sourcingdeal_id = :sourcingdeal_id', [':sourcingdeal_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $this->SaveSourcingdealTotal($RecordId);
                $modelleadetail->saveToVpReports($RecordId);


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "products") {


                //now update submodules
                $modelleadetail = new Products();
                $data = $_POST["products"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `products` where products_id=:products_id")
                    ->bindValue(":products_id", $RecordId)
                    ->queryOne();
                $output = Products::updateAll($data, 'products_id = :products_id', [':products_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "productdit") {


                //now update submodules
                $modelleadetail = new ProductDit();
                $data = $_POST["product_dit"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `product_dit` where productdit_id=:productdit_id")
                    ->bindValue(":productdit_id", $RecordId)
                    ->queryOne();
                $output = ProductDit::updateAll($data, 'productdit_id = :productdit_id', [':productdit_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "servicemaster") {


                //now update submodules
                $modelleadetail = new Servicemaster();
                $data = $_POST["servicemaster"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `servicemaster` where servicemaster_id=:servicemaster_id")
                    ->bindValue(":servicemaster_id", $RecordId)
                    ->queryOne();
                $output = Servicemaster::updateAll($data, 'Servicemaster_id = :Servicemaster_id', [':Servicemaster_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "vendoraccount") {
                // Update submodules
                $modelleadetail = new VendorAccount();
                $data = $_POST["vendor_account"]; // Array of key-value pairs to update
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }

                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                // added on 17 jan 2025
                // echo $_POST["vendor_account"]['kyc_completed'];die;

                //check if kyc_completed is checked then update acc_status to kyc_completed
                if (isset($_POST["vendor_account"]['kyc_completed']) && $_POST["vendor_account"]['kyc_completed'] == 1) {
                    $data['acc_status'] = '6';
                    //assign to finance manager
                    $sql = "select id from user 
                    join user2role on user2role.userid = user.id
                    where user2role.roleid='H19' and deleted =0 and status=10 limit 1";
                    $userresult = Yii::$app->db->createCommand($sql)
                        ->queryOne();
                    if ($userresult) {
                        $data['ownerid'] = $userresult['id'];
                        $data['kyc_completed_by'] = Yii::$app->user->id;
                    }
                    // echo $data['ownerid'];die;
                }
                //check if submitted_for_kyc is checked then update acc_status to submitted_for_kyc
                if (isset($_POST["vendor_account"]['submitted_for_kyc']) && $_POST["vendor_account"]['submitted_for_kyc'] == 1) {
                    $data['acc_status'] = '5';
                    //assign to compliance manager
                    $sql = "select id from user 
                    join user2role on user2role.userid = user.id
                    where user2role.roleid='H17' and deleted =0 and status=10 limit 1";
                    $userresult = Yii::$app->db->createCommand($sql)
                        ->queryOne();
                    if ($userresult) {
                        $data['ownerid'] = $userresult['id'];
                        //if condition added by ptpatel on date 09-04-25.because when user login by sales account this field is not in post so this show error
                        if (isset($_POST['kyc_submitted_by']))
                            $data['kyc_submitted_by'] = (string) Yii::$app->user->id;
                    }
                }
                //check if credit_stage is No Credit or Approved then update acc_status to Active
                if (isset($_POST["vendor_account"]['credit_stage']) && ($_POST["vendor_account"]['credit_stage'] == 1 || $_POST["vendor_account"]['credit_stage'] == 2)) {
                    $data['acc_status'] = '2';
                    // echo "<br>";

                    //get kyc submitted by assign to submitted by user  
                    //if condition added by ptpatel on date 09-04-25.because when user login by sales account this field is not in post so this show error
                    if (isset($_POST['kyc_submitted_by']))
                        $data['ownerid'] = $data['kyc_submitted_by'];
                    // echo "<br>";
                    // $data['finance_detail_submitted_date']=date("Y-m-d");

                    // $data['finance_detail_submitted_by'] = Yii::$app->user->id;

                }
                //check if credit_stage is Hold then update acc_status to Hold
                if (isset($_POST["vendor_account"]['credit_stage']) && ($_POST["vendor_account"]['credit_stage'] == 3)) {
                    $data['acc_status'] = '3';
                    // echo "<br>";
                    //get kyc submitted by assign to submitted by user
                    // $data['ownerid'] = $data['kyc_submitted_by'];
                    // echo "<br>";
                    // $data['finance_detail_submitted_date']=date("Y-m-d");


                    // $data['finance_detail_submitted_by'] = Yii::$app->user->id;

                }
                //check if recheck_kyc is checked then update acc_status to kyc recheck and reset submitted_for_kyc
                if (isset($_POST["vendor_account"]['recheck_kyc']) && $_POST["vendor_account"]['recheck_kyc'] == 1) {
                    $data['acc_status'] = '7';
                    $data['submitted_for_kyc'] = '0';
                    //get kyc submitted by assign to submitted by user                   
                    $data['ownerid'] = $data['kyc_submitted_by'];
                }
                //check if finance detail completed is checkd then save finnace detail submited date and finance detail submitted by 
                if (isset($_POST["vendor_account"]['finance_detail_completed']) && $_POST["vendor_account"]['finance_detail_completed'] == 1) {
                    $data['finance_detail_submitted_date'] = date("Y-m-d");

                    $data['finance_detail_submitted_by'] = Yii::$app->user->id;
                }

                ///////save attachments////////////////
                $fileInstance = 'vendor_account';
                if (isset($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }

                // Fetch old attributes for audit logging
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("SELECT * FROM `vendor_account` WHERE vendoraccid = :vendoraccid")
                    ->bindValue(":vendoraccid", $RecordId)
                    ->queryOne();

                // Update vendor account
                $output = VendorAccount::updateAll($data, 'vendoraccid = :vendoraccid', [':vendoraccid' => $RecordId]);
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);


                //remove from child table
                Yii::$app->db->createCommand("delete from `vendor_account_orgaisation_section` where vendoraccid=:vendoraccid")
                    ->bindValue(":vendoraccid", $RecordId)
                    ->queryOne();
                //save to child table
                $child = new VendorAccountOrgaisationSection();
                $child->saveVendorAccountOrgaisationSection($RecordId);

                //remove from child table
                Yii::$app->db->createCommand("delete from `vendor_account_oem_manager_detail` where vendoraccid=:vendoraccid")
                    ->bindValue(":vendoraccid", $RecordId)
                    ->queryOne();
                //save to child table
                $child = new VendorAccountOemManagerDetail();
                $child->saveVendorAccountOemManagerDetail($RecordId);

                // If custom table data is provided, update it
                if (!empty($CS)) {
                    // Fetch old records for audit logging
                    $oldCS = Yii::$app->db->createCommand("SELECT * FROM $customtablename WHERE {$this->fieldId} = :id")
                        ->bindValue(':id', $RecordId)
                        ->queryAll();

                    // Update custom table
                    Yii::$app->db->createCommand()
                        ->update($customtablename, $CS, [$this->fieldId => $RecordId])
                        ->execute();

                    // Audit log for custom table update
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "productdetail") {


                //now update submodules
                $modelleadetail = new ProductCosting();
                $data = $_POST["product_costing"]; // Array of key-value pairs to update
                // echo "<br><br><pre>";
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `product_costing` where product_costing_id=:product_costing_id")
                    ->bindValue(":product_costing_id", $RecordId)
                    ->queryOne();
                $output = ProductCosting::updateAll($data, 'product_costing_id = :product_costing_id', [':product_costing_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                //remove from child table
                Yii::$app->db->createCommand("delete from `product_costing_detail` where product_costing_id=:product_costing_id")
                    ->bindValue(":product_costing_id", $RecordId)
                    ->queryOne();
                //save to child table
                $child = new ProductCostingDetail();
                $child->saveProductCostingDetail($RecordId);

                if ($data['related_to'] == 51) {
                    $this->SaveSourcingdealTotal($data['related_to_id']);
                }


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "notes") {

                //now update submodules
                $modelleadetail = new Modnotes();
                $data = $_POST["modnotes"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `modnotes` where modnotesid=:modnotesid")
                    ->bindValue(":modnotesid", $RecordId)
                    ->queryOne();
                // print_r($modelleadetail->oldAttributes['folderid']);die;
                //first save doc
                $file = UploadedFile::getInstanceByName('modnotes[filename]'); // Optional file upload
                // echo $file;
                // print_r($_FILES);die;
                $documents = Yii::$app->request->post('modnotes'); // Text content

                if (!$file || empty($documents)) {
                    // return ['success' => false, 'message' => 'You must provide file and fill required fields'];
                }

                $fileUrl = null;



                // Handle file upload if a file is provided
                if ($file) {
                    // Security: Validate file extension and MIME type
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx'];
                    $allowedMimeTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/pdf',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                    if (!in_array($file->extension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
                        return ['success' => false, 'message' => 'Invalid file type.'];
                    } else {
                        //delete old file
                        $records = \app\models\Attachments::find()
                            ->where(['attachmentsid' => $modelleadetail->oldAttributes['filename']])
                            ->one();
                        $fileid = $records->attachmentsid;
                        // print_r($records);die;
                        $model = new Attachments();
                        $records = $model->find()
                            ->where(['attachmentsid' => $fileid])
                            ->one();
                        $fileName = $records['path'];
                        //print_r($records);die;

                        // Define the base directory for files
                        $filePath = Yii::getAlias('@webroot/' . $fileName);
                        // unlink($filePath);
                        // Check if the file exists before attempting to delete it
                        if (file_exists($filePath)) {
                            // Attempt to delete the file
                            if (unlink($filePath)) {
                                // echo "File removed successfully.";
                            } else {
                                //  echo "Error: Unable to delete the file.";
                            }
                        } else {
                            // echo "File does not exist.";
                        }
                    }

                    // Determine the directory structure based on year, month, and week
                    $year = date('Y');
                    $month = date('m');
                    $week = date('W'); // Week of the year



                    // Define the upload base path
                    $baseUploadPath = Yii::getAlias('@webroot/uploads');
                    $targetPath = $baseUploadPath . "/$year/$month/week_$week/";


                    // Create directories if they do not exist
                    if (!is_dir($targetPath)) {
                        if (!mkdir($targetPath, 0755, true)) {
                            return ['success' => false, 'message' => 'Failed to create upload directories.'];
                        }
                    }

                    // Generate a secure unique file name
                    $fileName = uniqid() . '.' . $file->extension;
                    $filePath = $targetPath . $fileName;
                    $filesavepath = "uploads/$year/$month/week_$week/" . $fileName;



                    //save to attachments
                    $modelatach = new Attachments();
                    $modelatach->name = $file->name;
                    $modelatach->type = $file->type;
                    $modelatach->path = $filesavepath;
                    $modelatach->storedname = $fileName;
                    if ($modelatach->validate()) {
                        if ($modelatach->save()) {
                            $data['filename'] = $modelatach->attachmentsid;
                        }
                    }


                    // Save the file
                    if ($file->saveAs($filePath)) {
                        $fileUrl = Yii::getAlias('@web') . "/uploads/$year/$month/week_$week/" . $fileName;
                    } else {
                        $message = 'Failed to save the file.';
                        die;
                    }
                }
                $output = Documents::updateAll($data, 'modnotesid = :modnotesid', [':modnotesid' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "grn") {
                //now update submodules
                $modelleadetail = new Grn();
                $data = $_POST["grn"]; // Array of key-value pairs to update
                $modelleadetail->attributes = $_POST["grn"];

                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $fileInstance = 'grn'; // Assuming 'grn' is the key in $_FILES
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `grn` where grn_id=:grn_id")
                    ->bindValue(":grn_id", $RecordId)
                    ->queryOne();
                $output = Grn::updateAll($data, 'grn_id = :grn_id', [':grn_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                //remove from child table
                Yii::$app->db->createCommand("delete from `grn_shipped_details` where grn_id=:grn_id")->bindValue(":grn_id", $RecordId)->execute();
                Yii::$app->db->createCommand("delete from `grn_document_details` where grn_id=:grn_id")->bindValue(":grn_id", $RecordId)->execute();
                Yii::$app->db->createCommand("delete from `grn_asset_detail` where grn_id=:grn_id")->bindValue(":grn_id", $RecordId)->execute();
                //save to child table
                $grn_shipped = new GrnShippedDetails();
                $grn_shipped->saveGrnShippedDetails($RecordId);

                $grn_documents = new GrnDocumentDetails();
                $grn_documents->saveGrnDocumentsDetails($RecordId);

                $grn_assets = new GrnAssetDetail();
                $grn_assets->saveGrnAssets($RecordId);

                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                //save to vp reports for grn
                $modelleadetail->save_vp_grn($RecordId);
            } else if ($module === "purchaseorder") {
                //now update submodules
                $modelleadetail = new PurchaseOrder();
                $data = $_POST["purchase_order"]; // Array of key-value pairs to update
                $submit_approval = $data["submit_approval"] ?? null;
                if (isset($data['type']) && is_array($data['type'])) {
                    $data['type'] = implode(",", $data['type']);
                }
                if (empty($submit_approval))
                    $data["submit_approval"] = null;
                $stage = $data["stage"] ?? null;
                if ($submit_approval == 1) {
                    //assign to finance executive
                    /*$sql = "select id from user 
                    join user2role on user2role.userid = user.id
                    where user2role.roleid='H63' and deleted =0 and status=10 limit 1";
                    */
                    // changed logic on 12 july 2025, assign to finance manager 'H19'
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H19'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H19'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H19'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $userresult = Yii::$app->db->createCommand($reports)
                        ->queryOne();
                    if ($userresult) {
                        $data['ownerid'] = $userresult['id'];
                        $data['stage'] = 2;

                        $purchaseinfo = PurchaseOrder::find()->select('purchase_order_no')->where(['purchase_order_id' => $RecordId])->one();
                        $notification = new Notifications();
                        $notification->userid = $data['ownerid'];
                        $notification->message = "Purchase Order " . $purchaseinfo['purchase_order_no'] . "  has been submitted for approval.Please check";
                        $notification->read_status = 0; // Unread notification
                        $notification->display_status = 0;
                        $notification->source_link = Yii::$app->request->baseUrl . "/" . $this->moduleName . "/detail?Record=" . $RecordId;
                        ;
                        $notification->createdtime = date('Y-m-d H:i:s');
                        $notification->modifiedtime = date('Y-m-d H:i:s');
                        if (!$notification->save()) {
                            echo 'save failed';
                            exit;
                        }
                    }
                }
                //custom validation
                $modelleadetail->validateDataIntegrityForPoApproval($stage);
                $fileInstance = 'purchase_order';
                //below line added by ptpatel on date 24-03-25 when edit purchase _order then it will not set
                if (isset($_FILES[$fileInstance])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `purchase_order` where purchase_order_id=:purchase_order_id ")
                    ->bindValue(":purchase_order_id", $RecordId)
                    ->queryOne();
                $output = PurchaseOrder::updateAll($data, 'purchase_order_id=:purchase_order_id', [':purchase_order_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                //remove from child table
                Yii::$app->db->createCommand("DELETE FROM `purchase_order_itemsdetail` WHERE purchase_order_id=:purchase_order_id")
                    ->bindValue(":purchase_order_id", $RecordId)
                    ->execute();
                //save to child table
                $child = new PurchaseOrderItemsdetail();
                $child->savePurchaseOderItems($RecordId);



                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                //save to vp reports for purchase order
                $modelleadetail->saveToVpReports($RecordId);
            } else if ($module === "pickup") {
                //now update submodules
                $modelleadetail = new Pickup();
                $data = $_POST["pickup"]; // Array of key-value pairs to update
                $sourcingdeal_id = $data["opportuity_name"] ?? "";
                $additional_info = $data["additional_info"] ?? "";
                if ($additional_info && is_array($additional_info)) {
                    $additional_info = implode(",", $additional_info);
                    $data["additional_info"] = $additional_info;
                }
                $vehicle_size1 = $data["vehicle_size1"] ?? "";
                if ($vehicle_size1 && is_array($vehicle_size1)) {
                    $vehicle_size1 = implode(",", $vehicle_size1);
                    $data["vehicle_size1"] = $vehicle_size1;
                }
                $modelleadetail->attributes = $data;

                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $current_pickup_stage = $modelleadetail->pickupStageCalc($data["pickup_status"] ?? null);
                $data["pickup_status"] = $current_pickup_stage;
                //echo "stage = $current_pickup_stage";exit;
                //manage assignment also
                if ($current_pickup_stage == 3) {
                    // get logistics manager
                    // $new_user_role = "H52";
                    // $assigned_user = $this->getUserBasedOnRole($new_user_role);
                    // if(!empty($assigned_user)) $data["ownerid"] = $assigned_user;
                    $assigned_user = $data["logistic_user"] ?? null;
                    if (!empty($assigned_user))
                        $data["ownerid"] = $assigned_user;
                } else if ($current_pickup_stage == 4) {
                    // get FE User
                    $selected_fe_user = $data["fe_name"] ?? null;
                    if (!empty($selected_fe_user))
                        $data["ownerid"] = $selected_fe_user;
                }
                // end of managing assignment
                $fileInstance = 'pickup';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `pickup` where pickup_id=:recordid")
                    ->bindValue(":recordid", $RecordId)
                    ->queryOne();
                $old_sourcing_deal_id = $modelleadetail->oldAttributes['opportuity_name'];
                ;
                $output = Pickup::updateAll($data, 'pickup_id = :pickup_id', [':pickup_id' => $RecordId]);

                //save attahments
                // $data = $_POST["pickup_document_details"];
                $fileInstance = 'pickup_document_details';
                // echo "<pre>";
                // print_r($_FILES[$fileInstance]);

                // Loop through the array of files (assuming you're uploading multiple files)
                // Check if the $_FILES array for this field exists and has files uploaded
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                        // Handle each file in this specific file input (attachment)
                        foreach ($files as $fileName => $file) {
                            // If file name is empty, fallback to the hidden file or another backup option
                            if (empty($file)) {
                                echo "No file selected for file pickup document $key.<br>";
                                // Check if there is a hidden file (previously uploaded)
                                if (isset($_POST["pickup_document_details"][$key]['attachment_hidden']) && !empty($_POST["pickup_document_details"][$key]['attachment_hidden'])) {
                                    // Assign the hidden file value to the attachment field
                                    $_POST["pickup_document_details"][$key]['attachment'] = $_POST["pickup_document_details"][$key]['attachment_hidden'];
                                }

                                continue; // Skip this file and move to the next one
                            }
                            // Check for errors in file upload
                            if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                echo "pickup_document_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                continue; // Skip this file and move to the next one
                            }



                            // Create an UploadedFile instance
                            $fileInstanceData = new \yii\web\UploadedFile([
                                'name' => $file,
                                'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                            ]);

                            // Call your method to save the file
                            $result = $this->saveAttachedFiles($fileInstanceData);

                            // Check if file saving was successful
                            if ($result['success']) {
                                $_POST["pickup_document_details"][$key]['attachment'] = $result['fileName'];
                                //delete old file
                                if (isset($_POST["pickup_document_details"][$key]['attachment_hidden'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST["pickup_document_details"][$key]['attachment_hidden']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];
                                    //print_r($records);die;

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                            } else {
                                // Handle the failure, log the message if needed
                                continue; // Continue processing the next file
                            }
                        }
                    }
                } else {
                    // Handle the case where no files are uploaded or the input is empty
                    // below line is commented by  ptpatel on date 24-03-25 reason it append in response though page not refresh properly
                    // echo "No files were uploaded for the attachment.<br>";
                    if (isset($_POST["pickup_document_details"]['attachment_hidden'])) {
                        $_POST["pickup_document_details"]['attachment'] = $_POST["pickup_document_details"]['attachment_hidden'];
                    }
                }



                // print_r($_POST["pickup_document_details"]);
                //die;

                $fileInstance = 'pickup_vehicle_details';
                // echo "<pre>";
                // print_r($_FILES[$fileInstance]);

                // Check if the file instance exists and is an array with file data
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    // Loop through the array of files
                    foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                        // If $files is an array, loop through the nested array for multiple files
                        foreach ($files as $fileName => $file) {
                            // If the file name is empty, check the hidden file or skip it
                            if (empty($file)) {
                                echo "No file selected for file pickup vehicle detail $key.<br>";
                                // Optionally set a hidden file if no file is selected
                                // $_POST["pickup_vehicle_details"][$key]['attach'] = $_POST["pickup_vehicle_details"][$key]['attach_hiddenfile'];
                                // Check if there is a hidden file (previously uploaded)
                                if (isset($_POST["pickup_vehicle_details"][$key]['attach_hidden']) && !empty($_POST["pickup_vehicle_details"][$key]['attach_hidden'])) {
                                    // Assign the hidden file value to the attachment field
                                    $_POST["pickup_vehicle_details"][$key]['attach'] = $_POST["pickup_vehicle_details"][$key]['attach_hidden'];
                                }
                                continue; // Skip this iteration and move to the next file
                            }

                            // Check if there was an upload error
                            if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                echo "pickup_vehicle_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                continue; // Skip this file and move to the next one
                            }

                            // Now proceed to create an UploadedFile instance for valid files
                            $fileInstanceData = new \yii\web\UploadedFile([
                                'name' => $file,
                                'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                            ]);

                            // Call your method to save the uploaded file
                            $result = $this->saveAttachedFiles($fileInstanceData);

                            // Check if file saving was successful
                            if ($result['success']) {
                                echo "File uploaded successfully for file $key. File name: " . $result['fileName'] . "<br>";
                                $_POST["pickup_vehicle_details"][$key]['attach'] = $result['fileName'];
                                //delete old file
                                if (isset($_POST["pickup_document_details"][$key]['attach_hidden'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST["pickup_document_details"][$key]['attach_hidden']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];
                                    //print_r($records);die;

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                            } else {
                                echo "Issue in file saving for file $key. Error message: " . ($result['message'] ?? "Unknown error") . "<br>";
                                continue; // Continue processing the next file
                            }
                        }
                    }
                }
                // else {
                //     // If no files are uploaded or the field doesn't exist
                //     echo "No files were uploaded for the attachment.<br>";
                //     // $_POST["pickup_vehicle_details"]['attach'] =  $_POST["pickup_vehicle_details"]['attach_hiddenfile'];
                //     if (isset($_POST["pickup_vehicle_details"]['attach_hidden'])) {
                //         $_POST["pickup_vehicle_details"]['attach'] = $_POST["pickup_vehicle_details"]['attach_hidden'];
                //     }
                // }

                //start saving vehicle_planning files
                $fileInstance = 'vehicle_planning';

                // Loop through the array of files (assuming you're uploading multiple files)
                // Check if the $_FILES array for this field exists and has files uploaded
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                        // Handle each file in this specific file input (attachment)
                        foreach ($files as $fileName => $file) {
                            // If file name is empty, fallback to the hidden file or another backup option
                            if (empty($file)) {
                                echo "No file selected for file vehicle planning $key.<br>";
                                // Check if there is a hidden file (previously uploaded)
                                if (isset($_POST["vehicle_planning"][$key][$fileName . '_hidden']) && !empty($_POST["vehicle_planning"][$key][$fileName . '_hidden'])) {
                                    // Assign the hidden file value to the attachment field
                                    $_POST["vehicle_planning"][$key][$fileName] = $_POST["vehicle_planning"][$key][$fileName . '_hidden'];
                                }

                                continue; // Skip this file and move to the next one
                            }
                            // Check for errors in file upload
                            if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                echo "vehicle_planning File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                continue; // Skip this file and move to the next one
                            }

                            // Create an UploadedFile instance
                            $fileInstanceData = new \yii\web\UploadedFile([
                                'name' => $file,
                                'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                            ]);

                            // Call your method to save the file
                            $result = $this->saveAttachedFiles($fileInstanceData);

                            // Check if file saving was successful
                            if ($result['success']) {
                                $_POST["vehicle_planning"][$key][$fileName] = $result['fileName'];
                                //delete old file
                                if (isset($_POST["vehicle_planning"][$key][$fileName . '_hidden'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST["vehicle_planning"][$key][$fileName . '_hidden']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];
                                    //print_r($records);die;

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                            } else {
                                // Handle the failure, log the message if needed
                                continue; // Continue processing the next file
                            }
                        }
                    }
                }
                // else {
                //     // Handle the case where no files are uploaded or the input is empty
                //     if (isset($_POST["vehicle_planning"][$fileName.'_hidden'])) {
                //         $_POST["vehicle_planning"][$fileName] = $_POST["vehicle_planning"][$fileName.'_hidden']??"";
                //     }
                // }
                // end of saving vehicle_planning files


                //start saving details_packing_list files
                $fileInstance = 'details_packing_list';

                // Loop through the array of files (assuming you're uploading multiple files)
                // Check if the $_FILES array for this field exists and has files uploaded
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                        // Handle each file in this specific file input (attachment)
                        foreach ($files as $fileName => $file) {
                            // If file name is empty, fallback to the hidden file or another backup option
                            if (empty($file)) {
                                echo "No file selected for file details packing list $key.<br>";
                                // Check if there is a hidden file (previously uploaded)
                                if (isset($_POST["details_packing_list"][$key][$fileName . '_hidden']) && !empty($_POST["details_packing_list"][$key][$fileName . '_hidden'])) {
                                    // Assign the hidden file value to the attachment field
                                    $_POST["details_packing_list"][$key][$fileName] = $_POST["details_packing_list"][$key][$fileName . '_hidden'];
                                }

                                continue; // Skip this file and move to the next one
                            }
                            // Check for errors in file upload
                            if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                echo "details_packing_list File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                continue; // Skip this file and move to the next one
                            }

                            // Create an UploadedFile instance
                            $fileInstanceData = new \yii\web\UploadedFile([
                                'name' => $file,
                                'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                            ]);

                            // Call your method to save the file
                            $result = $this->saveAttachedFiles($fileInstanceData);

                            // Check if file saving was successful
                            if ($result['success']) {
                                $_POST["details_packing_list"][$key][$fileName] = $result['fileName'];
                                //delete old file
                                if (isset($_POST["details_packing_list"][$key][$fileName . '_hidden'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST["details_packing_list"][$key][$fileName . '_hidden']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];
                                    //print_r($records);die;

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                            } else {
                                // Handle the failure, log the message if needed
                                continue; // Continue processing the next file
                            }
                        }
                    }
                }
                // else {
                //     // Handle the case where no files are uploaded or the input is empty
                //     if (isset($_POST["details_packing_list"][$fileName.'_hidden'])) {
                //         $_POST["details_packing_list"][$fileName] = $_POST["details_packing_list"][$fileName.'_hidden']??"";
                //     }
                // }

                //end saving details_packing_list files

                // start processing child table of packing_material
                if (isset($_POST['packing_material'])) {
                    Yii::$app->db->createCommand("delete from `packing_material` where pickup_id=:pickup_id")
                        ->bindValue(":pickup_id", $RecordId)
                        ->queryOne();

                    $packing_materials = new PackingMaterial();
                    $packing_materials->savePackingMaterials($RecordId);
                }
                // end processing child table of packing_material

                // start processing child table of vehicle_planning
                if (isset($_POST['vehicle_planning'])) {
                    Yii::$app->db->createCommand("delete from `vehicle_planning` where pickup_id=:pickup_id")
                        ->bindValue(":pickup_id", $RecordId)
                        ->queryOne();

                    $vehicle_planning = new VehiclePlanning();
                    $vehicle_planning->saveVehiclePlanning($RecordId);
                }
                // end processing child table of vehicle_planning
                // start processing child table of shipped_details
                if (isset($_POST['shipped_details'])) {
                    Yii::$app->db->createCommand("delete from `shipped_details` where pickup_id=:pickup_id")
                        ->bindValue(":pickup_id", $RecordId)
                        ->queryOne();

                    $shipped_details = new ShippedDetails();
                    $shipped_details->saveShippedDetails($RecordId);
                }
                // end processing child table of shipped_details

                // start processing child table of details_packing_list
                if (isset($_POST['details_packing_list'])) {
                    Yii::$app->db->createCommand("delete from `details_packing_list` where pickup_id=:pickup_id")
                        ->bindValue(":pickup_id", $RecordId)
                        ->queryOne();

                    $details_packing_list = new DetailsPackingList();
                    $details_packing_list->saveDetailsPackingList($RecordId);
                }
                // end processing child table of details_packing_list
                //remove from child table
                if (isset($_POST['pickup_document_details'])) {
                    Yii::$app->db->createCommand("delete from `pickup_document_details` where pickup_id=:pickup_id")
                        ->bindValue(":pickup_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $child1 = new PickupDocumentDetails();
                    $child1->savePickupDocumentDetails($RecordId);
                }
                // print_r($_POST);die;
                //remove from child table
                if (isset($_POST['pickup_manual_asset_detail'])) {
                    Yii::$app->db->createCommand("delete from `pickup_manual_asset_detail` where pickup_id=:pickup_id")
                        ->bindValue(":pickup_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $child1 = new PickupManualAssetDetail();
                    $child1->savePickupManualAssetDetail($RecordId);
                }




                if (isset($_POST['pickup_asset_detail'])) {
                    Yii::$app->db->createCommand("delete from `pickup_asset_detail` where pickup_id=:pickup_id")
                        ->bindValue(":pickup_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $child2 = new PickupAssetDetail();
                    $child2->savePickupAssetDetail($RecordId);
                }

                if (isset($_POST['pickup_vehicle_details'])) {
                    Yii::$app->db->createCommand("delete from `pickup_vehicle_details` where pickup_id=:pickup_id")
                        ->bindValue(":pickup_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $child3 = new PickupVehicleDetails();
                    $child3->savePickupVehicleDetails($RecordId);
                }
                //inspection related data processing for pickup
                $modelleadetail->inspectionRelatedDataEdit($sourcingdeal_id, $old_sourcing_deal_id, $RecordId);

                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);

                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "iqclaptop") {
                $modelleadetail = new IqcLaptop();
                $data = $_POST["iqc_laptop"]; // Array of key-value pairs to update
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `iqc_laptop` where iqclaptop_id=:iqclaptop_id")
                    ->bindValue(":iqclaptop_id", $RecordId)
                    ->queryOne();
                $output = IqcLaptop::updateAll($data, 'iqclaptop_id = :iqclaptop_id', [':iqclaptop_id' => $RecordId]);

                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "warehouse") {


                //now update submodules
                $modelleadetail = new Warehouse();
                $data = $_POST["warehouse"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `warehouse` where warehouse_id=:warehouse_id")
                    ->bindValue(":warehouse_id", $RecordId)
                    ->queryOne();
                $output = Warehouse::updateAll($data, 'warehouse_id = :warehouse_id', [':warehouse_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "vendorlocations") {

                //now update submodules
                $modelleadetail = new VendorLocations();
                $data = $_POST["vendor_locations"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `vendor_locations` where vendorloc_id=:vendorloc_id")
                    ->bindValue(":vendorloc_id", $RecordId)
                    ->queryOne();

                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                // echo "<pre>";
                //  print_r($data);die;
                $output = VendorLocations::updateAll($data, 'vendorloc_id = :vendorloc_id', [':vendorloc_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "iqcdesktop") {
                $modelleadetail = new IqcDesktop();
                $data = $_POST["iqc_desktop"]; // Array of key-value pairs to update
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `iqc_desktop` where iqcdesktop_id=:iqcdesktop_id")
                    ->bindValue(":iqcdesktop_id", $RecordId)
                    ->queryOne();
                $output = IqcDesktop::updateAll($data, 'iqcdesktop_id = :iqcdesktop_id', [':iqcdesktop_id' => $RecordId]);

                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);

                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "iqctft") {
                $modelleadetail = new IqcTft();
                $data = $_POST["iqc_tft"]; // Array of key-value pairs to update
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `iqc_tft` where iqctft_id=:iqctft_id")
                    ->bindValue(":iqctft_id", $RecordId)
                    ->queryOne();
                $output = IqcTft::updateAll($data, 'iqctft_id = :iqctft_id', [':iqctft_id' => $RecordId]);

                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);

                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "iqclaptopgrade") {
                $modelleadetail = new IqcLaptopGrade();
                $data = $_POST["iqc_laptop_grade"]; // Array of key-value pairs to update
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `iqc_laptop_grade` where laptop_grade_id=:laptop_grade_id")
                    ->bindValue(":laptop_grade_id", $RecordId)
                    ->queryOne();
                $output = IqcLaptopGrade::updateAll($data, 'laptop_grade_id = :laptop_grade_id', [':laptop_grade_id' => $RecordId]);

                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);

                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "contacts") {
                $data = $_POST["contacts"];
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                //code added by ptpatel start from here on date 03-09-2025 for ERP finding point no 403 - While creating the contact we are getting option to create User name and password but for existing contact we are not getting any option to create user name password for customer portal.
                if($data['password'] == "") 
                    $data['password'] = null;
                if($data['username'] == '')
                    $data['username'] = null;
                $old_passowrd = Yii::$app->db->createCommand("select password from `contacts` where contacts_id=:contacts_id")
                    ->bindValue(":contacts_id", $RecordId)
                    ->queryOne();

                if($old_passowrd['password'] == "" && $data['password'] != ''){
                    $data['password'] =  Yii::$app->security->generatePasswordHash($data['password']);
                }
                //code added by ptpatel end here on date 03-09-2025 for ERP finding point no 403 - While creating the contact we are getting option to create User name and password but for existing contact we are not getting any option to create user name password for customer portal.
                //now save submodules
                $modelleadetail = new Contacts();
                $modelleadetail->attributes = $data;

                // print_r($data);
                // die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `contacts` where contacts_id=:contacts_id")
                    ->bindValue(":contacts_id", $RecordId)
                    ->queryOne();
                $output = Contacts::updateAll($data, 'contacts_id = :contacts_id', [':contacts_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "iqcdesktopgrade") {
                $modelleadetail = new IqcDesktopGrade();
                $data = $_POST["iqc_desktop_grade"]; // Array of key-value pairs to update
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `iqc_desktop_grade` where desktop_grade_id=:desktop_grade_id")
                    ->bindValue(":desktop_grade_id", $RecordId)
                    ->queryOne();
                $output = IqcDesktopGrade::updateAll($data, 'desktop_grade_id = :desktop_grade_id', [':desktop_grade_id' => $RecordId]);

                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);

                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "user") {
                //now update submodules
                $modelleadetail = new User();
                $data = $_POST["user"]; // Array of key-value pairs to update

                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;


                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `user` where id=:id")
                    ->bindValue(":id", $RecordId)
                    ->queryOne();
                // Handle profilepic upload
                $imageFile = UploadedFile::getInstanceByName('user[profilepic]');
                if ($imageFile) {
                    // Generate unique filename to avoid overwriting
                    $uniqueFileName = uniqid('profile_') . '.' . $imageFile->extension;

                    // Define the upload path
                    $uploadDir = Yii::getAlias('@webroot/thememain/profile/');
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true); // Create directory if not exists
                    }

                    $filePath = $uploadDir . $uniqueFileName;

                    // Save the file and update the profilepic field
                    if ($imageFile->saveAs($filePath)) {
                        // Set the new profilepic path
                        $data['profilepic'] = 'thememain/profile/' . $uniqueFileName;

                        // Delete the old profile picture file if it exists
                        if (!empty($modelleadetail->oldAttributes['profilepic'])) {
                            $oldFilePath = Yii::getAlias('@webroot/') . $modelleadetail->oldAttributes['profilepic'];
                            if (file_exists($oldFilePath)) {
                                unlink($oldFilePath); // Delete the old file
                            }
                        }
                    } else {
                        Yii::error('Failed to save uploaded file.');
                        return false;
                    }
                }

                $output = User::updateAll($data, 'id = :id', [':id' => $RecordId]);
                //code added for update target of user
                if (isset($_POST['user_targets'])) {
                    $delete_old_record = UserTargets::deleteAll(['userid' => $RecordId]);
                    // if ($delete_old_record) {
                    $user_target = new UserTargets();
                    $user_target->saveUserTarget($_POST['user_targets'], $RecordId);
                    // }
                }
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

                // Handle role update in user2role table
                if (!empty($data['role'])) {
                    // Delete existing roles for the user
                    Yii::$app->db->createCommand("DELETE FROM user2role WHERE userid = :userid")
                        ->bindValue(':userid', $RecordId)
                        ->execute();

                    // Insert new role
                    Yii::$app->db->createCommand()->insert('user2role', [
                        'userid' => $RecordId,
                        'roleid' => $data['role'],
                    ])->execute();
                }
            } else if ($module === "drilling") {
                //now update submodules
                $modelleadetail = new Drilling();
                $data = $_POST["drilling"]; // Array of key-value pairs to update
                $fileInstance = 'drilling';
                $hdd_completed_count = 0;
                $wiping_status = $modelleadetail->drillingStageCalc($RecordId);
                $data["drilling_status"] = $wiping_status;
                if ($wiping_status == 3 && !empty($data["fe_name"])) {
                    $data["ownerid"] = $data["fe_name"];
                }

                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `drilling` where drilling_id=:drilling_id")
                    ->bindValue(":drilling_id", $RecordId)
                    ->queryOne();
                // print_r($data);die;
                $output = Drilling::updateAll($data, 'drilling_id = :drilling_id', [':drilling_id' => $RecordId]);
                $fileInstance = 'drilling_asset_details';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                        // Handle each file in this specific file input (attachment)
                        foreach ($files as $fileName => $file) {
                            // If file name is empty, fallback to the hidden file or another backup option
                            if (empty($file)) {
                                echo "No file selected for file $key.<br>";
                                // Check if there is a hidden file (previously uploaded)
                                if (isset($_POST["drilling_asset_details"][$key][$fileName . '_hidden']) && !empty($_POST["drilling_asset_details"][$key][$fileName . '_hidden'])) {
                                    // Assign the hidden file value to the attachment field
                                    $_POST["drilling_asset_details"][$key][$fileName] = $_POST["drilling_asset_details"][$key][$fileName . '_hidden'];
                                }

                                continue; // Skip this file and move to the next one
                            }
                            // Check for errors in file upload
                            if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                echo "drilling_asset_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                continue; // Skip this file and move to the next one
                            }

                            // Create an UploadedFile instance
                            $fileInstanceData = new \yii\web\UploadedFile([
                                'name' => $file,
                                'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                            ]);

                            // Call your method to save the file
                            $result = $this->saveAttachedFiles($fileInstanceData);

                            // Check if file saving was successful
                            if ($result['success']) {
                                $_POST["drilling_asset_details"][$key][$fileName] = $result['fileName'];
                                //delete old file
                                if (isset($_POST["drilling_asset_details"][$key][$fileName . '_hidden'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST["drilling_asset_details"][$key][$fileName . '_hidden']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];
                                    //print_r($records);die;

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                            } else {
                                // Handle the failure, log the message if needed
                                continue; // Continue processing the next file
                            }
                        }
                    }
                }

                if (isset($_POST['drilling_asset_details'])) {
                    Yii::$app->db->createCommand("delete from `drilling_asset_details` where drilling_id=:drilling_id")
                        ->bindValue(":drilling_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $drilling_asset = new DrillingAssetDetails();
                    $hdd_completed_count = $drilling_asset->saveDrillingAssets($RecordId);
                }
                Drilling::updateAll(['hdd_completed' => $hdd_completed_count], 'drilling_id = :drilling_id', [':drilling_id' => $RecordId]);
                $data["hdd_completed"] = $hdd_completed_count;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                //save to vp reports for drilling
                $modelleadetail->saveToVpReports($RecordId);
            } else if ($module == "convertlead") {
                $tabs = 1;
                // echo "<pre>";
                // print_r($_POST);
                // die;
                //delete old first
                //delete opportunity / sourcing deal/ account /contact where temp =0 and lead = this lead
                //get aacount
                $acc = Leadinformation::find()->select('vendor')->where(['leadid' => $RecordId])->one();
                if (isset($acc['vendor'])) {
                    //update account
                    $sql_c = "delete from `vendor_account` where vendoraccid = :vendoraccid and is_temp = 1";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":vendoraccid", $acc['vendor'])->execute();
                }
                //get opportunity 
                $opp = Opportunity::find()->select('opportunity_id')->where(['leadid' => $RecordId])->one();
                // echo "<pre>";print_r($opp);die;
                if (isset($opp['opportunity_id'])) {

                    //get contact from contact role
                    $sql_c = "select contacts_id from opportunity_contact_role where opportunity_id = :opportunity_id";
                    $res = Yii::$app->db->createCommand($sql_c)->bindValue(":opportunity_id", $opp['opportunity_id'])->queryAll();
                    foreach ($res as $key => $value) {
                        $sql_c = "delete from `contacts`  where contacts_id = :contacts_id and is_temp=1";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();

                        $sql_c = "delete from `opportunity_contact_role` where contacts_id = :contacts_id and is_temp=1";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();
                    }
                    //delete opportunity
                    $sql_c = "delete from `opportunity` where leadid = :leadid";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":leadid", $RecordId)->execute();
                }
                //get sourcing_deal 
                $opp = Sourcingdeal::find()->select('sourcingdeal_id')->where(['leadid' => $RecordId])->one();
                if (isset($opp['sourcingdeal_id'])) {

                    //get contact from contact role
                    $sql_c = "select contacts_id from sourcingdeal_contact_role where sourcingdeal_id = :sourcingdeal_id";
                    $res = Yii::$app->db->createCommand($sql_c)->bindValue(":sourcingdeal_id", $opp['sourcingdeal_id'])->queryAll();
                    foreach ($res as $key => $value) {
                        $sql_c = "delete from `contacts`  where contacts_id = :contacts_id and is_temp=1";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();

                        $sql_c = "delete from `sourcingdeal_contact_role` where contacts_id = :contacts_id and is_temp=1";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();
                    }
                    //delete sourcingdeal
                    $sql_c = "delete from `sourcingdeal` where leadid = :leadid";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":leadid", $RecordId)->execute();
                }
                //end delete
                //check if account_type==1
                $vendor_account_name = $_POST['vendor_account_name'];
                if (isset($_POST['account_type']) && $_POST['account_type'] == 2) { //choose from account
                    $vendor_account_name = $_POST['vendor_name'];
                }
                $contacts_id = ''; //$_POST['contacts_id'];
                if (isset($_POST['create_contact']) && $_POST['create_contact'] == 2) {
                    //choose fro contact
                    $contacts_id = $_POST['opportunity']['contact_name'];
                }
                // echo $contacts_id;die;
                if (isset($_POST['account_type']) && $_POST['account_type'] == 1 && !empty($_POST['vendor_account']['acc_name'])) {
                    //create vendor
                    $modelvendoraccount = new VendorAccount();
                    $data = $_POST["vendor_account"];
                    foreach ($data as $key => $val) {
                        if (is_array($val)) {
                            $data[$key] = implode(",", $val);
                        }
                    }
                    $data['creatorid'] = $_POST['creatorid'];
                    $data['ownerid'] = $_POST['ownerid'];
                    $data['modifiedby'] = $_POST['modifiedby'];
                    $data['createdtime'] = date('Y-m-d H:i:s');
                    $data['modifiedtime'] = $data['createdtime'];
                    // $data['account_category'] = '1'; //vendor
                    $data['acc_status'] = '1'; //in process
                    $data['phone'] = $_POST['mobile'];
                    $data['is_temp'] = 1;


                    // set account code
                    $code = '';
                    if (in_array(2, $_POST['vendor_account']['account_category']))
                        $code .= "C";
                    if (in_array(1, $_POST['vendor_account']['account_category']))
                        $code .= "V";
                    if (in_array(3, $_POST['vendor_account']['account_category']))
                        $code .= "P";
                    $data['cust_code'] = $this->getaccountcode($code);
                    //end set account code
                    $modelvendoraccount->attributes = $data;
                    //end set account code


                    // Ensure vendor_no is handled correctly
                    // if ($autoField = $this->checkAutoNo()) {
                    //     $modelvendoraccount->{$autoField} = $this->getAutoNo($tabs);
                    // }
                    if ($autoField = $this->checkAutoNoModule('vendor_account')) {
                        // echo $autoField;die;
                        // print_r($model);die;
                        $modelvendoraccount['vendor_no'] = $this->getAutoNoModule("vendor_account");
                    }

                    // Validate the model
                    if ($modelvendoraccount->validate()) {
                        // If valid, save the vendor account details
                        if ($modelvendoraccount->save()) {

                            $vendor_account_name = (string) $modelvendoraccount->vendoraccid;
                            // Audit log for vendor account
                            $modlog->auditlog($modelvendoraccount->oldAttributes, $modelvendoraccount->attributes, 'vendoraccount', $modelvendoraccount->vendoraccid, 0, Yii::$app->user->id);

                            // You can add additional functionality like updating CRM sequence or other custom logic if necessary
                            $this->updateCRMSequence($module, $modelvendoraccount->vendoraccid);

                            // If custom fields are provided, save them
                            if (!empty($CS)) {
                                $CS = array_merge($CS, ["vendoraccid" => $modelvendoraccount->vendoraccid]);
                                $command = Yii::$app->db->createCommand()->insert($customtablename, $CS);
                                $command->execute();
                                // Audit log for custom fields
                                $modlog->auditlog($oldAttributes = '', $CS, $this->moduleName, $modelvendoraccount->vendoraccid, $auditstatus, Yii::$app->user->id);
                            }

                            if ($autoField = $this->checkAutoNoModule("vendor_account"))
                                $this->setAutoNoModule("vendoraccount");


                            // return true; // Indicate success
                        } else {
                            // If save fails, print errors and log the failure
                            print_r($modelvendoraccount->getErrors());
                            die;
                            Yii::error("Failed to save vendor account: " . json_encode($modelvendoraccount->getErrors()));
                            return false; // Indicate failure
                        }
                    } else {
                        // If validation fails, print errors and log the failure
                        print_r($modelvendoraccount->getErrors());
                        die;
                        Yii::error("Validation errors for vendor account: " . json_encode($modelvendoraccount->getErrors()));
                        return false; // Indicate validation failure
                    }
                }
                // echo $vendor_account_name;die;


                //create/update contact

                if (empty($contacts_id) && isset($_POST['create_contact']) && $_POST['create_contact'] == 1) {
                    //create contact
                    $modelleadetail = new Contacts();
                    $data = $_POST["contacts"];
                    $data['creatorid'] = $_POST['creatorid'];
                    $data['ownerid'] = $_POST['ownerid'];
                    $data['modifiedby'] = $_POST['modifiedby'];
                    $data['createdtime'] = date('Y-m-d H:i:s');
                    $data['modifiedtime'] = $data['createdtime'];
                    // $data['contact_role'] = '6'; //requester
                    $data['contact_role'] = ''; //requester
                    $data['status'] = '2'; //actve
                    $data['mobile'] = $_POST['mobile'];
                    $data['vendor_account_name'] = $vendor_account_name;
                    $data['is_temp'] = 1;
                    $data['department'] = $_POST['departments'];
                    //added on date 14-06-25
                    $data['designation'] = $_POST['designation'];
                    $data['industry'] = $_POST['industry'];


                    $modelleadetail->attributes = $data;
                    //  echo "<pre>";
                    // print_r($modelleadetail->attributes);die;
                    if ($autoField = $this->checkAutoNoModule('contacts')) {
                        // echo $autoField;die;
                        // print_r($model);die;
                        $modelleadetail['contact_no'] = $this->getAutoNoModule("contacts");
                    }
                    // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                    if ($modelleadetail->validate()) {
                        //audit log

                        if ($modelleadetail->save()) {
                            $contacts_id = $modelleadetail->contacts_id;
                            $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, 'contacts', $modelleadetail->contacts_id, '0', Yii::$app->user->id);
                            $this->updateCRMSequence($module, $modelleadetail->contacts_id);

                            if ($autoField = $this->checkAutoNoModule("contacts"))
                                $this->setAutoNoModule("contacts");
                        } else {
                            print_r($modelleadetail->getErrors());

                            die();

                            Yii::error(
                                "Failed to save model: " .
                                json_encode(
                                    $modelleadetail->getErrors()
                                )
                            );
                            return false; // Indicate failure
                        }
                    } else {

                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Validation errors: " .
                            json_encode($modelleadetail->getErrors())
                        );
                        return false; // Indicate validation failure
                    }
                }
                // else {
                //     //updatecontact
                //     $modelleadetail = new Contacts();
                //     $contacts_id = $_POST['contacts_id'];
                //     $data = array();
                //     $data['first_name'] = $_POST["contacts"]['first_name']; // Array of key-value pairs to update
                //     $data['last_name'] = $_POST["contacts"]['last_name']; // Array of key-value pairs to update
                //     // print_r($data);die;
                //     $data['modifiedtime'] = date('Y-m-d H:i:s');
                //     $data['modifiedby'] = $uid;
                //     $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `contacts` where contacts_id=:contacts_id")
                //         ->bindValue(":contacts_id", $RecordId)
                //         ->queryOne();
                //     $output = Contacts::updateAll($data, 'contacts_id = :contacts_id', [':contacts_id' => $_POST['contacts_id']]);
                //     // print_r($modelleadetail->oldAttributes);die;
                //     $modlog->auditlog($modelleadetail->oldAttributes, $data, 'contacts', $_POST['contacts_id'], $auditstatus, Yii::$app->user->id);
                // }
                if (isset($_POST['sourcingdeal'])) {
                    //create sourcing deal
                    $modelleadetail = new Sourcingdeal();
                    $data = $_POST["sourcingdeal"];
                    $data['creatorid'] = $_POST['creatorid'];
                    $data['ownerid'] = $_POST['ownerid'];
                    $data['modifiedby'] = $_POST['modifiedby'];
                    $data['createdtime'] = date('Y-m-d H:i:s');
                    $data['modifiedtime'] = $data['createdtime'];
                    $data['contact_name'] = (string) $contacts_id;
                    $data['vendor_account_name'] = $vendor_account_name;
                    $data['contact_mobile'] = $_POST['mobile'];
                    $data['leadid'] = $RecordId; //save leaad reference in sourcingdeal
                    $data['is_temp'] = 1;
                    $data['stage'] = 1;
                    $data['exchange_rate'] = $_POST['exchange_rate'];
                    $data['currency'] = $_POST['currency'];

                    //added on date 14-06-25
                    $data['department'] = $_POST['departments'];
                    $data['designation'] = $_POST['designation'];
                    $modelleadetail->attributes = $data;
                    //  echo "<pre>";
                    // print_r($modelleadetail->attributes);die;
                    if ($autoField = $this->checkAutoNoModule('sourcingdeal')) {
                        // echo $autoField;die;
                        // print_r($model);die;
                        $modelleadetail['sourcingdeal_no'] = $this->getAutoNoModule('sourcingdeal');
                    }
                    // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                    if ($modelleadetail->validate()) {
                        //audit log
                        if ($modelleadetail->save()) {
                            $sourcingdeal_id = $modelleadetail->sourcingdeal_id;
                            $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, 'sourcingdeal', $modelleadetail->sourcingdeal_id, '0', Yii::$app->user->id);
                            $this->updateCRMSequence($module, $modelleadetail->sourcingdeal_id);

                            if ($autoField = $this->checkAutoNoModule('sourcingdeal'))
                                $this->setAutoNoModule('sourcingdeal');
                        } else {
                            print_r($modelleadetail->getErrors());

                            die();

                            Yii::error(
                                "Failed to save model: " .
                                json_encode(
                                    $modelleadetail->getErrors()
                                )
                            );
                            return false; // Indicate failure
                        }
                    } else {

                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Validation errors: " .
                            json_encode($modelleadetail->getErrors())
                        );
                        return false; // Indicate validation failure
                    }
                } else {
                    //now create opportuity
                    $modelleadetail = new Opportunity();
                    $data = $_POST["opportunity"];
                    $data['creatorid'] = $_POST['creatorid'];
                    $data['ownerid'] = $_POST['ownerid'];
                    $data['modifiedby'] = $_POST['modifiedby'];
                    $data['createdtime'] = date('Y-m-d H:i:s');
                    $data['modifiedtime'] = $data['createdtime'];
                    $data['requester_customer_name'] = (string) $contacts_id;
                    $data['requester_mobile'] = $_POST['mobile'];
                    $data['vendor_account_name'] = $vendor_account_name;
                    $data['leadid'] = $RecordId; //save leaad reference in opportunity
                    $data['is_temp'] = 1;


                    $modelleadetail->attributes = $data;
                    //  echo "<pre>";
                    // print_r($modelleadetail->attributes);die;
                    if ($autoField = $this->checkAutoNoModule('opportunity')) {
                        // echo $autoField;die;
                        // print_r($model);die;
                        $modelleadetail['opportunity_no'] = $this->getAutoNoModule('opportunity');
                    }
                    // $modelleadetail->leadname = $modelleadetail->firstname.$modelleadetail->lastname;
                    if ($modelleadetail->validate()) {
                        //audit log

                        if ($modelleadetail->save()) {
                            $opportunity_id = $modelleadetail->opportunity_id;
                            $modlog->auditlog($modelleadetail->oldAttributes, $modelleadetail->attributes, 'opportunities', $modelleadetail->opportunity_id, '0', Yii::$app->user->id);
                            $this->updateCRMSequence($module, $modelleadetail->opportunity_id);

                            if ($autoField = $this->checkAutoNoModule('opportunity'))
                                $this->setAutoNoModule('opportunity');


                        } else {
                            print_r($modelleadetail->getErrors());

                            die();

                            Yii::error(
                                "Failed to save model: " .
                                json_encode(
                                    $modelleadetail->getErrors()
                                )
                            );
                            return false; // Indicate failure
                        }
                    } else {

                        print_r($modelleadetail->getErrors());

                        die();

                        Yii::error(
                            "Validation errors: " .
                            json_encode($modelleadetail->getErrors())
                        );
                        return false; // Indicate validation failure
                    }
                }
                //save contact role
                if (isset($contacts_id) && !empty($contacts_id)) {
                    if (isset($sourcingdeal_id) && !empty($sourcingdeal_id)) {
                        //save in sourcing deal contact role
                        $sql = "INSERT INTO `sourcingdeal_contact_role`(`contacts_id`, `contact_role`, `sourcingdeal_id`,`is_temp`, `creatorid`, `createdtime`) VALUES (:contacts_id,:contact_role,:sourcingdeal_id,1,:creatorid,:createdtime)";
                        Yii::$app->db->createCommand($sql)
                            ->bindValue(":contacts_id", $contacts_id)
                            // ->bindValue(":contact_role", 6) //requestor
                            ->bindValue(":contact_role", '') //requestor
                            ->bindValue(":sourcingdeal_id", $sourcingdeal_id)
                            ->bindValue(":creatorid", Yii::$app->user->id)
                            ->bindValue(":createdtime", date("Y-m-d H:i:s"))
                            ->execute();
                        $typeid = $sourcingdeal_id;
                    }
                    if (isset($opportunity_id) && !empty($opportunity_id)) {
                        //save in opportunity contact role
                        $sql = "INSERT INTO `opportunity_contact_role`(`contacts_id`, `contact_role`, `opportunity_id`,`is_temp`, `creatorid`, `createdtime`) VALUES (:contacts_id,:contact_role,:opportunity_id,1,:creatorid,:createdtime)";
                        Yii::$app->db->createCommand($sql)
                            ->bindValue(":contacts_id", $contacts_id)
                            // ->bindValue(":contact_role", 6) //requestor
                            ->bindValue(":contact_role", '') //requestor
                            ->bindValue(":opportunity_id", $opportunity_id)
                            ->bindValue(":creatorid", Yii::$app->user->id)
                            ->bindValue(":createdtime", date("Y-m-d H:i:s"))
                            ->execute();
                        $typeid = $opportunity_id;

                    }
                }
                //now update lead
                //now update submodules
                $modelleadetail = new Leadinformation();
                $data = array(); // Array of key-value pairs to update
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $data['converted'] = 1;
                $data['leadstatus'] = 6;
                $data['vendor'] = $vendor_account_name;
                //added on 7 fe 2025 during conversion the status will be pending for approval

                $data['leadstatus'] = '4';
                //assign to reports to user
                $reports = User::find()->select('reports_to')->where(['id' => Yii::$app->user->id])->one();

                // print_r($reports);die;
                $reportsto = $reports['reports_to'];
                if (!empty($reportsto)) {
                    $data['ownerid'] = $reportsto;
                    $leadnoinfo = Leadinformation::find()->select('lead_no')->where(['leadid' => $RecordId])->one();
                    $notification = new Notifications();
                    $notification->userid = $data['ownerid'];
                    $notification->message = "Lead No " . $leadnoinfo['lead_no'] . " is pending for approval.Please check";
                    $notification->read_status = 0; // Unread notification
                    $notification->display_status = 0;
                    $notification->source_link = Yii::$app->request->baseUrl . "/" . $this->moduleName . "/detail?Record=" . $RecordId;
                    ;
                    $notification->createdtime = date('Y-m-d H:i:s');
                    $notification->modifiedtime = date('Y-m-d H:i:s');
                    if (!$notification->save()) {
                        echo 'save failed';
                        exit;
                    }

                    // echo 'helo' . $data['ownerid'];
                    // exit;
                }
                //  echo $_POST['ownerid'];die;

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `leadinformation` where leadid=:leadid")
                    ->bindValue(":leadid", $RecordId)
                    ->queryOne();
                $output = Leadinformation::updateAll($data, 'leadid = :leadid', [':leadid' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, '4', Yii::$app->user->id);

                //new added on 23may2025 by deepika for saving multiple contacts
                if (isset($_POST['sourcingdeal']))
                    $type = "sd";
                else if (isset($_POST['opportunity']))
                    $type = "op";
                // echo $type;die;
                $modelleadetail->savemultiplecontacts($RecordId, $type, $vendor_account_name, $typeid);
                // die;

            } else if ($module === "quotes") {

                                // echo "<pre>";print_r($_POST);die;
                //now update submodules
                $modelleadetail = new Quotes();
                $data = $_POST["quotes"]; // Array of key-value pairs to update
                // as per client change on date 21-06-25 added by ptpatel
                if (isset($data["po_type"]) && is_array($data["po_type"])) {
                    $data['po_type'] = implode(",", $data['po_type']);
                }

                ///////change sourcing deal sages based on quotes stages/////
                if ($_POST['quotes']['related_to'] == 51) {



                    $related_to_id = $_POST['quotes']['related_to_id'];
                    $quotestage = $_POST['quotes']['quote_stage'];
                    $srcstage = 0;

                    if ($quotestage == 1) //approved
                        $srcstage = 14; //won
                    if ($quotestage == 2) //negotiation
                        $srcstage = 13; //negotiation
                    if ($quotestage == 6) //quote rejected by customer
                        $srcstage = 27; //lost
                    if ($quotestage == 7) //quoted to customer
                        $srcstage = 12; //quote sent to client
                    if ($srcstage) {
                        $oldAttributessrc = Yii::$app->db->createCommand("select * from `sourcingdeal` where sourcingdeal_id=:sourcingdeal_id")
                            ->bindValue(":sourcingdeal_id", $related_to_id)
                            ->queryOne();
                        //update sourcing deal
                        $sql = "Update sourcingdeal set stage = :srcstage where sourcingdeal_id = :sourcingdeal_id";
                        $updt = Yii::$app->db->createCommand($sql)
                            ->bindValue(":srcstage", $srcstage)
                            ->bindValue(":sourcingdeal_id", $related_to_id)
                            ->execute();

                        $newattributessrc = array("stage" => $srcstage);

                        $modlog->auditlog($oldAttributessrc, $newattributessrc, "sourcingdeal", $related_to_id, 2, Yii::$app->user->id);
                    }
                }
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `quotes` where quotes_id=:quotes_id")
                    ->bindValue(":quotes_id", $RecordId)
                    ->queryOne();
                $output = Quotes::updateAll($data, 'quotes_id = :quotes_id', [':quotes_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                //remove from child table
                Yii::$app->db->createCommand("delete from `quoted_items_detail` where quotes_id=:quotes_id")
                    ->bindValue(":quotes_id", $RecordId)
                    ->queryOne();
                //save to child table
                $child = new QuotedItemsDetail();
                $child->saveQuotedItemsDetail($RecordId);

                $modelleadetail->saveToVpReports($RecordId);



                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "degaussing") {
                //now update submodules
                $modelleadetail = new Degaussing();
                $data = $_POST["degaussing"]; // Array of key-value pairs to update
                $fileInstance = 'degaussing';
                $hdd_completed_count = 0;
                $degaussing_status = $modelleadetail->stageCalc($RecordId);
                $data["degaussing_status"] = $degaussing_status;
                if ($degaussing_status == 3 && !empty($data["fe_name"])) {
                    $data["ownerid"] = $data["fe_name"];
                }
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `degaussing` where degaussinginfo_id=:degaussinginfo_id")
                    ->bindValue(":degaussinginfo_id", $RecordId)
                    ->queryOne();
                $output = Degaussing::updateAll($data, 'degaussinginfo_id = :degaussinginfo_id', [':degaussinginfo_id' => $RecordId]);
                $fileInstance = 'degaussing_asset_details';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                        // Handle each file in this specific file input (attachment)
                        foreach ($files as $fileName => $file) {
                            // If file name is empty, fallback to the hidden file or another backup option
                            if (empty($file)) {
                                echo "No file selected for file $key.<br>";
                                // Check if there is a hidden file (previously uploaded)
                                if (isset($_POST["degaussing_asset_details"][$key][$fileName . '_hidden']) && !empty($_POST["degaussing_asset_details"][$key][$fileName . '_hidden'])) {
                                    // Assign the hidden file value to the attachment field
                                    $_POST["degaussing_asset_details"][$key][$fileName] = $_POST["degaussing_asset_details"][$key][$fileName . '_hidden'];
                                }

                                continue; // Skip this file and move to the next one
                            }
                            // Check for errors in file upload
                            if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                echo "degaussing_asset_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                continue; // Skip this file and move to the next one
                            }

                            // Create an UploadedFile instance
                            $fileInstanceData = new \yii\web\UploadedFile([
                                'name' => $file,
                                'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                            ]);

                            // Call your method to save the file
                            $result = $this->saveAttachedFiles($fileInstanceData);

                            // Check if file saving was successful
                            if ($result['success']) {
                                $_POST["degaussing_asset_details"][$key][$fileName] = $result['fileName'];
                                //delete old file
                                if (isset($_POST["degaussing_asset_details"][$key][$fileName . '_hidden'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST["degaussing_asset_details"][$key][$fileName . '_hidden']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];
                                    //print_r($records);die;

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                            } else {
                                // Handle the failure, log the message if needed
                                continue; // Continue processing the next file
                            }
                        }
                    }
                }

                if (isset($_POST['degaussing_asset_details'])) {
                    Yii::$app->db->createCommand("delete from `degaussing_asset_details` where degaussinginfo_id=:degaussinginfo_id")
                        ->bindValue(":degaussinginfo_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $data_wiping_asset = new DegaussingAssetDetails();
                    $hdd_completed_count = $data_wiping_asset->saveDegaussingAssets($RecordId);
                }
                Degaussing::updateAll(['hdd_completed' => $hdd_completed_count], 'degaussinginfo_id = :degaussinginfo_id', [':degaussinginfo_id' => $RecordId]);
                $data["hdd_completed"] = $hdd_completed_count;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                //save to vp reports for degaussing
                $modelleadetail->saveToVpReports($RecordId);
            } else if ($module === "datawiping") {
                //now update submodules
                $modelleadetail = new DataWiping();
                $data = $_POST["data_wiping"]; // Array of key-value pairs to update
                $fileInstance = 'data_wiping';
                $hdd_completed_count = 0;
                $wiping_status = $modelleadetail->dataWipingStageCalc($RecordId);
                $data["wiping_status"] = $wiping_status;
                if ($wiping_status == 3 && !empty($data["fe_name"])) {
                    $data["ownerid"] = $data["fe_name"];
                }
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `data_wiping` where datawiping_id=:datawiping_id")
                    ->bindValue(":datawiping_id", $RecordId)
                    ->queryOne();
                $output = DataWiping::updateAll($data, 'datawiping_id = :datawiping_id', [':datawiping_id' => $RecordId]);
                $fileInstance = 'data_wiping_asset_details';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                        // Handle each file in this specific file input (attachment)
                        foreach ($files as $fileName => $file) {
                            // If file name is empty, fallback to the hidden file or another backup option
                            if (empty($file)) {
                                echo "No file selected for file $key.<br>";
                                // Check if there is a hidden file (previously uploaded)
                                if (isset($_POST["data_wiping_asset_details"][$key][$fileName . '_hidden']) && !empty($_POST["data_wiping_asset_details"][$key][$fileName . '_hidden'])) {
                                    // Assign the hidden file value to the attachment field
                                    $_POST["data_wiping_asset_details"][$key][$fileName] = $_POST["data_wiping_asset_details"][$key][$fileName . '_hidden'];
                                }

                                continue; // Skip this file and move to the next one
                            }
                            // Check for errors in file upload
                            if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                echo "data_wiping_asset_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                continue; // Skip this file and move to the next one
                            }

                            // Create an UploadedFile instance
                            $fileInstanceData = new \yii\web\UploadedFile([
                                'name' => $file,
                                'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                            ]);

                            // Call your method to save the file
                            $result = $this->saveAttachedFiles($fileInstanceData);

                            // Check if file saving was successful
                            if ($result['success']) {
                                $_POST["data_wiping_asset_details"][$key][$fileName] = $result['fileName'];
                                //delete old file
                                if (isset($_POST["data_wiping_asset_details"][$key][$fileName . '_hidden'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST["data_wiping_asset_details"][$key][$fileName . '_hidden']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];
                                    //print_r($records);die;

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                            } else {
                                // Handle the failure, log the message if needed
                                continue; // Continue processing the next file
                            }
                        }
                    }
                }

                if (isset($_POST['data_wiping_asset_details'])) {
                    Yii::$app->db->createCommand("delete from `data_wiping_asset_details` where datawiping_id=:datawiping_id")
                        ->bindValue(":datawiping_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $data_wiping_asset = new DataWipingAssetDetails();
                    $hdd_completed_count = $data_wiping_asset->saveDataWippingAssets($RecordId);
                }
                DataWiping::updateAll(['hdd_completed' => $hdd_completed_count], 'datawiping_id = :datawiping_id', [':datawiping_id' => $RecordId]);
                $data["hdd_completed"] = $hdd_completed_count;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                ///save to vp reports for datawiping
                $modelleadetail->saveToVpReports($RecordId);
            } else if ($module === "shredding") {
                //now update submodules
                $modelleadetail = new Shredding();
                $data = $_POST["shredding"]; // Array of key-value pairs to update
                $fileInstance = 'shredding';
                $hdd_completed_count = 0;
                $wiping_status = $modelleadetail->shreddingStageCalc($RecordId);
                $data["shredding_status"] = $wiping_status;
                if ($wiping_status == 3 && !empty($data["fe_name"])) {
                    $data["ownerid"] = $data["fe_name"];
                }

                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `shredding` where shredding_id=:shredding_id")
                    ->bindValue(":shredding_id", $RecordId)
                    ->queryOne();
                // print_r($data);die;
                $output = Shredding::updateAll($data, 'shredding_id = :shredding_id', [':shredding_id' => $RecordId]);
                $fileInstance = 'shredding_asset_details';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $files) {
                        // Handle each file in this specific file input (attachment)
                        foreach ($files as $fileName => $file) {
                            // If file name is empty, fallback to the hidden file or another backup option
                            if (empty($file)) {
                                echo "No file selected for file $key.<br>";
                                // Check if there is a hidden file (previously uploaded)
                                if (isset($_POST["shredding_asset_details"][$key][$fileName . '_hidden']) && !empty($_POST["shredding_asset_details"][$key][$fileName . '_hidden'])) {
                                    // Assign the hidden file value to the attachment field
                                    $_POST["shredding_asset_details"][$key][$fileName] = $_POST["shredding_asset_details"][$key][$fileName . '_hidden'];
                                }

                                continue; // Skip this file and move to the next one
                            }
                            // Check for errors in file upload
                            if ($_FILES[$fileInstance]['error'][$key][$fileName] !== UPLOAD_ERR_OK) {
                                echo "shredding_asset_details File upload error for file $key. Error code: " . $_FILES[$fileInstance]['error'][$key][$fileName] . "<br>";
                                continue; // Skip this file and move to the next one
                            }

                            // Create an UploadedFile instance
                            $fileInstanceData = new \yii\web\UploadedFile([
                                'name' => $file,
                                'type' => $_FILES[$fileInstance]['type'][$key][$fileName],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key][$fileName],
                                'error' => $_FILES[$fileInstance]['error'][$key][$fileName],
                                'size' => $_FILES[$fileInstance]['size'][$key][$fileName],
                            ]);

                            // Call your method to save the file
                            $result = $this->saveAttachedFiles($fileInstanceData);

                            // Check if file saving was successful
                            if ($result['success']) {
                                $_POST["shredding_asset_details"][$key][$fileName] = $result['fileName'];
                                //delete old file
                                if (isset($_POST["shredding_asset_details"][$key][$fileName . '_hidden'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST["shredding_asset_details"][$key][$fileName . '_hidden']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];
                                    //print_r($records);die;

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                            } else {
                                // Handle the failure, log the message if needed
                                continue; // Continue processing the next file
                            }
                        }
                    }
                }

                if (isset($_POST['shredding_asset_details'])) {
                    Yii::$app->db->createCommand("delete from `shredding_asset_details` where shredding_id=:shredding_id")
                        ->bindValue(":shredding_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $shredding_asset = new ShreddingAssetDetails();
                    $hdd_completed_count = $shredding_asset->saveShreddingAssets($RecordId);
                }
                Shredding::updateAll(['hdd_completed' => $hdd_completed_count], 'shredding_id = :shredding_id', [':shredding_id' => $RecordId]);
                $data["hdd_completed"] = $hdd_completed_count;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                //save to vp reports for shredding
                $modelleadetail->saveToVpReports($RecordId);
            } else if ($module === "inspection") {
                //now update submodules
                $modelleadetail = new Inspection();
                $data = $_POST["inspection"]; // Array of key-value pairs to update
                foreach ($data as $key => $val) {
                    if (is_array($val)) {
                        $data[$key] = implode(",", $val);
                    }
                }
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                if (isset($data['submit_for_logistics']) && $data['submit_for_logistics'] == 1) {
                    $data['stages'] = 2; //logistics pending

                    //assign to Logistic manager
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H52' limit 1";
                    //added bt deepika on 19 june 2025
                    // $reports = "-- First, get the next higher user ID after the last modifier
                    //                 (
                    //                     SELECT u.id
                    //                     FROM user u
                    //                     JOIN user2role ur ON ur.userid = u.id
                    //                     WHERE u.deleted = 0
                    //                     AND u.status = 10
                    //                     AND ur.roleid = 'H52'
                    //                     AND u.id > (
                    //                         SELECT whodid
                    //                         FROM modtracker_basic
                    //                         WHERE module = '" . ucfirst($this->moduleName) . "' AND status = 2
                    //                         ORDER BY changedon DESC
                    //                         LIMIT 1
                    //                     )
                    //                     ORDER BY u.id ASC
                    //                     LIMIT 1
                    //                 )
                    //                 UNION ALL
                    //                 -- If none, wrap around to the lowest ID (still excluding the last modifier)
                    //                 (
                    //                     SELECT u.id
                    //                     FROM user u
                    //                     JOIN user2role ur ON ur.userid = u.id
                    //                     WHERE u.deleted = 0
                    //                     AND u.status = 10
                    //                     AND ur.roleid = 'H52'
                    //                     AND u.id != (
                    //                         SELECT whodid
                    //                         FROM modtracker_basic
                    //                         WHERE module = '" . ucfirst($this->moduleName) . "' AND status = 2
                    //                         ORDER BY changedon DESC
                    //                         LIMIT 1
                    //                     )
                    //                     ORDER BY u.id ASC
                    //                     LIMIT 1
                    //                 )
                    //                 LIMIT 1";

                    // $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // // print_r($rest);die;
                    // if (isset($rest['id']) && !empty($rest['id'])) {
                    //     $data['ownerid'] = $rest['id'];
                    //     $ownerid = $data['ownerid'];
                    // }
                    //new logic assign to logistic spoc added on 5 july 2025

                    if (isset($data['logistics_spoc']) && !empty($data['logistics_spoc'])) {
                        $data['ownerid'] = $data['logistics_spoc'];
                        $ownerid = $data['ownerid'];
                    } else {
                        Yii::$app->session->setFlash('error', 'Info: Invalid request. Logistic SPOC cannot be blank when submitted for logistics');
                        // Throw a BadRequestHttpException
                        throw new Exception('Info: Invalid request. Logistic SPOC cannot be blank when submitted for logistics');
                    }
                }
                /////pave scheduled
                if (isset($data['schedule_inspection']) && $data['schedule_inspection'] == 1) {
                    $data['stages'] = 3; //logistics pending


                    if (isset($data['logistics_fe_name_done_by_dwmpl']) && !empty($data['logistics_fe_name_done_by_dwmpl'])) {//deshwal will do the inspection
                        $data['ownerid'] = $data['logistics_fe_name_done_by_dwmpl'];
                        $ownerid = $data['ownerid'];
                    }

                }
                /////inspection in process

                if (isset($data['schedule_inspection']) && $data['schedule_inspection'] == 1) {
                    $data['stages'] = 3; //logistics pending
                }

                if (isset($data['inspection_started']) && !empty($data['inspection_started'])) {//deshwal will do the inspection
                    $data['inspection_start_date'] = date('Y-m-d');
                    $data['stages'] = 4; //inspection in process

                }
                if (isset($data['inspection_completed']) && !empty($data['inspection_completed'])) {//deshwal will do the inspection
                    $data['inpection_completed_date'] = date('Y-m-d');
                    $data['stages'] = 8; //inspection completed
                    //check if any of inspected products added for inspections or not
                    $condprod = $modelleadetail->checkProducts($RecordId);
                    //echo $condprod;die;
                    // If no records were found, throw an error
                    if (!$condprod) {
                        //echo "Error: Invalid request. Add Products in Laptop, Desktop, TFT, or General Inspection detail.";die;
                        Yii::$app->session->setFlash('error', 'Error: Invalid request. Add Products in Laptop, Desktop, TFT, or General Inspection detail.');
                        // Throw a BadRequestHttpException
                        throw new Exception('Invalid request. Add Products in Laptop, Desktop, TFT, or General Inspection detail.');
                    }

                }





                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `inspection` where inspection_id=:inspection_id")
                    ->bindValue(":inspection_id", $RecordId)
                    ->queryOne();
                $output = Inspection::updateAll($data, 'inspection_id = :inspection_id', [':inspection_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);





                //save to child table
                $child = new InspectionFullProductDetailLaptop();
                $child->saveInspectionFullProductDetailLaptop($RecordId);

                //save to child table
                $child = new InspectionFullProductDetailDesktop();
                $child->saveInspectionFullProductDetailDesktop($RecordId);

                //save to child table
                $child = new InspectionFullProductDetailTft();
                $child->saveInspectionFullProductDetailTft($RecordId);

                //save to child table
                $child = new InspectionRandomProductDetail();
                $child->saveInspectionRandomProductDetail($RecordId);



                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "weighing") {
                //now update submodules
                $modelleadetail = new Weighing();
                $data = $_POST["weighing"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `weighing` where weighing_id=:weighing_id")
                    ->bindValue(":weighing_id", $RecordId)
                    ->queryOne();
                $output = Weighing::updateAll($data, 'weighing_id = :weighing_id', [':weighing_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "drillingformat") {
                //now update submodules
                $modelleadetail = new DrillingFormat();
                $data = $_POST["drilling_format"]; // Array of key-value pairs to update
                $fileInstance = 'drilling_format';
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `drilling_format` where drilling_format_id=:drilling_format_id")
                    ->bindValue(":drilling_format_id", $RecordId)
                    ->queryOne();
                $output = DrillingFormat::updateAll($data, 'drilling_format_id = :drilling_format_id', [':drilling_format_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "drillingvendordetails") {
                //now update submodules
                $modelleadetail = new DrillingVendorDetails();
                $data = $_POST["drilling_vendor_details"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `drilling_vendor_details` where drilling_vendor_id=:drilling_vendor_id")
                    ->bindValue(":drilling_vendor_id", $RecordId)
                    ->queryOne();
                $output = DrillingVendorDetails::updateAll($data, 'drilling_vendor_id = :drilling_vendor_id', [':drilling_vendor_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;

                //remove from child table
                Yii::$app->db->createCommand("delete from `drilling_vendor_costing` where drilling_vendor_id=:drilling_vendor_id")
                    ->bindValue(":drilling_vendor_id", $RecordId)
                    ->queryOne();
                //save to child table
                $child = new DrillingVendorCosting();
                $child->saveDrillingVendorCosting($RecordId);


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "degaussingformat") {
                //now update submodules
                $modelleadetail = new DegaussingFormat();
                $data = $_POST["degaussing_format"]; // Array of key-value pairs to update
                $fileInstance = 'degaussing_format';
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }
                if (isset($data["image_available"]) && $data["image_available"] != 2) {
                    $data["image"] = "";
                }
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `degaussing_format` where degaussing_format_id=:degaussing_format_id")
                    ->bindValue(":degaussing_format_id", $RecordId)
                    ->queryOne();
                $output = DegaussingFormat::updateAll($data, 'degaussing_format_id = :degaussing_format_id', [':degaussing_format_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "degaussingvendordetails") {
                //now update submodules
                $modelleadetail = new DegaussingVendorDetails();
                $data = $_POST["degaussing_vendor_details"]; // Array of key-value pairs to update

                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `degaussing_vendor_details` where degaussing_vendor_id=:degaussing_vendor_id")
                    ->bindValue(":degaussing_vendor_id", $RecordId)
                    ->queryOne();
                $output = DegaussingVendorDetails::updateAll($data, 'degaussing_vendor_id = :degaussing_vendor_id', [':degaussing_vendor_id' => $RecordId]);

                //remove from child table
                Yii::$app->db->createCommand("delete from `degaussing_vendor_costing` where degaussing_vendor_id=:degaussing_vendor_id")
                    ->bindValue(":degaussing_vendor_id", $RecordId)
                    ->queryOne();
                //save to child table
                $child = new DegaussingVendorCosting();
                $child->saveDegaussingVendorCosting($RecordId);
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "servicedetail") {

                //now update submodules
                $modelleadetail = new Servicedetail();
                $data = $_POST["servicedetail"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `servicedetail` where servicedetail_id =:servicedetail_id")
                    ->bindValue(":servicedetail_id", $RecordId)
                    ->queryOne();
                $output = Servicedetail::updateAll($data, 'servicedetail_id  = :servicedetail_id ', [':servicedetail_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                //remove from child table
                Yii::$app->db->createCommand("delete from `servicedetail_details` where servicedetail_id =:servicedetail_id")
                    ->bindValue(":servicedetail_id", $RecordId)
                    ->queryOne();
                //save to child table
                $child = new ServicedetailDetails();
                $child->saveServicedetailDetails($RecordId);

                if ($data['related_to'] == 51) {
                    $this->SaveSourcingdealTotal($data['related_to_id']);
                }

                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "campaign") {


                //now update submodules
                $modelleadetail = new Campaign();
                $data = $_POST["campaign"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `campaign` where campaign_id=:campaign_id")
                    ->bindValue(":campaign_id", $RecordId)
                    ->queryOne();
                $output = Campaign::updateAll($data, 'campaign_id = :campaign_id', [':campaign_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "contracts") {


                //now update submodules
                $modelleadetail = new Contracts();
                $data = $_POST["contracts"]; // Array of key-value pairs to update
                //  echo "<pre>";print_r($_POST);die;
                //added by ptpatel on date 17-06-25 code to send contract to RSM user for approval and notification
                if (isset($data['contract_status'])) {
                    if (isset($data['send_for_review']) && $data['send_for_review'] == 1) { //1 = draft

                        $data['contract_status'] = 2; //in review
                        ///assign to rolid H51 = RSM/ZM
                        //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H51' ORDER BY RAND()   limit 1";
                        //added by deepika on 19 june
                        $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H51'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H51'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H51'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                        $rest = Yii::$app->db->createCommand($reports)->queryOne();
                        // print_r($rest);die;
                        if (isset($rest['id']) && !empty($rest['id'])) {
                            $data['ownerid'] = $rest['id'];
                            $ownerid = $data['ownerid'];
                        }
                        $message = "Contract No. " . $data['contract_no'] . " is submitted for Review. Please check";
                        $this->sendnotification($data['ownerid'], $message, $this->moduleName, $RecordId);

                    }
                }
                //after contract approve ISR will check signin block details and check contract attach is checked or not
                // 
                if (isset($data['contract_attached']) && $data['contract_attached'] == 1 && $data['contract_status'] == 3) {
                    $data['contract_status'] = 7; //signed
                }
                // expire contract when contract_end_date date is higer or = current date
                // else if (isset($data['contract_end_date']) && $data['contract_end_date'] != '') {
                //     $end_date = $data['contract_end_date'];
                //     $today = date('Y-m-d');
                //     if ($today >= $end_date) {
                //         $data['contract_status'] = 4; //expired
                //     }
                // }
                // activate contract when activated date is higer or = current date
                else if (isset($data['activated_date']) && $data['activated_date'] != '') {
                    $dateToCheck = $data['activated_date'];
                    $today = date('Y-m-d');
                    if ($dateToCheck >= $today) {
                        $data['contract_status'] = 9; //activated
                    }
                }
                //end code added by ptpatel
                // print_r($data);
                // die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `contracts` where contract_id=:contract_id")
                    ->bindValue(":contract_id", $RecordId)
                    ->queryOne();
                $output = Contracts::updateAll($data, 'contract_id = :contract_id', [':contract_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                ///check if contract_status =3 approved the setvbilling type of accoun to RC
                if (isset($data['contract_status']) && $data['contract_status'] == 3) {
                    $sql = "update vendor_account set billing_type=1 where vendoraccid =:vendoraccid";
                    Yii::$app->db->createCommand($sql)->bindValue(":vendoraccid", $data['account_name'])->execute();
                }
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "sourcingdealcontactrole") {


                //now update submodules
                $modelleadetail = new Sourcingdealcontactrole();
                $data = $_POST["sourcingdeal_contact_role"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `sourcingdeal_contact_role` where contact_roleid=:contact_roleid")
                    ->bindValue(":contact_roleid", $RecordId)
                    ->queryOne();
                $output = Sourcingdealcontactrole::updateAll($data, 'contact_roleid = :contact_roleid', [':contact_roleid' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
            } else if ($module === "opportunitycontactrole") {


                //now update submodules
                $modelleadetail = new Opportunitycontactrole();
                $data = $_POST["opportunity_contact_role"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `opportunity_contact_role` where contact_roleid=:contact_roleid")
                    ->bindValue(":contact_roleid", $RecordId)
                    ->queryOne();
                $output = Opportunitycontactrole::updateAll($data, 'contact_roleid = :contact_roleid', [':contact_roleid' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
            } else if ($module === "drillingcalculator") {
                //now update submodules
                $modelleadetail = new DrillingCalculatorParents();
                $data = $_POST["drilling_calculator_parents"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `drilling_calculator_parents` where drilling_cal_parent_id=:drilling_cal_parent_id")
                    ->bindValue(":drilling_cal_parent_id", $RecordId)
                    ->queryOne();
                $output = DrillingCalculatorParents::updateAll($data, 'drilling_cal_parent_id = :drilling_cal_parent_id', [':drilling_cal_parent_id' => $RecordId]);

                //remove from child table
                Yii::$app->db->createCommand("delete from `drilling_calculator` where drilling_cal_parent_id =:drilling_cal_parent_id")
                    ->bindValue(":drilling_cal_parent_id", $RecordId)
                    ->queryOne();

                //save to child table
                $child = new DrillingCalculator();
                $child->saveDrillingCalculator($RecordId);

                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "datawipingcalculator") {
                //now update submodules
                $modelleadetail = new DatawipingCalculatorParents();
                $data = $_POST["datawiping_calculator_parents"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `datawiping_calculator_parents` where datawiping_cal_parent_id=:datawiping_cal_parent_id")
                    ->bindValue(":datawiping_cal_parent_id", $RecordId)
                    ->queryOne();
                $output = DatawipingCalculatorParents::updateAll($data, 'datawiping_cal_parent_id = :datawiping_cal_parent_id', [':datawiping_cal_parent_id' => $RecordId]);

                //remove from child table
                Yii::$app->db->createCommand("delete from `datawiping_calculator` where datawiping_cal_parent_id =:datawiping_cal_parent_id")
                    ->bindValue(":datawiping_cal_parent_id", $RecordId)
                    ->queryOne();

                //save to child table
                $child = new DatawipingCalculator();
                // print_r($child);die;
                $child->saveDatawipingCalculator($RecordId);

                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "shreddingcalculator") {
                //now update submodules
                $modelleadetail = new ShreddingCalculatorParents();
                $data = $_POST["shredding_calculator_parents"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `shredding_calculator_parents` where shredding_cal_parent_id=:shredding_cal_parent_id")
                    ->bindValue(":shredding_cal_parent_id", $RecordId)
                    ->queryOne();
                $output = ShreddingCalculatorParents::updateAll($data, 'shredding_cal_parent_id = :shredding_cal_parent_id', [':shredding_cal_parent_id' => $RecordId]);

                //remove from child table
                Yii::$app->db->createCommand("delete from `shredding_calculator` where shredding_cal_parent_id =:shredding_cal_parent_id")
                    ->bindValue(":shredding_cal_parent_id", $RecordId)
                    ->queryOne();

                //save to child table
                $child = new ShreddingCalculator();
                $child->saveShreddingCalculator($RecordId);

                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "degaussingcalculator") {
                //now update submodules
                $modelleadetail = new DegaussingCalculatorParents();
                $data = $_POST["degaussing_calculator_parents"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `degaussing_calculator_parents` where degaussing_cal_parent_id=:degaussing_cal_parent_id")
                    ->bindValue(":degaussing_cal_parent_id", $RecordId)
                    ->queryOne();
                $output = DegaussingCalculatorParents::updateAll($data, 'degaussing_cal_parent_id = :degaussing_cal_parent_id', [':degaussing_cal_parent_id' => $RecordId]);

                //remove from child table
                Yii::$app->db->createCommand("delete from `degaussing_calculator` where degaussing_cal_parent_id =:degaussing_cal_parent_id")
                    ->bindValue(":degaussing_cal_parent_id", $RecordId)
                    ->queryOne();

                //save to child table
                $child = new DegaussingCalculator();
                $child->saveDegaussingCalculator($RecordId);

                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "termsandconditions") {
                //now update submodules
                $modelleadetail = new TermsAndConditions();
                $data = $_POST["terms_and_conditions"]; // Array of key-value pairs to update
                // print_r($_POST);
                // die;
                //  print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `terms_and_conditions` where terms_conditions_id=:terms_conditions_id")
                    ->bindValue(":terms_conditions_id", $RecordId)
                    ->queryOne();
                $output = TermsAndConditions::updateAll($data, 'terms_conditions_id = :terms_conditions_id', [':terms_conditions_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "gateinward") {
                //now update submodules
                $modelleadetail = new Gateinward();
                $data = $_POST["gateinward"]; // Array of key-value pairs to update
                $docket_number = $data["docket_number"];
                $fileInstance = 'gateinward';
                if (isset($_FILES[$fileInstance]) && is_array($_FILES[$fileInstance]['name'])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `gateinward` where gateinward_id=:gateinward_id")
                    ->bindValue(":gateinward_id", $RecordId)
                    ->queryOne();
                $output = Gateinward::updateAll($data, 'gateinward_id = :gateinward_id', [':gateinward_id' => $RecordId]);

                if (isset($_POST['gateinward_details'])) {
                    Yii::$app->db->createCommand("delete from `gateinward_details` where gateinward_id=:gateinward_id")
                        ->bindValue(":gateinward_id", $RecordId)
                        ->queryOne();
                    //save to child table
                    $gate_inward_items = new GateinwardDetails();
                    $gate_inward_items->saveGateinwardDetails($RecordId);
                }
                //update status of pickup shipped details items 
                $modelleadetail->updateStatusOfPickupShippedDetailsItems($docket_number);
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "pickupcalculator") {
                //now update submodules
                $modelleadetail = new PickupCalculatorParent();
                $data = $_POST["pickup_calculator_parent"]; // Array of key-value pairs to update
                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `pickup_calculator_parent` where pickup_calculator_parentid=:pickup_calculator_parentid")
                    ->bindValue(":pickup_calculator_parentid", $RecordId)
                    ->queryOne();
                $output = PickupCalculatorParent::updateAll($data, 'pickup_calculator_parentid = :pickup_calculator_parentid', [':pickup_calculator_parentid' => $RecordId]);

                //remove from child table
                Yii::$app->db->createCommand("delete from `pickup_calculator` where pickup_calculator_parentid =:pickup_calculator_parentid")
                    ->bindValue(":pickup_calculator_parentid", $RecordId)
                    ->queryOne();

                //save to child table

                $child = new PickupCalculator();
                $child->savePickupCalculator($RecordId);


                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "payments") {
                //now update submodules
                $modelleadetail = new Payments();
                $data = $_POST["payments"]; // Array of key-value pairs to update
                if (isset($data['submit_approval']) && $data['submit_approval'] == 1) {
                    // $data['stage'] = 6; //logistics pending

                    //assign to pricing team
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H16' limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H16'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H16'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H16'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $data['stage'] = "2";

                        $ownerid = $data['ownerid'];


                        $paymentinfo = Payments::find()->select('payment_no')->where(['payments_id' => $RecordId])->one();
                        $notification = new Notifications();
                        $notification->userid = $data['ownerid'];
                        $notification->message = "Payment No " . $paymentinfo['payment_no'] . " has been submitted for approval. Please check";
                        $notification->read_status = 0; // Unread notification
                        $notification->display_status = 0;
                        $notification->source_link = Yii::$app->request->baseUrl . "/" . $this->moduleName . "/detail?Record=" . $RecordId;
                        ;
                        $notification->createdtime = date('Y-m-d H:i:s');
                        $notification->modifiedtime = date('Y-m-d H:i:s');
                        if (!$notification->save()) {
                            echo 'save failed';
                            exit;
                        }
                    }
                }

                // print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `payments` where payments_id=:paymentsid")
                    ->bindValue(":paymentsid", $RecordId)
                    ->queryOne();
                // print_r($modelleadetail->oldAttributes);die;

                try {
                    $output = Payments::updateAll($data, '
                    payments_id = :paymentsid', [':paymentsid' => $RecordId]);
                    if ($output === false) {
                        // Handle the case where the update fails
                        throw new Exception("Failed to update record");
                    }
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }


                //remove from child table
                Yii::$app->db->createCommand("delete from `payments_invoice_details` where payments_id =:payments_id")
                    ->bindValue(":payments_id", $RecordId)
                    ->queryOne();

                //save to child table
                $child = new PaymentsInvoiceDetails();
                $child->savePaymentsInvoiceDetails($RecordId);

                //remove from child table
                Yii::$app->db->createCommand("delete from `payment_details` where payments_id =:payments_id")
                    ->bindValue(":payments_id", $RecordId)
                    ->queryOne();

                //save to child table
                $child = new PaymentDetails();
                $child->savePaymentDetails($RecordId);


                Yii::$app->db->createCommand("delete from `payments_attachment_detail` where payments_id =:payments_id")
                    ->bindValue(":payments_id", $RecordId)
                    ->queryOne();
                //save to child table
                $child = new PaymentsAttachmentDetail();
                $child->savePaymentsAttachmentDetail($RecordId);

                // print_r($modelleadetail->oldAttributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                ///save to reports
                $modelleadetail->savetoreports($RecordId);

            } else if ($module === "segregation") {
                //now save submodules
                // echo "<pre>hi";print_r($modelleadetail->attributes);die;
                $modelleadetail = new Segregation();
                $data = $_POST["segregation"];
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $data['deleted'] = 0;
                // $modelleadetail->save_as_draft = $data['save_as_draft'];
                $modelleadetail->attributes = $data;

                if ($modelleadetail->save_as_draft == 1) {
                    // echo "in if of update";die;
                    $grnid = Grn::find()->select('grn_id')->where(['grn_no' => $modelleadetail->grn_no])->one();
                    $grn_assets_id = GrnAssetDetail::find()->select('grn_asset_detail_id')->where(['grn_id' => $grnid->grn_id])->one();
                    if ($grn_assets_id != null) {
                        Yii::$app->db->createCommand("UPDATE grn_asset_detail set grn_status = 0 where grn_asset_detail_id=:id")
                            ->bindValue(":id", $grn_assets_id->grn_asset_detail_id)
                            ->execute();
                    } else {
                        echo "Grn Asset Detail not found";
                    }

                    $fieldId = $this->fieldId;
                    //save code
                    $inventory = new Inventory();
                    $inventory->saveInventory($_POST['segregation'], $_POST['segregation_detail'], $grnid->grn_id);

                    Yii::$app->db->createCommand("delete from `segregation_detail` where segregation_id=:segregation_id")->bindValue(":segregation_id", $RecordId)->execute();
                    Yii::$app->db->createCommand("delete from `segregation` where segregation_id=:segregation_id")->bindValue(":segregation_id", $RecordId)->execute();

                } else {
                    try {
                        $output = Segregation::updateAll($data, 'segregation_id = :segregation_id', [':segregation_id' => $RecordId]);
                        if ($output === false) {
                            // Handle the case where the update fails
                            throw new Exception("Failed to update record");
                        }
                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage();
                    }
                    Yii::$app->db->createCommand("delete from `segregation_detail` where segregation_id=:segregation_id")->bindValue(":segregation_id", $RecordId)->execute();
                    //save to child table
                    $segregation_detail = new SegregationDetail();
                    $segregation_detail->saveSegregationDetail($RecordId);
                }
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `segregation` where segregation_id=:segregation_id")
                    ->bindValue(":segregation_id", $RecordId)
                    ->queryOne();
                // echo "<pre>";print_r($_POST);die;


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                // echo "save";die;
            } else if ($module === "productpricebook") {


                //now save submodules
                $modelleadetail = new ProductPriceBook();
                $modelleadetail->attributes = $_POST["product_price_book"];
                $data = $_POST["product_price_book"]; // Array of key-value pairs to update
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `product_price_book` where productpricebook_id=:productpricebook_id")
                    ->bindValue(":productpricebook_id", $RecordId)
                    ->queryOne();
                $output = ProductPriceBook::updateAll($data, 'productpricebook_id = :productpricebook_id', [':productpricebook_id' => $RecordId]);
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "servicepricebook") {


                //now save submodules
                $modelleadetail = new ServicePriceBook();
                $modelleadetail->attributes = $_POST["service_price_book"];
                $data = $_POST["service_price_book"]; // Array of key-value pairs to update
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `service_price_book` where servicepricebook_id=:servicepricebook_id")
                    ->bindValue(":servicepricebook_id", $RecordId)
                    ->queryOne();
                $output = ServicePriceBook::updateAll($data, 'servicepricebook_id = :servicepricebook_id', [':servicepricebook_id' => $RecordId]);
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "pickupaddress") {


                //now save submodules
                $modelleadetail = new PickupAddress();
                $modelleadetail->attributes = $_POST["pickup_address"];
                $data = $_POST["pickup_address"]; // Array of key-value pairs to update
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `pickup_address` where pickupaddress_id=:pickupaddress_id")
                    ->bindValue(":pickupaddress_id", $RecordId)
                    ->queryOne();
                $output = PickupAddress::updateAll($data, 'pickupaddress_id = :pickupaddress_id', [':pickupaddress_id' => $RecordId]);
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "billingtoaddress") {


                //now save submodules
                $modelleadetail = new BillingToAddress();
                $modelleadetail->attributes = $_POST["billing_to_address"];
                $data = $_POST["billing_to_address"]; // Array of key-value pairs to update
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `billing_to_address` where billingtoaddress_id=:billingtoaddress_id")
                    ->bindValue(":billingtoaddress_id", $RecordId)
                    ->queryOne();
                $output = BillingToAddress::updateAll($data, 'billingtoaddress_id = :billingtoaddress_id', [':billingtoaddress_id' => $RecordId]);
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "billingfromaddress") {


                //now save submodules
                $modelleadetail = new BillingFromAddress();
                $modelleadetail->attributes = $_POST["billing_from_address"];
                $data = $_POST["billing_from_address"]; // Array of key-value pairs to update
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `billing_from_address` where billingfromaddress_id=:billingfromaddress_id")
                    ->bindValue(":billingfromaddress_id", $RecordId)
                    ->queryOne();
                $output = BillingFromAddress::updateAll($data, 'billingfromaddress_id = :billingfromaddress_id', [':billingfromaddress_id' => $RecordId]);
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "deliveryaddress") {


                //now save submodules
                $modelleadetail = new DeliveryAddress();
                $modelleadetail->attributes = $_POST["delivery_address"];
                $data = $_POST["delivery_address"]; // Array of key-value pairs to update
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `delivery_address` where deliveryaddress_id=:deliveryaddress_id")
                    ->bindValue(":deliveryaddress_id", $RecordId)
                    ->queryOne();
                $output = DeliveryAddress::updateAll($data, 'deliveryaddress_id = :deliveryaddress_id', [':deliveryaddress_id' => $RecordId]);
                //  echo "<pre>";
                // print_r($modelleadetail->attributes);die;
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "quotesdit") {

                //now update submodules
                $modelleadetail = new QuotesDit();

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `quotes_dit` where quotes_dit_id=:quotes_dit_id")
                    ->bindValue(":quotes_dit_id", $RecordId)
                    ->queryOne();



                $data = $_POST["quotes_dit"]; // Array of key-value pairs to update
                //edn on 13 jan 2025
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                // print_r($data);die;

                if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {

                    $data['quote_stage'] = 2; //First Approval pending
                    //    echo  $data['quote_stage'];die;

                    ///assign to business head
                    // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H83' ORDER BY RAND()   limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H83'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H83'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H83'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }
                    $message = "Quote No. " . $data['quotes_dit_no'] . " is submitted for First Approval. Please check";
                    $this->sendnotification($data['ownerid'], $message, $this->moduleName, $RecordId);

                }
                // print_r($data);die;


                $output = QuotesDit::updateAll($data, 'quotes_dit_id = :quotes_dit_id', [':quotes_dit_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;



                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

                //save to child table
                $QuotesditShipDetail = new QuotesditShipDetail();
                $QuotesditShipDetail->saveQuotesditShipDetail($RecordId);

                //save to child table
                $QuotesditProductDetail = new QuotesditProductDetail();
                $QuotesditProductDetail->saveQuotesditProductDetail($RecordId);


            } else if ($module === "salesorderdit") {

                //now update submodules
                $modelleadetail = new SalesorderDit();

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `salesorder_dit` where salesorder_dit_id=:salesorder_dit_id")
                    ->bindValue(":salesorder_dit_id", $RecordId)
                    ->queryOne();



                $data = $_POST["salesorder_dit"]; // Array of key-value pairs to update
                //edn on 13 jan 2025
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                //echo "<pre>";
                //print_r($data);die;

                if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {

                    $data['so_stage'] = 2; //First Approval pending
                    //    echo  $data['quote_stage'];die;

                    ///assign to devit cx
                    // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H86' ORDER BY RAND()   limit 1";
                    //added by deepika on 19 june
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H86'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H86'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H86'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }
                    $message = "Sales Order No. " . $modelleadetail->oldAttributes['salesorder_dit_no'] . " is submitted for First Approval. Please check";
                    $this->sendnotification($data['ownerid'], $message, $this->moduleName, $RecordId);

                }
                ///////save attachments////////////////
                $fileInstance = 'salesorder_dit';
                foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                    if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                        // Create an UploadedFile instance
                        $file = new \yii\web\UploadedFile([
                            'name' => $_FILES[$fileInstance]['name'][$key],
                            'type' => $_FILES[$fileInstance]['type'][$key],
                            'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                            'error' => $_FILES[$fileInstance]['error'][$key],
                            'size' => $_FILES[$fileInstance]['size'][$key],
                        ]);
                        $result = $this->saveAttachedFiles($file);
                        if ($result['success']) {
                            $data[$key] = (string) $result['fileName'];
                        } else {
                            echo $result["message"] ?? "Issue in file saving";
                            die();
                        }
                    }
                }
                // print_r($data);die;

                $output = SalesorderDit::updateAll($data, 'salesorder_dit_id = :salesorder_dit_id', [':salesorder_dit_id' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;



                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

                //save to child table
                $SalesorderditProductDetails = new SalesorderditProductDetails();
                $SalesorderditProductDetails->saveSalesorderditProductDetails($RecordId);

                //save to child table
                $SalesorderditShipToAddress = new SalesorderditShipToAddress();
                $SalesorderditShipToAddress->saveSalesorderditShipToAddress($RecordId);


            } else if ($module === "purchaseorderdit") {

                //now update submodules
                $modelleadetail = new PurchaseOrderDit();

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `purchase_order_dit` where purchaseorder_dit_id=:purchaseorder_dit_id")
                    ->bindValue(":purchaseorder_dit_id", $RecordId)
                    ->queryOne();



                $data = $_POST["purchase_order_dit"]; // Array of key-value pairs to update
                //edn on 13 jan 2025
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                //echo "<pre>";
                // print_r($data);die;

                if (isset($data['send_for_approval']) && $data['send_for_approval'] == 1) {

                    $data['stage'] = 2; //First Approval pending
                    //    echo  $data['quote_stage'];die;

                    ///assign to finanace manager
                    //$reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H87' ORDER BY RAND()   limit 1";
                    $reports = "-- If only one user exists in the role, return that user
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H87'
                        LIMIT 1
                    )

                    UNION ALL

                    -- If there are multiple users, find the next higher user ID after the last modifier
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H87'
                        AND u.id > (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module = '" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    UNION ALL

                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                    (
                        SELECT u.id
                        FROM user u
                        JOIN user2role ur ON ur.userid = u.id
                        WHERE u.deleted = 0
                        AND u.status = 10
                        AND ur.roleid = 'H87'
                        AND u.id != (
                            SELECT whodid
                            FROM modtracker_basic
                            WHERE module ='" . $this->moduleName . "'
                            AND status = 2
                            ORDER BY changedon DESC
                            LIMIT 1
                        )
                        ORDER BY u.id ASC
                        LIMIT 1
                    )

                    LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($rest);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }
                    $message = "Purchase Order No. " . $modelleadetail->oldAttributes['purchaseorder_dit_no'] . " is submitted for First Approval. Please check";
                    $this->sendnotification($data['ownerid'], $message, $this->moduleName, $RecordId);

                }


                $output = PurchaseOrderDit::updateAll($data, 'purchaseorder_dit_id = :purchase_order_dit', [':purchase_order_dit' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;



                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

                //save to child table
                $PurchaseorderditProductDetails = new PurchaseorderditProductDetails();
                $PurchaseorderditProductDetails->savePurchaseorderditProductDetails($RecordId);

            } else if ($module === "grndit") {
                echo "Edit not allowed in GRN DevIT";
                die;
                //now update submodules
                $modelleadetail = new GrnDit();

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `grn_dit` where grndit_id=:grndit_id")
                    ->bindValue(":grndit_id", $RecordId)
                    ->queryOne();



                $data = $_POST["grn_dit"]; // Array of key-value pairs to update
                //edn on 13 jan 2025
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                //echo "<pre>";
                // print_r($data);die;

                //start saving grn_dit files
                $fileInstance = 'grn_dit';

                // Loop through the array of files (assuming you're uploading multiple files)
                if (isset($_FILES[$fileInstance])) {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                if (isset($_POST[$key . '_hiddenfile'])) {
                                    $records = \app\models\Attachments::find()
                                        ->where(['attachmentsid' => $_POST[$key . '_hiddenfile']])
                                        ->one();
                                    $fileid = $records->attachmentsid;
                                    // print_r($records);die;
                                    $model = new Attachments();
                                    $records = $model->find()
                                        ->where(['attachmentsid' => $fileid])
                                        ->one();
                                    $fileName = $records['path'];

                                    // Define the base directory for files
                                    $filePath = Yii::getAlias('@webroot/' . $fileName);
                                    // unlink($filePath);
                                    // Check if the file exists before attempting to delete it
                                    if (file_exists($filePath)) {
                                        // Attempt to delete the file
                                        if (unlink($filePath)) {
                                            // echo "File removed successfully.";
                                        } else {
                                            //  echo "Error: Unable to delete the file.";
                                        }
                                    } else {
                                        // echo "File does not exist.";
                                    }
                                }
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }
                // end of saving grn_dit files

                $data["status"] = $modelleadetail->grnStageCalc($data["status"] ?? null);


                $output = GrnDit::updateAll($data, 'grndit_id = :grn_dit', [':grn_dit' => $RecordId]);
                // print_r($modelleadetail->oldAttributes);die;



                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

                //save to child table
                // $GrnditProductDetails = new GrnditProductDetails();
                // $GrnditProductDetails->saveGrnditProductDetails($RecordId);

                // //save to child table
                // $GrnditBarcodes = new GrnditBarcodes();
                // $GrnditBarcodes->saveGrnditBarcodes($RecordId);

                $modelleadetail->Savetoinventory($RecordId);

                $modelleadetail->Savetoinventory($RecordId);



            } else if ($module === "deliverychallandit") {
                $modelleadetail = new DeliveryChallandit();
                $modelleadetail->attributes = $_POST["delivery_challandit"];
                $data = $_POST["delivery_challandit"]; // Array of key-value pairs to update
                // echo "<pre>";print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                if ($data['status'] == 1 && $data['send_for_approval'] == 1) {  //1 = draft
                    $data['status'] = "2"; //in Approval Pending
                    ///assign to finance 
                    // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H51' ORDER BY RAND()   limit 1";
                    $reports = "-- If only one user exists in the role, return that user
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If there are multiple users, find the next higher user ID after the last modifier
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    AND u.id > (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module = '" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H87'
                                    AND u.id != (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module ='" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($reports);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }

                    //send notification to finance team
                    $message = "Delivery Challan No. " . $data['deliverychallan_no'] . " is submitted for Approval. Please check";
                    $this->sendnotification($ownerid, $message, $this->moduleName, $RecordId);
                }
                $fileInstance = 'delivery_challandit';
                //    echo "<pre>";print_r($_FILES['delivery_challandit']);die;
                if (isset($_FILES[$fileInstance]) && $data['status'] == 7) //delivered
                {
                    foreach ($_FILES[$fileInstance]['name'] as $key => $name) {
                        if ($_FILES[$fileInstance]['error'][$key] === UPLOAD_ERR_OK && !empty($name)) {
                            // Create an UploadedFile instance
                            $file = new \yii\web\UploadedFile([
                                'name' => $_FILES[$fileInstance]['name'][$key],
                                'type' => $_FILES[$fileInstance]['type'][$key],
                                'tempName' => $_FILES[$fileInstance]['tmp_name'][$key],
                                'error' => $_FILES[$fileInstance]['error'][$key],
                                'size' => $_FILES[$fileInstance]['size'][$key],
                            ]);
                            $result = $this->saveAttachedFiles($file);
                            if ($result['success']) {
                                $data[$key] = (string) $result['fileName'];
                            } else {
                                echo $result["message"] ?? "Issue in file saving";
                                die();
                            }
                        }
                    }
                }

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `delivery_challandit` where deliverychallan_id=:deliverychallan_id")
                    ->bindValue(":deliverychallan_id", $RecordId)
                    ->queryOne();
                // echo "2";
                $output = DeliveryChallandit::updateAll($data, 'deliverychallan_id = :deliverychallan_id', [':deliverychallan_id' => $RecordId]);

                $DeliverychallanditProductDetails = new DeliverychallanditProductDetails();
                $DeliverychallanditProductDetails->saveDeliverychallanditProductDetails($RecordId);


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

                // echo "<pre>";print_r($data['status']);die;
                if ($data['status'] == 7 || $data['status'] == 8) {
                    // echo "jaggannath";die;
                    $modelleadetail->savetoreports($RecordId);
                }
                if ($data['status'] == 7) {
                    $modelleadetail->changeInvoiceStatus($RecordId);
                    echo "inv status change when DC is delivered";
                }
                // echo "sasd";die;
            } else if ($module === "packinglistdit") {
                $modelleadetail = new PackingListDit();
                $modelleadetail->attributes = $_POST["packing_list_dit"];
                $data = $_POST["packing_list_dit"]; // Array of key-value pairs to update
                // echo "<pre>";print_r($_POST);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `packing_list_dit` where packinglist_id=:packinglist_id")
                    ->bindValue(":packinglist_id", $RecordId)
                    ->queryOne();
                // echo "2";
                $output = PackingListDit::updateAll($data, 'packinglist_id = :packinglist_id', [':packinglist_id' => $RecordId]);

                $PackinglistditProductDetails = new PackinglistditProductDetails();
                $PackinglistditProductDetails->savePackinglistditProductDetails($RecordId);


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "focdit") {
                $modelleadetail = new FocDit();
                $modelleadetail->attributes = $_POST["foc_dit"];
                $data = $_POST["foc_dit"]; // Array of key-value pairs to update
                // echo "<pre>";print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                if ($data['stage'] == 1 && $data['submit_for_approval'] == 1) {  //1 = draft
                    $data['stage'] = "2"; //in submit_for_approval
                    ///assign to finance 
                    // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H51' ORDER BY RAND()   limit 1";
                    $reports = "-- If only one user exists in the role, return that user
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H91'
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If there are multiple users, find the next higher user ID after the last modifier
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H91'
                                    AND u.id > (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module = '" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                UNION ALL

                                -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                                (
                                    SELECT u.id
                                    FROM user u
                                    JOIN user2role ur ON ur.userid = u.id
                                    WHERE u.deleted = 0
                                    AND u.status = 10
                                    AND ur.roleid = 'H91'
                                    AND u.id != (
                                        SELECT whodid
                                        FROM modtracker_basic
                                        WHERE module ='" . $this->moduleName . "'
                                        AND status = 2
                                        ORDER BY changedon DESC
                                        LIMIT 1
                                    )
                                    ORDER BY u.id ASC
                                    LIMIT 1
                                )

                                LIMIT 1;";
                    $rest = Yii::$app->db->createCommand($reports)->queryOne();
                    // print_r($reports);die;
                    if (isset($rest['id']) && !empty($rest['id'])) {
                        $data['ownerid'] = $rest['id'];
                        $ownerid = $data['ownerid'];
                    }

                    //send notification to finance team
                    $message = "FOC No. " . $data['focdit_no'] . " is submitted for Approval. Please check";
                    $this->sendnotification($ownerid, $message, $this->moduleName, $RecordId);
                }

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `foc_dit` where focdit_id=:focdit_id")
                    ->bindValue(":focdit_id", $RecordId)
                    ->queryOne();
                // echo "2";
                $output = FocDit::updateAll($data, 'focdit_id = :focdit_id', [':focdit_id' => $RecordId]);

                $FocditProductDetails = new FocditProductDetails();
                $FocditProductDetails->saveFocditProductDetails($RecordId);


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } else if ($module === "invoicedit") {
                $modelleadetail = new Invoicedit();
                $modelleadetail->attributes = $_POST["invoicedit"];
                $data = $_POST["invoicedit"]; // Array of key-value pairs to update
                // echo "<pre>";print_r($_POST["invoicedit"]['send_for_approval']);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
                if (isset($data['send_for_approval'])) {
                    if ($data['invoice_status'] == 1 && $data['send_for_approval'] == 1) {  //1 = draft
                        $data['invoice_status'] = "2"; //pending for approval
                        ///assign to devit finance  manager
                        // $reports = "select id from user join user2role on user2role.userid = user.id where user.deleted = 0 and status = 10 and user2role.roleid='H51' ORDER BY RAND()   limit 1";
                        $reports = "-- If only one user exists in the role, return that user
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H87'
                                        LIMIT 1
                                    )

                                    UNION ALL

                                    -- If there are multiple users, find the next higher user ID after the last modifier
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H87'
                                        AND u.id > (
                                            SELECT whodid
                                            FROM modtracker_basic
                                            WHERE module = '" . $this->moduleName . "'
                                            AND status = 2
                                            ORDER BY changedon DESC
                                            LIMIT 1
                                        )
                                        ORDER BY u.id ASC
                                        LIMIT 1
                                    )

                                    UNION ALL

                                    -- If no higher ID is found, wrap around to the lowest user ID (excluding the last modifier)
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H87'
                                        AND u.id != (
                                            SELECT whodid
                                            FROM modtracker_basic
                                            WHERE module ='" . $this->moduleName . "'
                                            AND status = 2
                                            ORDER BY changedon DESC
                                            LIMIT 1
                                        )
                                        ORDER BY u.id ASC
                                        LIMIT 1
                                    )

                                    LIMIT 1;";
                        $rest = Yii::$app->db->createCommand($reports)->queryOne();
                        // print_r($reports);die;
                        if (isset($rest['id']) && !empty($rest['id'])) {
                            $data['ownerid'] = $rest['id'];
                            $ownerid = $data['ownerid'];
                        }

                        //send notification to finance team
                        $message = "Invoice DevIT No. " . $data['invoicedit_no'] . " is submitted for Approval. Please check";
                        $this->sendnotification($ownerid, $message, $this->moduleName, $RecordId);
                    }
                }

                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `invoicedit` where invoicedit_id=:invoicedit_id")
                    ->bindValue(":invoicedit_id", $RecordId)
                    ->queryOne();
                // echo "2";
                $output = Invoicedit::updateAll($data, 'invoicedit_id = :invoicedit_id', [':invoicedit_id' => $RecordId]);

                //save to child table
                $InvoiceditProductDetails = new InvoiceditProductDetails();
                $InvoiceditProductDetails->saveInvoiceditProductDetails($RecordId);


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }

            } else if ($module === "paymentdit") {
                $modelleadetail = new Paymentdit();
                $modelleadetail->attributes = $_POST["paymentdit"];
                $data = $_POST["paymentdit"]; // Array of key-value pairs to update
                // echo "<pre>";print_r($_POST["paymentdit"]['send_for_approval']);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;


                $modelleadetail->oldAttributes = Yii::$app->db->createCommand("select * from `paymentdit` where paymentdit_id=:paymentdit_id")
                    ->bindValue(":paymentdit_id", $RecordId)
                    ->queryOne();
                // echo "2";
                $output = Paymentdit::updateAll($data, 'paymentdit_id = :paymentdit_id', [':paymentdit_id' => $RecordId]);


                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
                ///save to reports
                $modelleadetail->savetoreports($modelleadetail->paymentdit_id);

            } else if ($module === "exportrequest") {
                $modelleadetail = new Exportrequest();

                $modelleadetail->attributes = $_POST["exportrequest"];
                echo "<pre>";print_r($_POST);die;
                $data = $_POST["exportrequest"]; // Array of key-value pairs to update
                // echo "<pre>";print_r($data);die;
                $data['modifiedtime'] = date('Y-m-d H:i:s');
                $data['modifiedby'] = $uid;
               
                $modlog->auditlog($modelleadetail->oldAttributes, $data, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                if (!empty($CS)) {
                    //get old records
                    $oldCS = Yii::$app->db->createCommand("select * from $customtablename where " . $this->fieldId . "=" . $RecordId)->queryAll();
                    $command = Yii::$app->db->createCommand()->update($customtablename, $CS, [$this->fieldId = ':id'], [':id' => $RecordId])->execute();
                    $command->execute();
                    $modlog->auditlog($oldCS, $CS, $this->moduleName, $RecordId, $auditstatus, Yii::$app->user->id);
                }
            } 

            $transaction->commit();
            // echo "save";die;
            return true;

        } catch (Exception $e) {
            $transaction->rollBack();
            $error_message = $e->getMessage();
            Yii::$app->session->setFlash(
                "error",
                $error_message
            );
            return false;
        }
    }
    function convertdate()
    {
        // echo "<pre>";
        // print_r($_POST);die;
        // echo "<br>";
        $data = $_POST;
        $sql = "select * from tab where  name =:module ";
        $command = Yii::$app->db->createCommand($sql)->bindValue(":module", $_POST['module'])->queryOne();
        if (!$command)
            return 1;

        $tabid = $command['tabid'];

        foreach ($data as $key => $val) {
            //     if (!is_array($val)) {
            //        echo  $data[$key] ;
            // echo "<br>";

            //     }
            if (is_array($val)) {
                //tab from this date

                // print_r($data[$key]) ;
                // echo "<br>value=";

                //  print_r($val);
                foreach ($val as $key2 => $val2) {
                    if (is_array($val2)) {

                        // Loop through the array and print each key and value
                        foreach ($val2 as $key3 => $value3) {
                            // echo "Key: $key3, Value: $value3\n";
                            //check uitype of this value
                            $sql3 = "select uitype from field where tabid = :tabid and fieldname = :fieldname and uitype = 17";
                            $command3 = Yii::$app->db->createCommand($sql3)
                                ->bindValue(":tabid", $tabid)
                                ->bindValue(":fieldname", $key3)
                                ->queryOne();
                            if ($command3) {
                                if (!empty($value3)) {
                                    // Try to create a DateTime object from the given date in dd-mm-yyyy format
                                    $dateObject = \DateTime::createFromFormat('d-m-Y', $value3);

                                    if ($dateObject) {
                                        // Return the formatted date in yyyy-mm-dd format
                                        $_POST[$key][$key2][$key3] = $dateObject->format('Y-m-d');
                                    }
                                }
                            }
                            //check uitype of this value
                            $sql3 = "select uitype from field where tabid = :tabid and fieldname = :fieldname and uitype = 13";
                            $command3 = Yii::$app->db->createCommand($sql3)
                                ->bindValue(":tabid", $tabid)
                                ->bindValue(":fieldname", $key3)
                                ->queryOne();
                            if ($command3) {
                                if (!empty($value3)) {
                                    // Try to create a DateTime object from the given date in dd-mm-yyyy format
                                    $dateObject = \DateTime::createFromFormat('d-m-Y H:i', $value3);

                                    if ($dateObject) {
                                        // Return the formatted date in yyyy-mm-dd format
                                        $_POST[$key][$key2][$key3] = $dateObject->format('Y-m-d H:i');
                                    }
                                }
                            }
                        }
                    } else {
                        //check uitype of this value
                        $sql2 = "select uitype from field where tabid = :tabid and fieldname = :fieldname and uitype = 17";
                        $command2 = Yii::$app->db->createCommand($sql2)
                            ->bindValue(":tabid", $tabid)
                            ->bindValue(":fieldname", $key2)
                            ->queryOne();
                        if ($command2) {
                            if (!empty($val2)) {
                                // Try to create a DateTime object from the given date in dd-mm-yyyy format
                                $dateObject = \DateTime::createFromFormat('d-m-Y', $val2);

                                if ($dateObject) {
                                    // Return the formatted date in yyyy-mm-dd format
                                    $_POST[$key][$key2] = $dateObject->format('Y-m-d');
                                }
                            }
                        }
                        //check uitype of this value
                        $sql2 = "select uitype from field where tabid = :tabid and fieldname = :fieldname and uitype = 13";
                        $command2 = Yii::$app->db->createCommand($sql2)
                            ->bindValue(":tabid", $tabid)
                            ->bindValue(":fieldname", $key2)
                            ->queryOne();
                        if ($command2) {
                            if (!empty($val2)) {
                                // Try to create a DateTime object from the given date in dd-mm-yyyy format
                                $dateObject = \DateTime::createFromFormat('d-m-Y H:i', $val2);

                                if ($dateObject) {
                                    // Return the formatted date in yyyy-mm-dd format
                                    $_POST[$key][$key2] = $dateObject->format('Y-m-d H:i');
                                }
                            }
                        }
                    }
                }
            }
        }
        // print_r($_POST);
        // die;
    }
    //update crmentity sequence
    function updateCRMSequence($semodule, $crmid)
    {
        // echo "UPDATE `modentity_num` SET cur_id = $crmid where semodule='$semodule'" ;die;
        try {
            Yii::$app->db->createCommand("UPDATE `modentity_num` SET cur_id = :crmid where semodule=:semodule")
                ->bindParam(":crmid", $crmid)
                ->bindParam(":semodule", $semodule)
                ->execute();
        } catch (\Exception $e) {
            // Handle the error, e.g. log it or display a message
            Yii::error($e->getMessage());
        }
    }
    function getCRMSequence()
    {
        $seq = Yii::$app->db->createCommand("select id from crmentity_seq")->queryOne();
        $id = $seq['id'] + 1;
        // echo $id;die;
        return $id;
    }
    public function checkAutoNo()
    {

        $table_name = $this->tableName();
        $autoField = Yii::$app->db->createCommand("SELECT columnname
            FROM field 
            WHERE tablename = :tablename AND uitype = :uitype")
            ->bindValue(':tablename', $table_name)
            ->bindValue(':uitype', 11)
            ->queryOne();
        if (empty($autoField))
            return false; // if does not exist;
        if (count($autoField) < 1)
            return false;
        else
            return $autoField['columnname'];
    }
    public function setAutoNo($tabs)
    {
        $table_name = $this->moduleName;
        $model = new AutoNo();
        $upAutoNo = $model->setAutomoduleno($tabs, $table_name);
        return $upAutoNo;
    }
    public function getAutoNo($tabs)
    {
        $table_name = $this->moduleName;
        $model = new AutoNo();
        $orderno = $model->getautomoduleno($tabs, $table_name);
        return $orderno;
    }
    public function checkAutoNoModule($moduleName)
    {

        $table_name = $moduleName;
        // echo "SELECT columnname
        //     FROM field 
        //     WHERE tablename = '$table_name' AND uitype = 11";die;
        $autoField = Yii::$app->db->createCommand("SELECT columnname
            FROM field 
            WHERE tablename = :tablename AND uitype = :uitype")
            ->bindValue(':tablename', $table_name)
            ->bindValue(':uitype', 11)
            ->queryOne();
        if (empty($autoField))
            return false; // if does not exist;
        if (count($autoField) < 1)
            return false;
        else
            return $autoField['columnname'];
    }
    public function setAutoNoModule($moduleName)
    {
        if ($moduleName == "opportunity")
            $table_name = 'opportunities';
        else
            $table_name = $moduleName;


        $model = new AutoNo();
        $upAutoNo = $model->setAutomoduleno(1, $table_name);
        return $upAutoNo;
    }
    public function getAutoNoModule($moduleName)
    {
        if ($moduleName == "opportunity")
            $table_name = 'opportunities';
        else
            $table_name = $moduleName;
        $model = new AutoNo();
        $orderno = $model->getautomoduleno(1, $table_name);
        return $orderno;
    }

    public function sendnotification($userid, $message, $moduleName, $fieldId)
    {
        $notification = new Notifications();
        $notification->userid = $userid;
        $notification->message = $message;
        $notification->read_status = 0; // Unread notification
        $notification->display_status = 0;
        $notification->source_link = Yii::$app->request->baseUrl . "/" . $moduleName . "/detail?Record=" . $fieldId;
        ;
        $notification->createdtime = date('Y-m-d H:i:s');
        $notification->modifiedtime = date('Y-m-d H:i:s');
        if (!$notification->save()) {
            echo 'save failed';
            exit;
        }
    }

    // public function auditlog($oldAttributes,$newattributes,$ModuleName,$crmid,$auditstatus){
    //     if($auditstatus == 0){//insert
    //         //insert in modtrackerbasic table
    //         Yii::$app->db->createCommand("insert into `modtracker_basic` set crmid=:crmid,module=:module,whodid=:whodid,status=0,changedon=now()")
    //         ->bindValue("crmid",$crmid)
    //         ->bindValue("module",$ModuleName)
    //         ->bindValue("whodid",Yii::$app->user->id)
    //         ->execute();
    //         $lastid = Yii::$app->db->getLastInsertID();
    //             foreach ($newattributes as $column => $value) {

    //                 # code...
    //                 Yii::$app->db->createCommand("insert into `modtracker_detail` set id=:id,fieldname=:fieldname,prevalue=:prevalue,postvalue=:postvalue")
    //                     ->bindValue("id",$lastid)
    //                     ->bindValue("fieldname",$column)
    //                     ->bindValue("prevalue",'')
    //                     ->bindValue("postvalue",$value)
    //                     ->execute();

    //             }
    //     }
    //     else  if($auditstatus == 2)
    //     {//update
    //         if (!$oldAttributes) 
    //             {
    //                // die("Record not found!");
    //             }
    //             // print_r($oldAttributes);die;

    //             //  Compare old and new values
    //             $changes = [];
    //             foreach ($newattributes as $column => $newValue) {
    //                 if($column !="modifiedtime" && $column !="modifiedby" && $column !="creatorid" && 
    //                     $column !="createdtime")
    //                 {
    //                     $oldValue = $oldAttributes[$column] ?? null;
    //                     // echo $oldValue." ".$newValue;die; 
    //                     if ($oldValue != $newValue) {
    //                         $changes[$column] = [
    //                             'prevalue' => $oldValue,
    //                             'postvalue' => $newValue
    //                         ];
    //                     }
    //                 }
    //             }
    //             //print_r($changes);die;
    //                 //Log changes
    //             if (!empty($changes)) {
    //                     //insert in modtrackerbasic table
    //                     Yii::$app->db->createCommand("insert into `modtracker_basic` set crmid=:crmid,module=:module,whodid=:whodid,status=:status,changedon=now()")
    //                     ->bindValue("crmid",$crmid)
    //                     ->bindValue("status",$auditstatus)
    //                     ->bindValue("module",$ModuleName)
    //                     ->bindValue("whodid",Yii::$app->user->id)
    //                     ->execute();

    //                     $lastid = Yii::$app->db->getLastInsertID();

    //                     foreach ($changes as $column => $values) {

    //                         Yii::$app->db->createCommand("insert into `modtracker_detail` set id=:id,fieldname=:fieldname,prevalue=:prevalue,postvalue=:postvalue")
    //                                 ->bindValue("id",$lastid)
    //                                 ->bindValue("fieldname",$column)
    //                                 ->bindValue("prevalue", $values['prevalue'])
    //                                 ->bindValue("postvalue",$values['postvalue'])
    //                                 ->execute();
    //                     }

    //             //echo "Changes logged successfully.";
    //             }


    //     }
    // }
    public function approvelead($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //olde record
            $oldAttributes = Yii::$app->db->createCommand("select * from `leadinformation` where leadid=:leadid")
                ->bindValue(":leadid", $Record)
                ->queryOne();

            $id = Yii::$app->user->id;
            $creatorid = $oldAttributes['creatorid'];

            if (isset($_POST['approve_reason'])) {
                $newattributes = array("leadstatus" => $_POST['leadstatus_v'], "vm_comment" => $_POST['approve_reason']);
                $sql = "update leadinformation set vm_comment = :approve_reason,leadstatus = :leadstatus,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where leadid = :leadid";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":approve_reason", $_POST['approve_reason'])
                    ->bindValue(":leadstatus", $_POST['leadstatus_v'])
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $creatorid)
                    ->bindValue(":leadid", $Record)
                    ->execute();
                //make opportunity / sourcing deal/ account /contact temp =1
                //get aacount
                $acc = Leadinformation::find()->select('vendor')->where(['leadid' => $Record])->one();
                if (isset($acc['vendor'])) {
                    //update account
                    $sql_c = "update `vendor_account` set is_temp =0 where vendoraccid = :vendoraccid";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":vendoraccid", $acc['vendor'])->execute();
                }
                //get opportunity 
                $opp = Opportunity::find()->select('opportunity_id')->where(['leadid' => $Record])->one();
                if (isset($opp['opportunity_id'])) {
                    //update opportunity
                    $sql_c = "update `opportunity` set is_temp =0 where leadid = :leadid";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":leadid", $Record)->execute();
                    //get contact from contact role
                    $sql_c = "select contacts_id from opportunity_contact_role where opportunity_id = :opportunity_id";
                    $res = Yii::$app->db->createCommand($sql_c)->bindValue(":opportunity_id", $opp['opportunity_id'])->queryAll();

                    foreach ($res as $value) {

                        $sql_c = "update `contacts` set is_temp =0 where contacts_id = :contacts_id";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();

                        $sql_c = "update `opportunity_contact_role` set is_temp =0 where contacts_id = :contacts_id";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();
                    }
                }
                //get sourcing_deal 
                $opp = Sourcingdeal::find()->select('sourcingdeal_id')->where(['leadid' => $Record])->one();
                if (isset($opp['sourcingdeal_id'])) {
                    //update sourcing_deal
                    $sql_c = "update `sourcingdeal` set is_temp = 0 where leadid = :leadid";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":leadid", $Record)->execute();
                    //get contact from contact role
                    $sql_c = "select contacts_id from sourcingdeal_contact_role where sourcingdeal_id = :sourcingdeal_id";
                    $res = Yii::$app->db->createCommand($sql_c)->bindValue(":sourcingdeal_id", $opp['sourcingdeal_id'])->queryAll();
                    foreach ($res as $value) {
                        $sql_c = "update `contacts` set is_temp =0 where contacts_id = :contacts_id";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();

                        $sql_c = "update `sourcingdeal_contact_role` set is_temp =0 where contacts_id = :contacts_id";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();
                    }
                }
            } else if (isset($_POST['modify_reason'])) {
                $newattributes = array("leadstatus" => $_POST['leadstatus_m'], "vm_comment" => $_POST['modify_reason']);

                $sql = "update leadinformation set vm_comment   = :approve_reason,leadstatus = :leadstatus,modifiedtime = :modifiedtime,modifiedby = :modifiedby,send_for_approval=0,ownerid=:ownerid where leadid = :leadid";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":approve_reason", $_POST['modify_reason'])
                    ->bindValue(":leadstatus", $_POST['leadstatus_m'])
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $creatorid)
                    ->bindValue(":leadid", $Record)
                    ->execute();
            } else if (isset($_POST['reject_reason'])) {
                $newattributes = array("leadstatus" => $_POST['leadstatus_v'], "vm_comment" => $_POST['reject_reason'], "reject_reason" => $_POST['reject_type'], "other_reject_reason" => $_POST['other_reason']);

                $sql = "update leadinformation set vm_comment = :approve_reason, reject_reason = :reject_type, other_reject_reason=:other_reason, leadstatus = :leadstatus,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where leadid = :leadid";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":approve_reason", $_POST['reject_reason'])
                    ->bindValue(":reject_type", $_POST['reject_type'])
                    ->bindValue(":other_reason", $_POST['other_reason'])
                    ->bindValue(":reject_type", $_POST['reject_type'])
                    ->bindValue(":reject_type", $_POST['reject_type'])
                    ->bindValue(":leadstatus", $_POST['leadstatus_v'])
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $creatorid)
                    ->bindValue(":leadid", $Record)
                    ->execute();
                //delete opportunity / sourcing deal/ account /contact where temp =0 and lead = this lead
                //get aacount
                $acc = Leadinformation::find()->select('vendor')->where(['leadid' => $Record])->one();
                if (isset($acc['vendor'])) {
                    //update account
                    $sql_c = "delete from `vendor_account` where vendoraccid = :vendoraccid";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":vendoraccid", $acc['vendor'])->execute();
                }
                //get opportunity 
                $opp = Opportunity::find()->select('opportunity_id')->where(['leadid' => $Record])->one();
                if (isset($opp['opportunity_id'])) {

                    //get contact from contact role
                    $sql_c = "select contacts_id from opportunity_contact_role where opportunity_id = :opportunity_id";
                    $res = Yii::$app->db->createCommand($sql_c)->bindValue(":opportunity_id", $opp['opportunity_id'])->queryAll();
                    foreach ($res as $key => $value) {
                        $sql_c = "delete from `contacts`  where contacts_id = :contacts_id";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();

                        $sql_c = "delete from `opportunity_contact_role` where contacts_id = :contacts_id";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();
                    }
                    //delete opportunity
                    $sql_c = "delete from `opportunity` where leadid = :leadid";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":leadid", $Record)->execute();
                }
                //get sourcing_deal 
                $opp = Sourcingdeal::find()->select('sourcingdeal_id')->where(['leadid' => $Record])->one();
                if (isset($opp['sourcingdeal_id'])) {

                    //get contact from contact role
                    $sql_c = "select contacts_id from sourcingdeal_contact_role where sourcingdeal_id = :sourcingdeal_id";
                    $res = Yii::$app->db->createCommand($sql_c)->bindValue(":sourcingdeal_id", $opp['sourcingdeal_id'])->queryAll();
                    foreach ($res as $key => $value) {
                        $sql_c = "delete from `contacts`  where contacts_id = :contacts_id";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();

                        $sql_c = "delete from `sourcingdeal_contact_role` where contacts_id = :contacts_id";
                        Yii::$app->db->createCommand($sql_c)->bindValue(":contacts_id", $value['contacts_id'])->execute();
                    }
                    //delete sourcingdeal
                    $sql_c = "delete from `sourcingdeal` where leadid = :leadid";
                    Yii::$app->db->createCommand($sql_c)->bindValue(":leadid", $Record)->execute();
                }
            } else if (isset($_POST['reactivate_reason'])) {
                $newattributes = array("leadstatus" => $_POST['leadstatus_reactivate'], "vm_comment" => $_POST['reactivate_reason']);

                $sql = "update leadinformation set vm_comment = :approve_reason,leadstatus = :leadstatus,modifiedtime = :modifiedtime,modifiedby = :modifiedby,send_for_approval=0,ownerid=:ownerid where leadid = :leadid";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":approve_reason", $_POST['reactivate_reason'])
                    ->bindValue(":leadstatus", $_POST['leadstatus_reactivate'])
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $creatorid)
                    ->bindValue(":leadid", $Record)
                    ->execute();
            }
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function approvePurchaseOrder($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `purchase_order` where purchase_order_id=:purchase_order_id")
                ->bindValue(":purchase_order_id", $Record)
                ->queryOne();

            $id = Yii::$app->user->id;
            $creatorid = $oldAttributes['creatorid'];
            if (isset($_POST['approve_reason'])) {
                $newattributes = array("stage" => 3, "comment" => $_POST['approve_reason']);
                $sql = "update purchase_order set comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where purchase_order_id = :purchase_order_id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":approve_reason", $_POST['approve_reason'])
                    ->bindValue(":stage", 3)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $creatorid)
                    ->bindValue(":purchase_order_id", $Record)
                    ->execute();
            } else if (isset($_POST['modify_reason'])) {
                $newattributes = array("stage" => 4, "comment" => $_POST['modify_reason']);

                $sql = "update purchase_order set comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,submit_approval=0 where purchase_order_id = :purchase_order_id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":approve_reason", $_POST['modify_reason'])
                    ->bindValue(":stage", 4)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $creatorid)
                    ->bindValue(":purchase_order_id", $Record)
                    ->execute();
            }
            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    public function approvePickup($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `pickup` where pickup_id=:pickup_id")
                ->bindValue(":pickup_id", $Record)
                ->queryOne();
            if (empty($oldAttributes)) {
                throw new Exception("No record found");
            }
            $current_pickup_status = $oldAttributes["pickup_status"];
            $fe_user = $oldAttributes["fe_name"];
            $logistic_user = $oldAttributes["logistic_user"];
            $id = Yii::$app->user->id;
            if (isset($_POST['approve_reason'])) {
                if ($current_pickup_status == 10) {
                    if (empty($logistic_user)) {
                        throw new Exception("Logistic User not assigned");
                    }
                    $new_pickup_status = 12;
                    $newattributes = array("ownerid" => $logistic_user, "pickup_status" => $new_pickup_status, "remarks" => $_POST['approve_reason']);

                    $sql = "update pickup set ownerid=:ownerid,remarks = :remarks,pickup_status = :pickup_status,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":remarks", $_POST['approve_reason'])
                        ->bindValue(":pickup_status", $new_pickup_status)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $logistic_user)
                        ->bindValue(":id", $Record)
                        ->execute();
                } else if ($current_pickup_status == 11) {
                    if (empty($fe_user)) {
                        throw new Exception("FE User not assigned");
                    }
                    $new_pickup_status = 16;
                    $newattributes = array("ownerid" => $fe_user, "pickup_status" => $new_pickup_status, "vehicle_planning_remarks" => $_POST['approve_reason']);

                    $sql = "update pickup set vehicle_planning_remarks = :remarks,pickup_status = :pickup_status,ownerid=:ownerid,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":remarks", $_POST['approve_reason'])
                        ->bindValue(":pickup_status", $new_pickup_status)
                        ->bindValue(":ownerid", $fe_user)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":id", $Record)
                        ->execute();
                } else {
                    throw new Exception("Pickup status is not known");
                }
            } else if (isset($_POST['reject_reason'])) {
                if (empty($_POST['reject_reason'])) {
                    throw new Exception("Comment is required");
                }
                if ($current_pickup_status == 10) {
                    $newattributes = array("pickup_status" => 13, "remarks" => $_POST['reject_reason']);
                    $new_pickup_status = 13;
                    $sql = "update pickup set remarks = :remarks,pickup_status = :pickup_status,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                } else if ($current_pickup_status == 11) {
                    $newattributes = array("pickup_status" => 13, "vehicle_planning_remarks" => $_POST['reject_reason']);
                    $new_pickup_status = 17;
                    $sql = "update pickup set vehicle_planning_remarks = :remarks,pickup_status = :pickup_status,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                } else {
                    throw new Exception("Pickup status is not known");
                }
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":remarks", $_POST['reject_reason'])
                    ->bindValue(":pickup_status", $new_pickup_status)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":id", $Record)
                    ->execute();
            } else if (isset($_POST['modify_reason'])) {
                if (empty($_POST['modify_reason'])) {
                    throw new Exception("Comment is required");
                }
                if ($current_pickup_status == 10) {
                    if (empty($fe_user)) {
                        throw new Exception("FE User not assigned");
                    }
                    $newattributes = array("ownerid" => $fe_user, "pickup_status" => 14, "remarks" => $_POST['modify_reason']);
                    $new_pickup_status = 14;
                    $sql = "update pickup set remarks = :remarks,pickup_status = :pickup_status,ownerid=:ownerid,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":remarks", $_POST['modify_reason'])
                        ->bindValue(":pickup_status", $new_pickup_status)
                        ->bindValue(":ownerid", $fe_user)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":id", $Record)
                        ->execute();
                } else if ($current_pickup_status == 11) {
                    if (empty($logistic_user)) {
                        throw new Exception("Logistic User not assigned");
                    }
                    $newattributes = array("pickup_status" => 18, "vehicle_planning_remarks" => $_POST['modify_reason']);
                    $new_pickup_status = 18;
                    $sql = "update pickup set vehicle_planning_remarks = :remarks,pickup_status = :pickup_status,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":remarks", $_POST['modify_reason'])
                        ->bindValue(":pickup_status", $new_pickup_status)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":id", $Record)
                        ->execute();
                } else {
                    throw new Exception("Pickup status is not known");
                }
            } else if (isset($_POST['start_pickup']) && $_POST["start_pickup"] == "Yes") {
                if ($current_pickup_status == 16) {
                    $new_pickup_status = 5;
                    $newattributes = array("pickup_status" => $new_pickup_status);

                    $sql = "update pickup set pickup_status = :pickup_status,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":pickup_status", $new_pickup_status)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":id", $Record)
                        ->execute();
                } else {
                    throw new Exception("Only record with current pickup status as 'Vehicle Planning Approved' can be accepted for this action");
                }
            } else if (isset($_POST['pickup_completed']) && $_POST["pickup_completed"] == "Yes") {
                if ($current_pickup_status == 5) {
                    $e_waste_category = false;
                    $general_category = false;
                    $battery_category = false;
                    $dismentalling_category = false;
                    // check asset verification data
                    $asset_verification_query = PickupAssetDetail::find()->where(['pickup_id' => $Record, 'deleted' => 0]);
                    $asset_verification_count = $asset_verification_query->count();
                    if ($asset_verification_count > 0) {
                        $asset_verification_data = $asset_verification_query->all();
                        foreach ($asset_verification_data as $asset) {
                            if (empty($asset->picked_qty)) {
                                throw new Exception("Picked Qty is required for all items in the 'Asset Verification' section");
                            }
                            $category = $asset->category;
                            if ($category) {
                                $cleaned_category = preg_replace('/[^a-zA-Z0-9]/', '', $category);
                                $cleaned_category = strtolower($cleaned_category);
                                if ($cleaned_category == "ewaste" || $cleaned_category == 1)
                                    $e_waste_category = true;
                                if ($cleaned_category == "generalwaste" || $cleaned_category == 12)
                                    $general_category = true;
                                if ($cleaned_category == "batterywaste" || $cleaned_category == 4)
                                    $battery_category = true;
                                if ($cleaned_category == "dismentling")
                                    $dismentalling_category = true;
                            }
                        }
                    } else {
                        throw new Exception("Asset verification data is missing for this pickup");
                    }

                    // check Detail packing list data
                    $detail_packaging_query = DetailsPackingList::find()->where(['pickup_id' => $Record, 'deleted' => 0]);
                    $detail_packaging_data_count = $detail_packaging_query->count();
                    if ($detail_packaging_data_count > 0) {
                        $detail_packaging_data = $detail_packaging_query->all();
                        foreach ($detail_packaging_data as $dpl) {
                            if (empty($dpl->box_number)) {
                                throw new Exception("Box Number is required for all items in the 'Details Packing List' section");
                            }
                            if (empty($dpl->sub_category)) {
                                throw new Exception("Sub Category is required for all items in the 'Details Packing List' section");
                            }
                            if (empty($dpl->condition)) {
                                throw new Exception("Condition is required for all items in the 'Details Packing List' section");
                            }
                            if (empty($dpl->count)) {
                                throw new Exception("Count is required for all items in the 'Details Packing List' section");
                            }
                            if (empty($dpl->uom)) {
                                throw new Exception("UOM is required for all items in the 'Details Packing List' section");
                            }
                            if (empty($dpl->upload_image)) {
                                throw new Exception("Upload Image is required for all items in the 'Details Packing List' section");
                            }
                            if (empty($dpl->vehicle_number)) {
                                throw new Exception("Vehicle Number is required for all items in the 'Details Packing List' section");
                            }
                        }
                    } else {
                        throw new Exception("'Details Packing List' is missing for this pickup");
                    }
                    // check FE documents
                    $pickup_document_fe_query = PickupDocumentDetails::find()->where(['pickup_id' => $Record, 'deleted' => 0]);
                    $pickup_document_fe_count = $pickup_document_fe_query->count();
                    if ($pickup_document_fe_count > 0) {
                        $pickup_document_fe_data = $pickup_document_fe_query->all();
                        foreach ($pickup_document_fe_data as $fe_document) {
                            if (empty($fe_document->document_for_pickup)) {
                                throw new Exception("'Document For Pickup' is required for all items in the 'Document Name (FE)' section");
                            }
                            if (empty($fe_document->document_attached)) {
                                throw new Exception("'Status' is required for all items in the 'Document Name (FE)' section");
                            }
                            //make attachment mandatory only for status = yes
                            if ($fe_document->document_attached == 2 && empty($fe_document->attachment)) {
                                throw new Exception("'Attachment' is required for all items for which status is Yes in the 'Document Name (FE)' section");
                            }
                        }
                    } else {
                        throw new Exception("Document Name (FE) data is missing for this pickup");
                    }

                    // check shipped details
                    $shipped_detail_query = ShippedDetails::find()->where(['pickup_id' => $Record, 'deleted' => 0]);
                    $shipped_detail_count = $shipped_detail_query->count();
                    if ($shipped_detail_count > 0) {
                        $shipped_detail_data = $shipped_detail_query->all();
                        foreach ($shipped_detail_data as $sd) {
                            if (empty($sd->transporter_name)) {
                                throw new Exception("Transporter Name is required for all items in the 'Shipped Details' section");
                            }
                            if (empty($sd->vehicle_size)) {
                                throw new Exception("Vehicle Size is required for all items in the 'Shipped Details' section");
                            }
                            if (empty($sd->shippment_mode)) {
                                throw new Exception("Shippment Mode is required for all items in the 'Shipped Details' section");
                            }
                            if (empty($sd->docket_number)) {
                                throw new Exception("Docket Number is required for all items in the 'Shipped Details' section");
                            }
                            if (empty($sd->seal_number)) {
                                throw new Exception("Seal Number is required for all items in the 'Shipped Details' section");
                            }
                            if (empty($sd->shipped_date)) {
                                throw new Exception("Shipped Date is required for all items in the 'Shipped Details' section");
                            }
                            if (empty($sd->estimate_delivery_date)) {
                                throw new Exception("Estimate Delivery Date is required for all items in the 'Shipped Details' section");
                            }
                            /*As per point no 49 this is not mandatory here
                            if (empty($sd->delivery_date)) {
                                throw new Exception("Delivery Date is required for all items in the 'Shipped Details' section");
                            }
                            */
                            if (empty($sd->status)) {
                                throw new Exception("Status is required for all items in the 'Shipped Details' section");
                            }
                        }
                    } else {
                        throw new Exception("'Shipped Details' is missing for this pickup");
                    }
                    // check compliance document
                    if (!empty($oldAttributes)) {
                        /* this is old dumb logic and no one gets it
                        if (($e_waste_category || $general_category || $dismentalling_category) && empty($oldAttributes["form6_unsigned_copy"])) {
                            throw new Exception("'Form 6 unsigned Copy' is not available. Please click on the Form 6 button to generate it");
                        }

                        if (($e_waste_category || $general_category || $dismentalling_category) && empty($oldAttributes["form6_stamped_copy"])) {
                            throw new Exception("'Form 6 Stamped Copy' is not available");
                        }
                        if ($battery_category && empty($oldAttributes["form10_unsigned_copy"])) {
                            throw new Exception("'Form 10 unsigned Copy' is not available. Please click on the Form 10 button to generate it");
                        }
                        if ($battery_category && empty($oldAttributes["form10_stamped_copy"])) {
                            throw new Exception("'Form 10 Stamped Copy' is not available");
                        }
                        */
                        $waste_catgory_sql = "SELECT products.waste_catagory,
                            prod_waste_catagory.waste_catagory_value
                            FROM pickup_asset_detail 
                            left join products on products.products_id = pickup_asset_detail.porduct_name 
                            left join prod_waste_catagory on prod_waste_catagory.waste_catagory_id = products.waste_catagory
                            WHERE pickup_asset_detail.pickup_id = :record_id and pickup_asset_detail.deleted=0";
                        $connection = Yii::$app->db;
                        $assets_command = $connection->createCommand($waste_catgory_sql)->bindValues([":record_id" => $Record]);
                        $pickup_assets_waste_categories = $assets_command->queryAll();
                        if (!empty($pickup_assets_waste_categories)) {
                            foreach ($pickup_assets_waste_categories as $i => $pa) {
                                $waste_category = $pa["waste_catagory_value"];
                                if ($waste_category == "G-Waste" || $waste_category == "E-Waste") {
                                    if (empty($oldAttributes["form6_unsigned_copy"])) {
                                        throw new Exception("'Form 6 unsigned Copy' is not available. Please click on the Form 6 button to generate it");
                                    }
                                    if (empty($oldAttributes["form6_stamped_copy"])) {
                                        throw new Exception("'Form 6 Stamped Copy' is not available. Please upload it under the 'COMPLAINCE DOCUMENTS' section");
                                    }
                                } else if ($waste_category == "H-Waste") {
                                    if (empty($oldAttributes["form10_unsigned_copy"])) {
                                        throw new Exception("'Form 10 unsigned Copy' is not available. Please click on the Form 10 button to generate it");
                                    }
                                    if (empty($oldAttributes["form10_stamped_copy"])) {
                                        throw new Exception("'Form 10 Stamped Copy' is not available. Please upload it under the 'COMPLAINCE DOCUMENTS' section");
                                    }
                                }
                            }
                        }
                        // if (empty($oldAttributes["upload_unsigned_copy"])) {
                        //     throw new Exception("'Form 10 Lubeoil Unsigned Copy' is not available");
                        // }
                        // if (empty($oldAttributes["upload_stamped_copy"])) {
                        //     throw new Exception("'Form 10 Lubeoil Stamped Copy' is not available");
                        // }
                        /* It will not be generated from here as per point no 50
                        if (empty($oldAttributes["green_certificate"])) {
                            throw new Exception("'Green Certificate' is not available.. Please click on the Green Certificate button to generate it");
                        }*/
                    } else {
                        throw new Exception("Document Name (FE) data is missing for this pickup");
                    }
                    $newattributes = array("pickup_status" => 6);
                    $new_pickup_status = 6;
                    $sql = "update pickup set pickup_status = :pickup_status,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":pickup_status", $new_pickup_status)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":id", $Record)
                        ->execute();
                    //update certificate reporting table
                    $pickup_modal = new Pickup();
                    $pickup_modal->save_vp_certificate($RecordId);
                } else {
                    throw new Exception("Only 'Pickup in process/ Activity in process' record can be accepted for this action");
                }
            } else if (isset($_POST['submit_for_logistrics']) && $_POST["submit_for_logistrics"] == "Yes") {
                if ($current_pickup_status != 2) {
                    throw new Exception("Invalid Action. Only record with status as 'Pickup Created' can be accepted for this action");
                }
                $pickup_location = $oldAttributes["pickup_location"] ?? "";
                $delivery_location = $oldAttributes["delivery_location"] ?? "";
                $preferred_pickup_date = $oldAttributes["preferred_pickup_date"] ?? "";
                $actual_pickup_date = $oldAttributes["actual_pickup_date"] ?? "";
                $scheduled_pickup_date = $oldAttributes["scheduled_pickup_date"] ?? "";
                $doc_received = $oldAttributes["doc_received"] ?? "";
                $logistic_user = $oldAttributes["logistic_user"] ?? "";
                if (empty($pickup_location)) {
                    throw new Exception("'Pickup Location' undeer 'PICKUP ADDRESS' is not available");
                }
                if (empty($delivery_location)) {
                    throw new Exception("'Delivery Location' under 'DELIVERY ADDRESS' is not available");
                }
                if (empty($preferred_pickup_date)) {
                    throw new Exception("'Preferred Pickup Date' under 'PICKUP INSTRUCTIONS' is not available");
                }
                /* as per point no 28
                Now it will be filled by logistic user
                if (empty($actual_pickup_date)) {
                    throw new Exception("'Actual Pickup Date' under 'PICKUP INSTRUCTIONS' is not available");
                }
                */

                if (empty($doc_received)) {
                    throw new Exception("'Document Received' under 'PICKUP INSTRUCTIONS' is not available");
                }
                if (empty($logistic_user)) {
                    throw new Exception("'Logistic User' under 'PICKUP INSTRUCTIONS' is not available");
                }
                if (empty($logistic_user)) {
                    throw new Exception("Logistic User is not set");
                }
                $new_pickup_status = 3;

                $newattributes = array("pickup_status" => $new_pickup_status, "ownerid" => $logistic_user, "pickup_submitted_for_logistics" => 1);

                $sql = "update pickup set pickup_status = :pickup_status,ownerid=:ownerid,pickup_submitted_for_logistics=:pickup_submitted_for_logistics,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":pickup_status", $new_pickup_status)
                    ->bindValue(":ownerid", $logistic_user)
                    ->bindValue(":pickup_submitted_for_logistics", 1)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":id", $Record)
                    ->execute();
            } else if (isset($_POST['pickup_schedule']) && $_POST["pickup_schedule"] == "Yes") {
                if ($current_pickup_status != 12) {
                    throw new Exception("Invalid Action. Only record with status as 'Packing Material Approved' can be accepted for this action");
                }
                /* This is moved to pickup instruction now
                $vehicle_planning_query = VehiclePlanning::find()->where(['pickup_id' => $Record, 'deleted' => 0]);
                $vehicle_planning_count = $vehicle_planning_query->count();
                if ($vehicle_planning_count > 0) {
                    $vehicle_planning_data = $vehicle_planning_query->all();
                    foreach ($vehicle_planning_data as $vp) {
                        if (empty($vp->schedule_pickup_date)) {
                            throw new Exception("'Schedule Pickup Date' is required for all records in the 'VEHICAL PLANNING' section");
                        }
                    }
                } else {
                    throw new Exception("Schedule Pickup Date under 'VEHICLE PLANNING' is required");
                }
                */
                $scheduled_pickup_date = $oldAttributes["scheduled_pickup_date"] ?? "";
                if (empty($scheduled_pickup_date)) {
                    throw new Exception("'Schedule Pickup Date' is required under 'PICKUP INSTRUCTIONS' ");
                }
                $new_pickup_status = 15;
                if (empty($logistic_user)) {
                    throw new Exception("'Logistic User' is not set");
                }
                $newattributes = array("pickup_status" => $new_pickup_status, "ownerid" => $logistic_user, "pickup_schedule" => 1);

                $sql = "update pickup set pickup_status = :pickup_status,ownerid=:ownerid,pickup_schedule=:pickup_schedule,modifiedtime = :modifiedtime,modifiedby = :modifiedby where pickup_id = :id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":pickup_status", $new_pickup_status)
                    ->bindValue(":ownerid", $logistic_user)
                    ->bindValue(":pickup_schedule", 1)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":id", $Record)
                    ->execute();
            } else {
                throw new Exception("Invalid Action");
            }
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return $e->getMessage();
        }
    }
    public function approveSourcingDeal($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `sourcingdeal` where sourcingdeal_id=:sourcingdeal_id")
                ->bindValue(":sourcingdeal_id", $Record)
                ->queryOne();
            $creatorid = $oldAttributes['creatorid'];

            $id = Yii::$app->user->id;
            if (isset($_POST['approve_reason'])) {
                $newattributes = array('probability' => '50', "stage" => 10, "ceo_comment" => $_POST['approve_reason']);
                $sql = "update sourcingdeal set ownerid=:ownerid,ceo_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,probability='50' where sourcingdeal_id = :sourcingdeal_id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":approve_reason", $_POST['approve_reason'])
                    ->bindValue(":stage", 10)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":sourcingdeal_id", $Record)
                    ->bindValue(":ownerid", $creatorid)
                    ->execute();
            } else if (isset($_POST['reject_reason'])) {
                $newattributes = array('probability' => '0', "stage" => 28, "ceo_comment" => $_POST['reject_reason']);

                $sql = "update sourcingdeal set ownerid=:ownerid,ceo_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,probability=0 where sourcingdeal_id = :sourcingdeal_id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":approve_reason", $_POST['reject_reason'])
                    ->bindValue(":stage", 28)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":sourcingdeal_id", $Record)
                    ->bindValue(":ownerid", $creatorid)
                    ->execute();
            }
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function approveSalesorderdit($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `salesorder_dit` where salesorder_dit_id=:salesorder_dit_id")
                ->bindValue(":salesorder_dit_id", $Record)
                ->queryOne();

            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['so_stage'];
            $creatorid = $oldAttributes['creatorid'];

            if (isset($_POST['reject_reason'])) {
                $stage = 1;//draft
                $ownerid = $creatorid;
                $submit_approval = 0;
            }
            if ($stageold == '2') {
                //first level approval
                if (isset($_POST['approve_reason'])) {
                    $stage = 3; //second level approval
                    $ownerid = $creatorid;
                    //fin executive to be next approver
                    //assign to finance executive
                    $sql = "select id from user 
                    join user2role on user2role.userid = user.id
                    where user2role.roleid='H87' and deleted =0 and status=10 order by Rand() limit 1";
                    $userresult = Yii::$app->db->createCommand($sql)
                        ->queryOne();
                    if ($userresult) {
                        $ownerid = $userresult['id'];
                    }
                    $newattributes = array("so_stage" => $stage, "first_approval_comment" => $_POST['approve_reason']);
                    $sql = "update salesorder_dit set first_approval_comment = :approve_reason,so_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where salesorder_dit_id = :salesorder_dit_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['approve_reason'])
                        ->bindValue(":stage", $stage)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":salesorder_dit_id", $Record)
                        ->execute();

                    $paymentinfo = SalesorderDit::find()->select('salesorder_dit_no')->where(['salesorder_dit_id' => $Record])->one();
                    $notification = new Notifications();
                    $notification->userid = $ownerid;
                    $notification->message = "Sales Order No " . $paymentinfo['salesorder_dit_no'] . " has been submitted for approval. Please check";
                    $notification->read_status = 0; // Unread notification
                    $notification->display_status = 0;
                    $notification->source_link = Yii::$app->request->baseUrl . "/" . $this->moduleName . "/detail?Record=" . $Record;
                    ;
                    $notification->createdtime = date('Y-m-d H:i:s');
                    $notification->modifiedtime = date('Y-m-d H:i:s');
                    if (!$notification->save()) {
                        echo 'save failed';
                        exit;
                    }
                } else if (isset($_POST['reject_reason'])) {
                    $newattributes = array("so_stage" => $stage, "first_approval_comment" => $_POST['reject_reason']);

                    $sql = "update salesorder_dit set first_approval_comment = :approve_reason,so_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,send_for_approval=0 where salesorder_dit_id = :salesorder_dit_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['reject_reason'])
                        ->bindValue(":stage", 1)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":salesorder_dit_id", $Record)
                        ->execute();
                }
            } else {
                //second level approval
                if (isset($_POST['approve_reason'])) {
                    $stage = 4; //payment approved
                    $ownerid = $creatorid;
                    $newattributes = array("so_stage" => $stage, "second_approval_comment" => $_POST['approve_reason']);
                    $sql = "update salesorder_dit set second_approval_comment = :approve_reason,so_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where salesorder_dit_id = :salesorder_dit_no";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['approve_reason'])
                        ->bindValue(":stage", $stage)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":salesorder_dit_no", $Record)
                        ->execute();
                } else if (isset($_POST['reject_reason'])) {
                    $newattributes = array("so_stage" => $stage, "second_approval_comment" => $_POST['reject_reason']);

                    $sql = "update salesorder_dit set second_approval_comment = :approve_reason,so_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,send_for_approval=0 where salesorder_dit_id = :salesorder_dit_no";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['reject_reason'])
                        ->bindValue(":stage", 1)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":salesorder_dit_no", $Record)
                        ->execute();
                }
            }

            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    public function approvePurchaseorderdit($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `purchase_order_dit` where purchaseorder_dit_id=:purchaseorder_dit_id")
                ->bindValue(":purchaseorder_dit_id", $Record)
                ->queryOne();

            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['stage'];
            $creatorid = $oldAttributes['creatorid'];
            $purchaseorder_dit_no = $oldAttributes['purchaseorder_dit_no'];
            $total = $oldAttributes['total'];


            if (isset($_POST['reject_reason'])) {
                $stage = 1;//draft
                $ownerid = $creatorid;
                $submit_approval = 0;
            }
            if ($stageold == '2') {

                //first level approval
                if (isset($_POST['approve_reason'])) {

                    if ($total > 20000000)//more than 2 cr
                    {

                        $stage = 3; //second level approval
                        $ownerid = $creatorid;
                        //fin executive to be next approver
                        //assign to c level
                        $sql = "select id from user 
                        join user2role on user2role.userid = user.id
                        where user2role.roleid='H84' and deleted =0 and status=10 order by Rand() limit 1";
                        $userresult = Yii::$app->db->createCommand($sql)
                            ->queryOne();
                        if ($userresult) {
                            $ownerid = $userresult['id'];
                        }
                        $newattributes = array("ownerid" => $ownerid, "stage" => $stage, "first_approval_comment" => $_POST['approve_reason']);
                        $sql = "update purchase_order_dit set first_approval_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where purchaseorder_dit_id = :purchaseorder_dit_id";
                        Yii::$app->db->createCommand($sql)
                            ->bindValue(":approve_reason", $_POST['approve_reason'])
                            ->bindValue(":stage", $stage)
                            ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                            ->bindValue(":modifiedby", $id)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":purchaseorder_dit_id", $Record)
                            ->execute();


                        $message = "Purchase Order No " . $purchaseorder_dit_no . " has been submitted for approval. Please check";

                        $this->sendnotification($ownerid, $message, $this->moduleName, $Record);
                    } else {
                        //direct approve
                        $stage = 4; // approved

                        $newattributes = array("ownerid" => $creatorid, "stage" => $stage, "first_approval_comment" => $_POST['approve_reason']);
                        $sql = "update purchase_order_dit set first_approval_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where purchaseorder_dit_id = :purchaseorder_dit_id";
                        Yii::$app->db->createCommand($sql)
                            ->bindValue(":approve_reason", $_POST['approve_reason'])
                            ->bindValue(":stage", $stage)
                            ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                            ->bindValue(":modifiedby", $id)
                            ->bindValue(":ownerid", $creatorid)
                            ->bindValue(":purchaseorder_dit_id", $Record)
                            ->execute();

                        $message = "Purchase Order No " . $purchaseorder_dit_no . " has been Approved. Please check";

                        $this->sendnotification($creatorid, $message, $this->moduleName, $Record);
                    }
                } else if (isset($_POST['reject_reason'])) {
                    $newattributes = array("ownerid" => $creatorid, "stage" => $stage, "first_approval_comment" => $_POST['reject_reason']);

                    $sql = "update purchase_order_dit set first_approval_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,send_for_approval=0 where purchaseorder_dit_id = :purchaseorder_dit_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['reject_reason'])
                        ->bindValue(":stage", 1)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":purchaseorder_dit_id", $Record)
                        ->execute();


                    $message = "Purchase Order No " . $purchaseorder_dit_no . " has been rejected. Please check";

                    $this->sendnotification($creatorid, $message, $this->moduleName, $Record);
                }
            } else {

                //second level approval
                if (isset($_POST['approve_reason'])) {
                    $stage = 4; // approved
                    //assign to procurement team executive
                    $sql = "select id from user 
                    join user2role on user2role.userid = user.id
                    where user2role.roleid='H68' and deleted =0 and status=10 order by Rand() limit 1";
                    $userresult = Yii::$app->db->createCommand($sql)
                        ->queryOne();
                    if ($userresult) {
                        $ownerid = $userresult['id'];
                    }
                    $newattributes = array("ownerid" => $ownerid, "stage" => $stage, "second_approval_comment" => $_POST['approve_reason']);
                    $sql = "update purchase_order_dit set second_approval_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where purchaseorder_dit_id = :purchaseorder_dit_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['approve_reason'])
                        ->bindValue(":stage", $stage)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":purchaseorder_dit_id", $Record)
                        ->execute();

                    $message = "Purchase Order No " . $purchaseorder_dit_no . " has been Approved. Please check";

                    $this->sendnotification($ownerid, $message, $this->moduleName, $Record);
                } else if (isset($_POST['reject_reason'])) {
                    $newattributes = array("ownerid" => $ownerid, "stage" => $stage, "second_approval_comment" => $_POST['reject_reason']);

                    $sql = "update purchase_order_dit set second_approval_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,send_for_approval=0 where purchaseorder_dit_id = :purchaseorder_dit_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['reject_reason'])
                        ->bindValue(":stage", 1)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $creatorid)
                        ->bindValue(":purchaseorder_dit_id", $Record)
                        ->execute();

                    $message = "Purchase Order No " . $purchaseorder_dit_no . " has been rejected. Please check";

                    $this->sendnotification($creatorid, $message, $this->moduleName, $Record);
                }
            }
            // die;

            // print_r($newattributes);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function approvePayments($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `payments` where payments_id=:payments_id")
                ->bindValue(":payments_id", $Record)
                ->queryOne();

            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['stage'];
            $creatorid = $oldAttributes['creatorid'];

            if (isset($_POST['reject_reason'])) {
                $stage = 4;
                $ownerid = $creatorid;
                $submit_approval = 0;
            }
            if ($stageold == '2') {
                //first level approval
                if (isset($_POST['approve_reason'])) {
                    $stage = 3; //second level approval
                    $ownerid = $creatorid;
                    //fin executive to be next approver
                    //assign to finance executive
                    $sql = "select id from user 
                    join user2role on user2role.userid = user.id
                    where user2role.roleid='H63' and deleted =0 and status=10 order by Rand() limit 1";
                    $userresult = Yii::$app->db->createCommand($sql)
                        ->queryOne();
                    if ($userresult) {
                        $ownerid = $userresult['id'];
                    }
                    $newattributes = array("stage" => $stage, "comment" => $_POST['approve_reason']);
                    $sql = "update payments set first_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where payments_id = :payments_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['approve_reason'])
                        ->bindValue(":stage", $stage)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":payments_id", $Record)
                        ->execute();

                    $paymentinfo = Payments::find()->select('payment_no')->where(['payments_id' => $Record])->one();
                    $notification = new Notifications();
                    $notification->userid = $ownerid;
                    $notification->message = "Payment No " . $paymentinfo['payment_no'] . " has been submitted for approval. Please check";
                    $notification->read_status = 0; // Unread notification
                    $notification->display_status = 0;
                    $notification->source_link = Yii::$app->request->baseUrl . "/" . $this->moduleName . "/detail?Record=" . $Record;
                    ;
                    $notification->createdtime = date('Y-m-d H:i:s');
                    $notification->modifiedtime = date('Y-m-d H:i:s');
                    if (!$notification->save()) {
                        echo 'save failed';
                        exit;
                    }
                } else if (isset($_POST['reject_reason'])) {
                    $newattributes = array("stage" => $stage, "comment" => $_POST['reject_reason']);

                    $sql = "update payments set first_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,submit_approval=0 where payments_id = :payments_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['reject_reason'])
                        ->bindValue(":stage", 4)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":payments_id", $Record)
                        ->execute();
                }
            } else {
                //second level approval
                if (isset($_POST['approve_reason'])) {
                    $stage = 5; //payment approved
                    $ownerid = $creatorid;
                    $newattributes = array("stage" => $stage, "comment" => $_POST['approve_reason']);
                    $sql = "update payments set second_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where payments_id = :payments_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['approve_reason'])
                        ->bindValue(":stage", $stage)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":payments_id", $Record)
                        ->execute();
                } else if (isset($_POST['reject_reason'])) {
                    $newattributes = array("stage" => $stage, "comment" => $_POST['reject_reason']);

                    $sql = "update payments set second_comment = :approve_reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,submit_approval=0 where payments_id = :payments_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['reject_reason'])
                        ->bindValue(":stage", 4)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":payments_id", $Record)
                        ->execute();
                }
            }

            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    public function approvequotesdit($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `quotes_dit` where quotes_dit_id=:quotes_dit_id")
                ->bindValue(":quotes_dit_id", $Record)
                ->queryOne();

            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['quote_stage'];
            $creatorid = $oldAttributes['creatorid'];
            $margin = $oldAttributes['margin'];

            if (isset($_POST['reject_reason'])) {
                $stage = 5;
                $ownerid = $creatorid;
                $submit_approval = 0;
            }

            if ($stageold == '2') {
                //first level approval
                if (isset($_POST['approve_reason'])) {
                    $stage = 3; //second level approval
                    $ownerid = $creatorid;
                    if ($margin < 5) {
                        //fin executive to be next approver
                        //assign to c level
                        $sql = "select id from user 
                        join user2role on user2role.userid = user.id
                        where user2role.roleid='H84' and deleted =0 and status=10 ORDER BY RAND() limit 1";
                        $userresult = Yii::$app->db->createCommand($sql)
                            ->queryOne();
                        if ($userresult) {
                            $ownerid = $userresult['id'];
                        }
                        $newattributes = array("ownerid" => $ownerid, "quote_stage" => $stage, "first_comment" => $_POST['approve_reason']);
                        $sql = "update quotes_dit set first_comment = :approve_reason,quote_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where quotes_dit_id = :quotes_dit_id";

                        Yii::$app->db->createCommand($sql)
                            ->bindValue(":approve_reason", $_POST['approve_reason'])
                            ->bindValue(":stage", $stage)
                            ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                            ->bindValue(":modifiedby", $id)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":quotes_dit_id", $Record)
                            ->execute();

                        $paymentinfo = QuotesDit::find()->select('quotes_dit_no')->where(['quotes_dit_id' => $Record])->one();
                        $notification = new Notifications();
                        $notification->userid = $ownerid;
                        $notification->message = "Quote No " . $paymentinfo['quotes_dit_no'] . " has been submitted for approval. Please check";
                        $notification->read_status = 0; // Unread notification
                        $notification->display_status = 0;
                        $notification->source_link = Yii::$app->request->baseUrl . "/" . $this->moduleName . "/detail?Record=" . $Record;
                        ;
                        $notification->createdtime = date('Y-m-d H:i:s');
                        $notification->modifiedtime = date('Y-m-d H:i:s');
                        if (!$notification->save()) {
                            echo 'save failed';
                            exit;
                        }
                    } else {
                        $stage = 4; // approved
                        $ownerid = $creatorid;
                        $newattributes = array("ownerid" => $ownerid, "quote_stage" => $stage, "first_comment" => $_POST['approve_reason']);

                        $sql = "update quotes_dit set first_comment = :approve_reason,quote_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where quotes_dit_id = :quotes_dit_id";
                        Yii::$app->db->createCommand($sql)
                            ->bindValue(":approve_reason", $_POST['approve_reason'])
                            ->bindValue(":stage", $stage)
                            ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                            ->bindValue(":modifiedby", $id)
                            ->bindValue(":ownerid", $ownerid)
                            ->bindValue(":quotes_dit_id", $Record)
                            ->execute();

                    }
                } else if (isset($_POST['reject_reason'])) {
                    $newattributes = array("ownerid" => $ownerid, "quote_stage" => $stage, "first_comment" => $_POST['reject_reason']);

                    $sql = "update quotes_dit set first_comment = :approve_reason,quote_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,send_for_approval =0 where quotes_dit_id = :quotes_dit_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['reject_reason'])
                        ->bindValue(":stage", 5)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":quotes_dit_id", $Record)
                        ->execute();
                }
            } else {
                //second level approval
                if (isset($_POST['approve_reason'])) {
                    $stage = 4; // approved
                    $ownerid = $creatorid;
                    $newattributes = array("ownerid" => $ownerid, "quote_stage" => $stage, "second_comment" => $_POST['approve_reason']);

                    $sql = "update quotes_dit set second_comment = :approve_reason,quote_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where quotes_dit_id = :quotes_dit_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['approve_reason'])
                        ->bindValue(":stage", $stage)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":quotes_dit_id", $Record)
                        ->execute();
                } else if (isset($_POST['reject_reason'])) {
                    $newattributes = array("ownerid" => $ownerid, "quote_stage" => $stage, "second_comment" => $_POST['reject_reason']);


                    $sql = "update quotes_dit set second_comment = :approve_reason,quote_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,send_for_approval =0 where quotes_dit_id = :quotes_dit_id";
                    Yii::$app->db->createCommand($sql)
                        ->bindValue(":approve_reason", $_POST['reject_reason'])
                        ->bindValue(":stage", 5)
                        ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                        ->bindValue(":modifiedby", $id)
                        ->bindValue(":ownerid", $ownerid)
                        ->bindValue(":quotes_dit_id", $Record)
                        ->execute();
                }
            }
            //echo $stage;die;
            if ($stage == 4) {
                //approved stage then change opportunity stage
                $opportunity_stage = 10;//quote approved
                $related_to_id = $oldAttributes['opportunity_name'];

                $oldAttributessrc = Yii::$app->db->createCommand("select * from `opportunity` where opportunity_id=:opportunity_id")
                    ->bindValue(":opportunity_id", $related_to_id)
                    ->queryOne();
                //update sourcing deal
                //    echo  $sql = "Update opportunity set opportunity_stage = $opportunity_stage where opportunity_id = $related_to_id";die;
                $sql = "Update opportunity set opportunity_stage = :srcstage where opportunity_id = :opportunity_id";
                $updt = Yii::$app->db->createCommand($sql)
                    ->bindValue(":srcstage", $opportunity_stage)
                    ->bindValue(":opportunity_id", $related_to_id)
                    ->execute();

                $newattributessrc = array("opportunity_stage" => $opportunity_stage);

                $modlog->auditlog($oldAttributessrc, $newattributessrc, "opportunities", $related_to_id, 2, Yii::$app->user->id);
            }


            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    public function approveOpportunity($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `opportunity` where opportunity_id=:opportunity_id")
                ->bindValue(":opportunity_id", $Record)
                ->queryOne();

            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['opportunity_stage'];
            $creatorid = $oldAttributes['creatorid'];

            if (isset($_POST['reject_reason'])) {
                $stage = 1;//changed to prospect on 4th sept 9;//lost
                $ownerid = $creatorid;
                $reason = $_POST['reject_reason'];
                $submit_for_screening =0;
            }
            if (isset($_POST['approve_reason'])) {
                $stage = 3;//qualified
                $ownerid = $creatorid;
                $reason = $_POST['approve_reason'];
            }
            
            $newattributes = array("opportunity_stage" => $stage, "comment" => $reason, "ownerid" => $oldAttributes['ownerid']);

            if (isset($_POST['reject_reason'])) {
                $sql = "update opportunity set comments = :reason,opportunity_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,submit_for_screening=0 where opportunity_id = :opportunity_id";
            Yii::$app->db->createCommand($sql)
                ->bindValue(":reason", $reason)
                ->bindValue(":stage", $stage)
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":ownerid", $ownerid)
                ->bindValue(":opportunity_id", $Record)
                ->execute();
            }
            else{
            $sql = "update opportunity set comments = :reason,opportunity_stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where opportunity_id = :opportunity_id";
            Yii::$app->db->createCommand($sql)
                ->bindValue(":reason", $reason)
                ->bindValue(":stage", $stage)
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":ownerid", $ownerid)
                ->bindValue(":opportunity_id", $Record)
                ->execute();
        }


            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function approveContract($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `contracts` where contract_id=:contract_id")
                ->bindValue(":contract_id", $Record)
                ->queryOne();
            // echo "<pre>";print_r($oldAttributes);die;
            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['contract_status'];
            $creatorid = $oldAttributes['creatorid'];

            $contract_status_text = '';
            if (isset($_POST['reject_reason'])) {
                $stage = 5;//changes required
                $ownerid = $creatorid;
                $reason = $_POST['reject_reason'];
                $contract_status_text = ' Rejected';
                //user review contract and resend to review
                $send_for_review = 0;
            }
            if (isset($_POST['approve_reason'])) {
                $stage = 3;//approved
                $ownerid = $creatorid;
                $reason = $_POST['approve_reason'];
                $contract_status_text = ' Approved';
                //no need to change
                $send_for_review = 1;
            }

            $newattributes = array("contract_status" => $stage, "comment" => $reason, "ownerid" => $oldAttributes['ownerid']);

            $sql = "update contracts set comments = :reason,contract_status = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,send_for_review = :send_for_review where contract_id = :contract_id";
            Yii::$app->db->createCommand($sql)
                ->bindValue(":reason", $reason)
                ->bindValue(":stage", $stage)
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":ownerid", $oldAttributes['creatorid'])
                ->bindValue(":contract_id", $Record)
                ->bindValue(":send_for_review", $send_for_review)
                ->execute();

            $message = "Contract No. " . $oldAttributes['contract_no'] . " is" . $contract_status_text . ". Please check";
            $this->sendnotification($oldAttributes['creatorid'], $message, $this->moduleName, $Record);
            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);


            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function approveDeliverychallandit($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `delivery_challandit` where deliverychallan_id=:deliverychallan_id")
                ->bindValue(":deliverychallan_id", $Record)
                ->queryOne();
            // echo "<pre>";print_r($oldAttributes);die;
            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['status'];
            $creatorid = $oldAttributes['creatorid'];

            $dc_status_text = '';
            if (isset($_POST['reject_reason'])) {
                $stage = 1;// Draft
                $send_for_approval = 0;
                $ownerid = $creatorid;
                $reason = $_POST['reject_reason'];
                $dc_status_text = ' Rejected';
                //user review contract and resend to review
                $send_for_review = 0;
            }
            if (isset($_POST['approve_reason'])) {
                $stage = 3;//approved
                $ownerid = $creatorid;
                $send_for_approval = 1;
                $reason = $_POST['approve_reason'];
                $dc_status_text = ' Approved ';

            }
            // echo "after generate deliverychallan";die;
            $newattributes = array("status" => $stage, "ownerid" => $oldAttributes['ownerid']);

            $sql = "update delivery_challandit set comment = :reason,status = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid, send_for_approval = :send_for_approval where deliverychallan_id = :deliverychallan_id";
            Yii::$app->db->createCommand($sql)
                ->bindValue(":reason", $reason)
                ->bindValue(":stage", $stage)
                ->bindValue(":send_for_approval", $send_for_approval)
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":ownerid", $oldAttributes['creatorid'])
                ->bindValue(":deliverychallan_id", $Record)
                ->execute();

            $message = "Delivery Challan No. " . $oldAttributes['deliverychallan_no'] . " is" . $dc_status_text . ". Please check";
            $this->sendnotification($oldAttributes['creatorid'], $message, $this->moduleName, $Record);
            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);
            //if DC is approved then change status to packing list generated
            // if($stage == 3){
            //         $this->generatepackinglistfordeliverychallandit($Record);
            // }

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function approveFocdit($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `foc_dit` where focdit_id=:focdit_id")
                ->bindValue(":focdit_id", $Record)
                ->queryOne();
            // echo "<pre>";print_r($_POST);die;
            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['stage'];
            $creatorid = $oldAttributes['creatorid'];

            $dc_status_text = '';
            if (isset($_POST['reject_reason'])) {
                $stage = 4;// reject
                $ownerid = $creatorid;
                $reason = $_POST['reject_reason'];
                $dc_status_text = ' Rejected';
            }
            if (isset($_POST['approve_reason'])) {
                $stage = 3;//approved
                $ownerid = $creatorid;
                $reason = $_POST['approve_reason'];
                $dc_status_text = ' Approved';

            }
            // echo "after generate deliverychallan";die;
            $newattributes = array("stage" => $stage, "ownerid" => $oldAttributes['ownerid']);

            $sql = "update foc_dit set comment = :reason,stage = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where focdit_id = :focdit_id";
            Yii::$app->db->createCommand($sql)
                ->bindValue(":reason", $reason)
                ->bindValue(":stage", $stage)
                ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                ->bindValue(":modifiedby", $id)
                ->bindValue(":ownerid", $oldAttributes['creatorid'])
                ->bindValue(":focdit_id", $Record)
                ->execute();

            $message = "FOC No. " . $oldAttributes['focdit_no'] . " is" . $dc_status_text . ". Please check";
            $this->sendnotification($oldAttributes['creatorid'], $message, $this->moduleName, $Record);
            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function approveInvoicedit($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `invoicedit` where invoicedit_id=:invoicedit_id")
                ->bindValue(":invoicedit_id", $Record)
                ->queryOne();
            // echo "<pre>";print_r($oldAttributes);die;
            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['invoice_status'];
            $creatorid = $oldAttributes['creatorid'];

            $dc_status_text = '';
            if (isset($_POST['reject_reason'])) {
                $stage = 1;// draft
                $ownerid = $oldAttributes['creatorid'];
                $reason = $_POST['reject_reason'];
                $dc_status_text = ' Rejected';

                $newattributes = array("comment" => $reason, "send_for_approval" => 0, "invoice_status" => $stage, "ownerid" => $ownerid);

                $sql = "update invoicedit set send_for_approval=0,invoice_status = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,comment=:comment where invoicedit_id = :invoicedit_id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":stage", $stage)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $ownerid)
                    ->bindValue(":comment", $reason)
                    ->bindValue(":invoicedit_id", $Record)
                    ->execute();
            }
            if (isset($_POST['approve_reason'])) {
                $stage = 3;//approved
                $ownerid = $creatorid;
                $reason = $_POST['approve_reason'];
                $dc_status_text = ' Approved';

                //once approved assign to devit cx
                $reports = "-- First, get the next higher user ID after the last modifier
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H86'
                                        AND u.id > (
                                            SELECT whodid
                                            FROM modtracker_basic
                                            WHERE module = '" . ucfirst($this->moduleName) . "' AND status = 2
                                            ORDER BY changedon DESC
                                            LIMIT 1
                                        )
                                        ORDER BY u.id ASC
                                        LIMIT 1
                                    )
                                    UNION ALL
                                    -- If none, wrap around to the lowest ID (still excluding the last modifier)
                                    (
                                        SELECT u.id
                                        FROM user u
                                        JOIN user2role ur ON ur.userid = u.id
                                        WHERE u.deleted = 0
                                        AND u.status = 10
                                        AND ur.roleid = 'H86'
                                        AND u.id != (
                                            SELECT whodid
                                            FROM modtracker_basic
                                            WHERE module = '" . ucfirst($this->moduleName) . "' AND status = 2
                                            ORDER BY changedon DESC
                                            LIMIT 1
                                        )
                                        ORDER BY u.id ASC
                                        LIMIT 1
                                    )
                                    LIMIT 1";
                $rest = Yii::$app->db->createCommand($reports)->queryOne();
                // print_r($rest);die;
                if (isset($rest['id']) && !empty($rest['id']))
                    $ownerid = $rest['id'];
                else
                    $ownerid = $oldAttributes['ownerid'];


                $newattributes = array("comment" => $reason, "invoice_status" => $stage, "ownerid" => $ownerid);

                $sql = "update invoicedit set invoice_status = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid,comment=:comment where invoicedit_id = :invoicedit_id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":stage", $stage)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $ownerid)
                    ->bindValue(":comment", $reason)
                    ->bindValue(":invoicedit_id", $Record)
                    ->execute();

            }
            // echo "after generate deliverychallan";die;

            if ($stage == 3) {
                //if apporved then update invoice date and invoice no in dc
                $delivery_challan_number = $oldAttributes['delivery_challan_number'];
                $invoicedit_no = $oldAttributes['invoicedit_no'];
                $invoice_date = $oldAttributes['invoice_date'];
                $sql = "update delivery_challandit set invoice_date = :invoice_date,invoice_number = :invoicedit_no,invoice_created=2,modifiedby = :modifiedby,modifiedtime=:modifiedtime where  	deliverychallan_id = :delivery_challan_number";

                Yii::$app->db->createCommand($sql)
                    ->bindValue(":invoicedit_no", $invoicedit_no)
                    ->bindValue(":invoice_date", $invoice_date)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":delivery_challan_number", $delivery_challan_number)
                    ->execute();
                $newattributes = array("invoice_date" => $invoice_date, 'invoice_created' => 2);
                // $modlog = new ModtrackerBasic();
                // $modlog->auditlog($oldAttributes, $newattributes, 'deliverychallandit', $delivery_challan_number, 2, Yii::$app->user->id);

                //print_r($_POST);die;
            }


            $message = "Invocie No. " . $oldAttributes['invoicedit_no'] . " is" . $dc_status_text . ". Please check";
            $this->sendnotification($ownerid, $message, $this->moduleName, $Record);

            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    public function generatepackinglistfordeliverychallandit($Record)
    {
        $modlog = new ModtrackerBasic();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $oldAttributes = Yii::$app->db->createCommand("select * from `delivery_challandit` where deliverychallan_id=:deliverychallan_id")
                ->bindValue(":deliverychallan_id", $Record)
                ->queryOne();
            // echo "<pre>";print_r($oldAttributes);die;
            $id = Yii::$app->user->id;
            $stageold = $oldAttributes['status'];
            $dctype = $oldAttributes['delivery_challan_type'];
            $creatorid = $oldAttributes['creatorid'];

            $dc_status_text = '';
            if ($stageold == 3) {
                $stage = 5;//Packing List Generated
                $ownerid = $creatorid;
                $dc_status_text = ' Packing List Generated';
            }

            $newattributes = array("status" => $stage, "ownerid" => $oldAttributes['ownerid']);

            if ($dctype == 1) {
                $sql = "update delivery_challandit set invoice_created =:invoice_created, status = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where deliverychallan_id = :deliverychallan_id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":stage", $stage)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $oldAttributes['creatorid'])
                    ->bindValue(":invoice_created", 3)
                    ->bindValue(":deliverychallan_id", $Record)
                    ->execute();
            } else {
                $sql = "update delivery_challandit set status = :stage,modifiedtime = :modifiedtime,modifiedby = :modifiedby,ownerid=:ownerid where deliverychallan_id = :deliverychallan_id";
                Yii::$app->db->createCommand($sql)
                    ->bindValue(":stage", $stage)
                    ->bindValue(":modifiedtime", date("Y-m-d H:i:s"))
                    ->bindValue(":modifiedby", $id)
                    ->bindValue(":ownerid", $oldAttributes['creatorid'])
                    ->bindValue(":deliverychallan_id", $Record)
                    ->execute();
            }

            // $message = "Delivery Challan No. " . $oldAttributes['deliverychallan_no'] . " is".$dc_status_text.". Please check";
            // $this->sendnotification($oldAttributes['creatorid'], $message, $this->moduleName, $Record);
            // print_r($_POST);die;
            $modlog->auditlog($oldAttributes, $newattributes, $this->moduleName, $Record, 2, Yii::$app->user->id);
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }
    // get header columns
    public function getHeaderDetail($Tab)
    {
        $connection = Yii::$app->db;
        $arr_tab = Yii::$app->db
            ->createCommand("SELECT GROUP_CONCAT(columnname SEPARATOR ', ') AS columns
FROM field
WHERE tabid=:tabid and headerview=1")
            ->bindValue(":tabid", $Tab)
            ->queryOne();
        // $arr_tab = Yii::$app->db->createCommand()
        // ->select()
        // ->from('tab')
        // ->where('name =:name', array(':name' =>$ModuleName))
        // ->queryRow();
        return $arr_tab;
    }
    // get header columns
    public function getRelatedmodules($Tab)
    {
        $connection = Yii::$app->db;
        $arr_tab = Yii::$app->db
            ->createCommand("SELECT related_module,actions, name as modulename,tablabel as modulelabel,related_table,related_tablekeyid ,related_fieldname,related_recordfieldnme FROM `module_relation` INNER JOIN tab on tab.tabid=module_relation.related_module where module_relation.source_module=:tabid and module_relation.deleted=0 order by sequence")
            ->bindValue(":tabid", $Tab)
            ->queryAll();
        // $arr_tab = Yii::$app->db->createCommand()
        // ->select()
        // ->from('tab')
        // ->where('name =:name', array(':name' =>$ModuleName))
        // ->queryRow();
        return $arr_tab;
    }

    public function getRelatedmoduleActiond($Tab, $related_module)
    {
        $connection = Yii::$app->db;
        $arr_tab = Yii::$app->db
            ->createCommand("SELECT actions as modulename FROM `module_relation` INNER JOIN tab on tab.tabid=module_relation.related_module where module_relation.source_module=:tabid and related_module=:related_module and module_relation.deleted=0 order by sequence")
            ->bindValue(":tabid", $Tab)
            ->bindValue(":related_module", $related_module)
            ->queryAll();
        // $arr_tab = Yii::$app->db->createCommand()
        // ->select()
        // ->from('tab')
        // ->where('name =:name', array(':name' =>$ModuleName))
        // ->queryRow();
        return $arr_tab;
    }

    public function saveAttachedFiles($file)
    {
        if (empty($file)) {
            return "";
        }

        // Maximum file size allowed (5GB)
        $maxFileSize = 5 * 1024 * 1024 * 1024; // 5GB in bytes

        // Security: Validate file extension and MIME type
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'zip'];
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            "application/x-zip-compressed"
        ];

        $fileExtension = pathinfo($file->name, PATHINFO_EXTENSION);
        if ($fileExtension)
            $fileExtension = strtolower($fileExtension);

        if (!in_array($fileExtension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
            return ['success' => false, 'message' => "Invalid file type. $fileExtension is not allowed"];
        }

        // Check if file exceeds maximum allowed size (5GB)
        if ($file->size > $maxFileSize) {
            return ['success' => false, 'message' => 'File size exceeds the maximum allowed size of 5GB.'];
        }

        // Determine the directory structure based on year, month, and week
        $year = date('Y');
        $month = date('m');
        $week = date('W'); // Week of the year

        // Define the upload base path
        $baseUploadPath = Yii::getAlias('@webroot/uploads');
        $targetPath = $baseUploadPath . "/$year/$month/week_$week/";

        // Create directories if they do not exist
        if (!is_dir($targetPath)) {
            if (!mkdir($targetPath, 0755, true)) {
                return ['success' => false, 'message' => 'Failed to create upload directories.'];
            }
        }

        // Generate a secure unique file name
        $fileName = uniqid() . '.' . $fileExtension;
        $filePath = $targetPath . $fileName;
        $filesavepath = "uploads/$year/$month/week_$week/" . $fileName;

        // Save the file
        $attachment_id = "";
        if ($file->saveAs($filePath)) {
            // Save to attachments
            $modelatach = new Attachments();
            $modelatach->name = $file->name;
            $modelatach->type = $file->type;
            $modelatach->path = $filesavepath;
            $modelatach->storedname = $fileName;

            if ($modelatach->validate()) {
                if ($modelatach->save()) {
                    // Update modelleadetail if necessary
                    // $modelleadetail->filename = $modelatach->attachmentsid;
                    $attachment_id = $modelatach->attachmentsid;
                }
            }

            return ['success' => true, 'fileName' => $attachment_id];
        } else {
            return ['success' => false, 'message' => 'Failed to save the file.'];
        }
    }

 
    public function getralatedkeys($relatedmodule)
    {
        $connection = Yii::$app->db;

        $arr_tab = Yii::$app->db
            ->createCommand("SELECT tablabel as modulename,source_module,related_fieldname,related_recordfieldnme 
            FROM module_relation
            join tab on tab.tabid = module_relation.source_module
            WHERE related_module=:relatedmodule")
            ->bindValue(":relatedmodule", $relatedmodule)
            ->queryAll();
        // $arr_tab = Yii::$app->db->createCommand()
        // ->select()
        // ->from('tab')
        // ->where('name =:name', array(':name' =>$ModuleName))
        // ->queryRow();
        return $arr_tab;
    }
    //added on 21 ajn 2025 to get opportunity detail
    public function getopportunity($recordid)
    {
        $data = $_POST;
        $record_id = $recordid;
        $account_name = "";
        $spoc_name = "";
        $bill_to_location = "";

        $connection = Yii::$app->db;
        $command = $connection
            ->createCommand("SELECT * FROM opportunity WHERE opportunity_id = :record_id and drilling=:drilling")
            ->bindValues([":record_id" => $record_id, ":drilling" => 1]);
        $columns = $command->queryOne();
        if (!empty($columns)) {
            //get vendor name
            $vendor_id = $columns['vendor_account_name'];
            if ($vendor_id) {
                $command = $connection
                    ->createCommand("SELECT acc_name FROM vendor_account WHERE vendoraccid = :vendoraccid")
                    ->bindValue(":vendoraccid", $vendor_id);
                $vendrorData = $command->queryOne();
                $account_name = $vendrorData['acc_name'] ?? "";
            }

            //get spoc name
            $contact_id = $columns['contact_name'];
            if ($contact_id) {
                $command = $connection
                    ->createCommand("SELECT first_name FROM contacts WHERE contacts_id = :contacts_id")
                    ->bindValue(":contacts_id", $contact_id);
                $contactData = $command->queryOne();
                $spoc_name = $contactData['first_name'] ?? "";
            }

            // bill to location 
            $bill_location = $columns['bill_location'];
            if ($bill_location) {
                $command = $connection->createCommand("SELECT vendorloc_id ,vendor_loc_name FROM vendor_locations WHERE vendorloc_id=:vendorloc_id")
                    ->bindValues([":vendorloc_id" => "$bill_location"]);
                $data = $command->queryOne();
                $bill_to_location = $data && $data["vendor_loc_name"] ? $data["vendor_loc_name"] : "";
            }
            return
                $data = array(
                    'account_name' => $account_name ?? "",
                    'spoc_name' => $spoc_name ?? "",
                    'spoc_mobile' => $columns['contact_mobile'] ?? "",
                    'bill_address' => $columns['bill_address'] ?? "",
                    'bill_location' => $bill_to_location ?? "",
                    'bill_state' => $columns['bill_state'] ?? "",
                    'bill_pincode' => $columns['bill_pincode'] ?? "",
                    'bill_gstin_no' => $columns['bill_gstin_no'] ?? ""
                );
        } else {
            return '';
        }
    }

    // function to get get account code
    public function getaccountcode($code)
    {
        $currentfinanceyear = $this->getCurrentFinanceYear();
        $table_name = $this->moduleName;
        $model = new AutoNo();
        $orderno = $model->getautomodulecode(1, $table_name);
        $code = $code . $currentfinanceyear . $orderno;
        return $code;
    }
    public function getPOnuber($stdcode)
    {
        $currentfinanceyear = $this->getCurrentFinanceYear();
        $table_name = $this->moduleName;
        $model = new AutoNo();
        $orderno = $model->getautomodulecode(1, $table_name);
        $code = "DIT" . $currentfinanceyear . $stdcode . "PO" . $orderno;
        return $code;
    }
    function getCurrentFinanceYear()
    {
        // $currentMonth = date('m'); // Get current month (01 to 12)

        // If the current month is before April (January to March), the fiscal year is for the previous year
        // if ($currentMonth < 4) {
        //     $startYear = date('y') - 1; // Fiscal year starts from the previous year
        //     $endYear = date('y'); // Fiscal year ends in the current year
        // } else {
        //     $startYear = date('y'); // Fiscal year starts in the current year
        //     $endYear = date('y') + 1; // Fiscal year ends in the next year
        // }

        //////get financial year/////
        $startyear = '';
        $endyear = '';
        $sql = "select * from fyear where is_active = 1 limit 1";
        $cmd = Yii::$app->db->createCommand($sql)->queryOne();
        if ($cmd) {
            $startyear = $cmd['start_year'];
            $endyear = $cmd['end_year'];

            $string = "Hello, World!";
            $n = 2; // Number of characters to extract from the end
            $startyear = substr($startyear, -$n);
            $endyear = substr($endyear, -$n);
        }
        // echo $startyear.$endyear;die;

        return $startyear . $endyear; // Concatenate the last two digits of the start and end years
    }
    function SaveSourcingdealTotal($sourcingdeal_id)
    {
        $TotalSourcingDealAmount = 0; //Service Sale Amt + Product Quoted Amt (inclusive GST)
        $TotalSourcingDealCost = 0; //Product Quoted Amt (exclusive GST) + Service Cost Amt
        $TotalSourcingDealSale = 0; //Service Sale Amt + Product Sale Amt (SP inclusive)
        $ProductCost = 0;
        $ProductSale = 0;
        $MarketingExpenses = 0;
        $ServiceSale = 0;
        $ServiceCost = 0;
        // echo $sourcingdeal_id;die;
        $record = Yii::$app->db->createCommand("select * from product_costing where related_to=51 and related_to_id=:sourcingdeal_id")
            ->bindValue(":sourcingdeal_id", $sourcingdeal_id)->queryOne();
        // print_r($record);die;
        if ($record) {
            $TotalSourcingDealAmount += $record['total_quoted_amt_inclusive_gst']; //Service Sale Amt + Product Quoted Amt (inclusive GST)
            $TotalSourcingDealCost += $record['total_quoted_amt_exclusive_gst']; //Product Quoted Amt (exclusive GST) + Service Cost Amt
            $TotalSourcingDealSale += $record['total_sp_amount_inclusive_gst']; //Service Sale Amt + Product Sale Amt (SP inclusive)
            $ProductCost = $record['total_quoted_amt_inclusive_gst'];
            $ProductSale = $record['total_sp_amount_inclusive_gst'];
            $MarketingExpenses = $record['total_marketing_expenses'];
            $Margin = $TotalSourcingDealCost - $MarketingExpenses; //Total Sourcing Deal Sale - Total Sourcing Deal Cost - Marketing Expenses
            if ($TotalSourcingDealSale > 0)
                $MarginPercent = ($Margin / $TotalSourcingDealSale) * 100; ////Margin / Total Sourcing Sale
            else
                $MarginPercent = 0;


            //update into sourcing eal
            // echo $sql = "Update sourcingdeal set total_sourcing_deal_amount=$TotalSourcingDealAmount,total_sourcing_deal_cost=$TotalSourcingDealCost,total_sourcing_deal_sale=$TotalSourcingDealSale,product_cost=$ProductCost,product_sale=$ProductSale,margin=$Margin,margin_percentage=$MarginPercent where sourcingdeal_id = $sourcingdeal_id";die;

            /////////////////save master pricing//////////////////////
            $CostPrice_GST_Exclude = (float) $record['total_quoted_amt_exclusive_gst'];
            $CostPrice_GST_Include = (float) $record['total_quoted_amt_inclusive_gst'];
            $SP_GST_Include = (float) $record['total_sp_amount_inclusive_gst'];
            $SP_GST_Exclude = (float) $record['total_sp_amount_exclusive_gst'];
            //get logistic cost
            $total_logistics_cost = 0;
            $repairing_cost = 0;
            $additional_cost = 0;
            $exp_cost = 0;
            $sql = "select total_logistics_cost,repairing_cost,exp_cost,additional_cost from sourcingdeal where sourcingdeal_id=:sourcingdeal_id";
            $res = Yii::$app->db->createCommand($sql)->bindValue(":sourcingdeal_id", $sourcingdeal_id)->queryOne();
            if ($res) {
                $total_logistics_cost = $res['total_logistics_cost'];
                $repairing_cost = $res['repairing_cost'];
                $exp_cost = $res['exp_cost'];
                $additional_cost = $res['additional_cost'];
            }
            // Actual Profit = ((Sales Price(GST Exclude)) - (Cost Price(GST Exclude)+Logistics Cost +Repairing Cost +EXPense Cost +Additional Cost)
            // echo $SP_GST_Exclude ."-". $CostPrice_GST_Exclude." + ".$total_logistics_cost." + ".$repairing_cost ." + ". $exp_cost ." + ". $additional_cost;
            $actual_profit = $SP_GST_Exclude - $CostPrice_GST_Exclude + $total_logistics_cost + $repairing_cost + $exp_cost + $additional_cost;
            //Actual Profit(%) = (Actual Profit/ Sales Price (GST Exclude)
            if ($SP_GST_Exclude > 0)
                $actual_profit_percentage = $actual_profit / $SP_GST_Exclude * 100;
            else
                $actual_profit_percentage = '';
            // echo $actual_profit;
            // echo "<br>";
            // echo $SP_GST_Exclude;
            // echo "<br>";
            // echo $actual_profit_percentage;die;
            $sql2 = "Update sourcingdeal set actual_profit=:actual_profit,actual_profit_percentage=:actual_profit_percentage,cost_price_gst_exclude=:cost_price_gst_exclude,cost_price_gst_include=:cost_price_gst_include,sales_price_gst_exclude=:sales_price_gst_exclude,sales_price_gst_include=:sales_price_gst_include  where sourcingdeal_id = :sourcingdeal_id";
            Yii::$app->db->createCommand($sql2)
                ->bindValue(":actual_profit", $actual_profit)
                ->bindValue(":actual_profit_percentage", $actual_profit_percentage)
                ->bindValue(":cost_price_gst_exclude", $CostPrice_GST_Exclude)
                ->bindValue(":cost_price_gst_include", $CostPrice_GST_Include)
                ->bindValue(":sales_price_gst_exclude", $SP_GST_Exclude)
                ->bindValue(":sales_price_gst_include", $SP_GST_Include)
                ->bindValue(":sourcingdeal_id", $sourcingdeal_id)
                ->execute();
        }

        // echo $MarketingExpenses;die;

        ///get detail from service detail
        $record = Yii::$app->db->createCommand("select * from servicedetail where related_to=51 and related_to_id=:sourcingdeal_id")
            ->bindValue(":sourcingdeal_id", $sourcingdeal_id)->queryOne();
        if ($record) {
            $TotalSourcingDealAmount += $record['total_sp_amount_inclusive_gst']; //Service Sale Amt + Product Quoted Amt (inclusive GST)
            $TotalSourcingDealCost += $record['total_service_cost']; //Product Quoted Amt (exclusive GST) + Service Cost Amt
            $TotalSourcingDealSale += $record['total_sp_amount_inclusive_gst']; //Service Sale Amt + Product Sale Amt (SP inclusive)
            $MarketingExpenses += $record['total_marketing_expenses'];

            // $ProductCost = $record['total_quoted_amt_inclusive_gst'];
            $ServiceSale = $record['total_sp_amount_inclusive_gst'];
            $ServiceCost = $record['total_service_cost'];


            //update into sourcing eal
            // echo $sql = "Update sourcingdeal set total_sourcing_deal_amount=$TotalSourcingDealAmount,total_sourcing_deal_cost=$TotalSourcingDealCost,total_sourcing_deal_sale=$TotalSourcingDealSale,product_cost=$ProductCost,product_sale=$ProductSale,margin=$Margin,margin_percentage=$MarginPercent where sourcingdeal_id = $sourcingdeal_id";die;

        }
        // echo $MarketingExpenses;die;
        if ($TotalSourcingDealAmount > 0) {
            $Margin = $TotalSourcingDealSale - $TotalSourcingDealCost - $MarketingExpenses; //Total Sourcing Deal Sale - Total Sourcing Deal Cost - Marketing Expenses
            // echo $Margin;die;
            $MarginPercent = ($Margin / $TotalSourcingDealSale) * 100; ////Margin / Total Sourcing Sale

            $sql = "Update sourcingdeal set total_sourcing_deal_amount=:total_sourcing_deal_amount,total_sourcing_deal_cost=:total_sourcing_deal_cost,total_sourcing_deal_sale=:total_sourcing_deal_sale,product_cost=:product_cost,product_sale=:product_sale,margin=:margin,margin_percentage=:margin_percentage,service_sale=:service_sale,service_cost=:service_cost  where sourcingdeal_id = :sourcingdeal_id";
            Yii::$app->db->createCommand($sql)
                ->bindValue(":total_sourcing_deal_amount", $TotalSourcingDealAmount)
                ->bindValue(":total_sourcing_deal_cost", $TotalSourcingDealCost)
                ->bindValue(":total_sourcing_deal_sale", $TotalSourcingDealSale)
                ->bindValue(":product_cost", $ProductCost)
                ->bindValue(":product_sale", $ProductSale)
                ->bindValue(":margin", $Margin)
                ->bindValue(":margin_percentage", $MarginPercent)
                ->bindValue(":sourcingdeal_id", $sourcingdeal_id)
                ->bindValue(":service_sale", $ServiceSale)
                ->bindValue(":service_cost", $ServiceCost)
                ->execute();
        }
    }

    public static function saveGeneratedFiles($base64Data, $originalName, $Record, $moduleName, $oldAttributes, $newattributes, $attachment_type = "application/pdf")
    {
        if (empty($base64Data)) {
            return ['success' => false, 'message' => 'No file data provided.'];
        }

        $decodedData = base64_decode($base64Data);
        if ($decodedData === false) {
            return ['success' => false, 'message' => 'Invalid base64 data.'];
        }

        // Define storage structure (Year/Month/Week)
        $year = date('Y');
        $month = date('m');
        $week = date('W');

        // Define upload path
        $baseUploadPath = Yii::getAlias('@webroot/uploads');
        $targetPath = $baseUploadPath . "/$year/$month/week_$week/";

        // Ensure directory exists
        if (!is_dir($targetPath) && !mkdir($targetPath, 0755, true)) {
            return ['success' => false, 'message' => 'Failed to create upload directories.'];
        }

        // Generate unique filename
        $fileName = $originalName; //uniqid('pdf_') . '.pdf'; want to save with the original name
        $filePath = $targetPath . $fileName;
        $fileSavePath = "uploads/$year/$month/week_$week/" . $fileName;

        // Save PDF file to disk
        if (file_put_contents($filePath, $decodedData) === false) {
            return ['success' => false, 'message' => 'Failed to save the file.'];
        }

        // Save file details in the database
        $attachment = new Attachments();
        $attachment->name = $originalName;
        $attachment->type = $attachment_type;
        $attachment->path = $fileSavePath;
        $attachment->storedname = $fileName;

        if ($attachment->validate() && $attachment->save()) {
            $modlog = new ModtrackerBasic();
            $modlog->auditlog($oldAttributes, $newattributes, $moduleName, $Record, 2, Yii::$app->user->id);
            return ['success' => true, 'fileName' => $attachment->attachmentsid, 'filePath' => $fileSavePath];
        }

        return ['success' => false, 'message' => 'Failed to save file details in database.'];
    }

}
