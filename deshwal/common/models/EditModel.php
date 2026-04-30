<?php
namespace common\models;
use Yii;
/**
 * EditModel class.
 * EditModel is the data structure for keeping
 * EditModel form data. It is used by the 'Module' action of 'Controller'.
 */
class EditModel extends \yii\db\ActiveRecord
{
	public $_members = array();
	private static $_tableName;   // Static property to hold the dynamic table name
	public $tableName;
	public $fieldId;
	public $moduleName;
	public $recordId;
	public $Multiple_Records=array();
	function __construct($tablename,$fieldid='',$moduleName)
	{
		$this->fieldId=$fieldid;
		$this->moduleName = $moduleName;
		self::$_tableName = $tablename;  // Set the dynamic table name
		$this->setTableName(self::$_tableName);
		$Columns=$this->getProperty();
		// print_r($Columns);//show dynamic columns
		// die;	
		foreach($Columns as $Column)
		$this->_members[$Column['columnname']] = null;
		$this->_members[$fieldid] = null;
		$this->_members['tableName'] = null;
		$this->_members['moduleName'] = null;
		$this->_members['fieldId'] = null;
		$this->_members['mode'] = null;
		parent::__construct();
	}
	  // Static tableName() method required by ActiveRecord
    public static function tableName()
    {
        return self::$_tableName;  // Return the dynamic table name
    }
	public function gettabname()
	{
		return $this->moduleName;
	}	
	public function setTableName($tablename)
	{
		$this->tableName=$tablename;
	}
	/**
	 * Declares the validation rules.
	 */
	public function getProperty()
	{
		$table_name=$this->tableName();
		$Columns =Yii::$app->db->createCommand('SELECT field.columnname,field.fieldlabel FROM field  WHERE tablename=:tablename') ->bindValue(':tablename', $table_name) ->queryAll();
		// (new \yii\db\Query())
    // ->select(['field.columnname', 'field.fieldlabel'])->from('field')->where('tablename = :tablename', [':tablename' => $table_name])->all();
		return 	$Columns;		
	}
	public function attributeLabels()
	{
		$Columns=$this->getProperty();	
		$arr_lable=array();
		//print_r($Columns);die;
		foreach($Columns as $Column)
		$arr_lable[$Column['columnname']]=$Column['fieldlabel'];
		/*echo "<br>Lable=";
		print_r($arr_lable);
		die;*/
		return 	$arr_lable;
	}
	public function getTabDetail($ModuleName)
	{
		$connection=Yii::$app->db;
		$arr_tab = Yii::$app->db->createCommand('SELECT * FROM tab WHERE  name=:name') ->bindValue(':name', $ModuleName) ->queryOne();
		// $arr_tab = Yii::$app->db->createCommand()
		// ->select()
		// ->from('tab')
		// ->where('name =:name', array(':name' =>$ModuleName))
		// ->queryRow();
		return $arr_tab;
	}
	public function getActionList($ModuleName)
	{
		$ActionList=array();
		$actionName=$ModuleName;
		$arr_tab=$this->getTabDetail($ModuleName);
		$ActionList['ActionName']=$actionName;
		$ActionList['ModuleName']=$ModuleName;
		$ActionList['ModuleLabel']=$arr_tab['tablabel'];
		return $ActionList;
	}
	// public function getFieldDetail($rolebasedrecord)
	//rolebase will be implementd later
	public function getFieldDetail()
	{
		$fieldid=$this->fieldId;
		$fieldids=$this->fieldId;
		// $table_name=$this->gettablename();
		$table_name = self::$_tableName;
		$tab_name=$this->moduleName;
		$RecordId=(int)$this->_members[$fieldid];
		//$roleid =(int)$rolebasedrecord;//role will be implemented later
		if(!empty($RecordId))
		{
		$view="edit_view";
		$Record=$this->find("$fieldid=".$RecordId);
		//print_r($Record);die;
		}
		else
		$view="create_view";
		$Tab=new Tab;
// echo $table_name;die;
		// $Column=$Tab->with('Blocks')->find('name =:name and Blocks.edit_view =:edit_view and Blocks.presence =:presence',array(':name' =>$table_name,':edit_view' =>1,':presence' =>1));
		$Column = Tab::find()
			    ->joinWith('blocks')  // Eager loading the related 'Blocks' model
			    ->where([
			        'tab.name' => $tab_name,
			        'blocks.edit_view' => 1,
			        'blocks.display_status' => 1
			    ])
			    ->one();
			    
		//print_r($Column);die;
		$tabId=$Column->tabid;
		$model1=new Reference($table_name,$fieldid);



		
		//echo "<pre>";print_r($Column);die;
		if(!empty($RecordId))
		return array($Column,$Record);
		else
		return $Column;
	}
	public function saveModule($tabs)
	{
		$mode = $_POST['mode'];
			echo $module = $_POST['module'];die;
			if($module == "leads")
			{

			}
	}
}
