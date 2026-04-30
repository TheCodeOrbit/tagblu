<?php

namespace common\controllers;

use yii\web\Controller;
use Yii;
use app\models\EditModel;
use app\models\ListModel;
use backend\models\AccessCheck;
use common\models\Tab;
use common\models\Field;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;

use backend\assets\AdminAsset;
use backend\models\KanbanCard;

use backend\models\TableList;
use backend\models\Task;
use app\models\Reference;
use app\models\Leaddetails;
use app\models\Leadinformation;
use app\models\Modnotes;
use app\models\ModtrackerBasic;
class ModuleController extends Controller
{
	 public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index','create','list','popuplist','popupsearch','quickcreatepopup','getcolumnfields','saveselectedcolumns','gettabledata','filterbylead','test','edit','detail','approvelead','postnotes','getnotes','deleteselectedrow','bulkupdate','bulkupdateview','detailhistory'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    public function actionApprovelead()
    {
        // print_r($_POST);die;
        $TabId=$this->TabId;
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;
      
        $Record = $_POST['Recordid'];
        $id =  Yii::$app->user->id;

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);
        $actionid = "edit";
        $auditmodel = new EditModel($TableName,$FieldId,$ModuleName,$actionid);

        // $modellead = new Leadinformation();
        //  $modellead->leadstatus = $_POST['leadstatus_v'];
        // $modellead->modifiedtime = date("Y-m-d H:i:s");
        // $modellead->modifiedby = $id;
        // $modellead->save();

       
        $newAttribute = $_POST;
        $auditmodel->approvelead($Record);
        
       
        $this->layout = '@app/views/layouts/main-one'; 

        
    }

    public function actionPopuplist()
    {
        // $this->enableCsrfValidation = false;//disable csrf
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;

        $id =  Yii::$app->user->id;
        $uid=Yii::$app->params['dirName'];

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile,$tabs);
        
       

        //$model=new ListModel($TableName,$FieldId);
        $model=new Reference($TableName,$FieldId);
        $ActionList=$model->getActionList($ModuleName);
        //print_r($ActionList);die;
        $ActionList['OrderBy']=Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder']=Yii::$app->request->get('SortOrder');
        list($ColumnList,$RecordList,$totalitemcount)=$model->getListRecord_pop($ActionList['OrderBy'],$ActionList['SortOrder'],$rolebasedrecord,$modulepermission,$ModuleName);
        $this->layout = '@app/views/layouts/main-new';
        $this->renderPartial('@app/views/tetra/PopupL',array('RecordList'=>$RecordList,'ColumnList'=>$ColumnList,'ActionList'=>$ActionList,'ModName'=>$ModuleName,'operation'=>$modelaccess,'modulepermission'=>$modulepermission,'totalitemcount'=>$totalitemcount));

    }
    public function actionBulkupdateview()
    {
        // $this->enableCsrfValidation = false;//disable csrf
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;

        $id =  Yii::$app->user->id;
        $uid=Yii::$app->params['dirName'];

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile,$tabs);
        
        $actionid = "edit";
        $model=new EditModel($TableName,$FieldId,$ModuleName,$actionid);
        //$model->_members[$FieldId]=$Record;
        $arrRender['model']=$model;
        $ActionList=$model->getActionList($ModuleName);
        $ActionList['ActionName']="Edit";
        $this->getClientScript($ModuleName,"Edit");

        $Column=$model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            // print_r($Column);die;
            $arrRender['ColumnList']=$Column;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower']=$hasadminpower;
            $arrRender['TableName']=$TableName;
            $arrRender['FieldId']=$FieldId;
            $arrRender['action_name']=$actionid;
            $arrRender['userlist'] =$model->getuserlist();
            $arrRender['ModuleName']=$ModuleName;
            $arrRender['TableName']=$TableName;
            $arrRender['FieldId']=$FieldId;
            $arrRender['Tabname']=ucfirst($ModuleName);
            

        $this->layout = '@app/views/layouts/main-one';
        $this->renderPartial('@app/views/tetra/Bulkupdateview',$arrRender);

    }
    
    public function actionPopupsearch()
    {
         $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;

        $id =  Yii::$app->user->id;
        $uid=Yii::$app->params['dirName'];

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile,$tabs);
        
       

        //$model=new ListModel($TableName,$FieldId);
        $model=new Reference($TableName,$FieldId);
        $ActionList=$model->getActionList($ModuleName);
        //print_r($ActionList);die;
        $ActionList['OrderBy']=Yii::$app->request->get('OrderBy');
        $ActionList['SortOrder']=Yii::$app->request->get('SortOrder');
        list($ColumnList,$RecordList,$totalitemcount)=$model->getListRecord_pop($ActionList['OrderBy'],$ActionList['SortOrder'],$rolebasedrecord,$modulepermission,$ModuleName);
        $this->layout = '@app/views/layouts/main-new';
        $this->renderPartial('@app/views/tetra/PopSearch',array('RecordList'=>$RecordList,'ColumnList'=>$ColumnList,'ActionList'=>$ActionList,'ModName'=>$ModuleName,'operation'=>$modelaccess,'modulepermission'=>$modulepermission,'totalitemcount'=>$totalitemcount));      

    }
    public function actionGetcolumnfields()
    {
        $TabId = $this->TabId;
        $ModuleName = $this->ModuleName;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //get customview
         $savedColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => ucfirst($ModuleName)])
            ->column();
            //print_r($savedColumns);die;
        // Get the columns saved for the current user
        $savedColumns = (new \yii\db\Query())
            ->select(['fieldid'])
            ->from('cvcolumnlist')
            ->where(['cvid' => $savedColumns[0]])
            ->column();

            
        // Get all columns for the 'leaddetails' table
        $columns = (new \yii\db\Query())
            ->select(['columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tabid' => $TabId])
            ->all();
            //print_r($columns);die;

        // Set visibility based on whether column is in saved columns
        foreach ($columns as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }

        return $columns;
    }
    
    public function actionTest()
    {
        $this->layout = '@app/views/layouts/main-new'; 
        $this->render('@app/views/tetra/test');
    }
     public function actionDetail()
    {
        $TabId=$this->TabId;
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;
      
        $Record = $_REQUEST['Record'];
        $id =  Yii::$app->user->id;

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);

        $actionid = "detail";
        $model=new EditModel($TableName,$FieldId,$ModuleName,$actionid);
        $model->_members[$FieldId]=$Record;
        $arrRender['model']=$model;
        $ActionList=$model->getActionList($ModuleName);
        $ActionList['ActionName']="Edit";
        $this->getClientScript($ModuleName,"Edit");
           
           // echo "<br>Final Else Case";
            list($Column,$Record)=$model->getFieldDetail($rolebasedrecord);
            $arrRender['Record']=$Record;

            //echo "<pre>"; print_r($arrRender);echo "</pre>";die;
            $arrRender['ColumnList']=$Column;
            $arrRender['userlist'] =$model->getuserlist();
            $arrRender['ModuleName']=$ModuleName;
            $arrRender['TableName']=$TableName;
            $arrRender['FieldId']=$FieldId;
            $arrRender['Tabname']=ucfirst($ModuleName);
            $arrRender['Recordid']= $_REQUEST['Record'];
            $arrRender['getnotes'] = $this->getnotes();

        // $this->layout = '@app/views/layouts/main-new'; 
        $this->layout = '@app/views/layouts/main-one'; 
        $this->render('@app/views/tetra/detailview',$arrRender);
    }
    public function actionCreate()
    {
    	//print_r($_SESSION);die;
        //   	$ModuleName="Tetra";
		$action="Edit";
		// $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);		
		$arrRender=array();

    	$FieldId=$this->FieldId;
		$ModuleName=$this->ModuleName;
		$TableName=$this->TableName;


		// echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
		$id =  Yii::$app->user->id;
		$uid=Yii::$app->params['dirName'];

		$model=new AccessCheck();
		$tabs=$model->tabs($id,$ModuleName);
		$profile=$model->profile($id,$tabs,$ModuleName);
		$modelaccess=$model->moduleaccess($id,$profile,$tabs);
		$rolebasedrecord=$model->rolebasedrecord($id,$profile);
		$hasadminpower = $model->hasadminpower($profile);
        $modulepermission=$model->modulepermission($uid,$tabs);
		// print_r($rolebasedrecord);
        $actionid = "create";
		$model=new EditModel($TableName,$FieldId,$ModuleName,$actionid);
		$arrRender['model']=$model;
		$ActionList=$model->getActionList($ModuleName);
		$ActionList['ActionName']="Create";
		$this->getClientScript($ModuleName,$action);
	
		$arrRender['ActionList']=$ActionList;
		if(isset($_POST['btncancel']))
		{
			$this->redirect(array("$ModuleName/List"));	
		}
		elseif ($this->request->isPost) 
		{
			$tabs =1 ;
			 if (Yii::$app->request->isPost) {

                $result = $model->saveModule($tabs);

                if ($result) {
                	//echo "saved";die;
                    Yii::$app->session->setFlash('success', 'Data saved successfully.');
                    return $this->redirect(['list']); // Adjust accordingly
                } else {
                	echo "Failed";die;
                    Yii::$app->session->setFlash('error', 'Failed to save data.');
                }
            }
				
		}
		else
		{
			
			// echo "<br>Final Else Case";
			// die;
			//rolebase will be implemented later
			// $Column=$model->getFieldDetail($rolebasedrecord);
			$Column=$model->getFieldDetail($rolebasedrecord);
			// echo "xdgdf<pre>";
			// print_r($Column);die;
			$arrRender['ColumnList']=$Column;
			$arrRender['profile'] = $profile;
			$arrRender['uid'] = $id;
			$arrRender['tabs'] = $tabs;
			$arrRender['hasadminpower']=$hasadminpower;
            $arrRender['TableName']=$TableName;
            $arrRender['FieldId']=$FieldId;
            $arrRender['action_name']=$actionid;

			// 
			// echo "xdgdf<pre>";
			// print_r($arrRender)	;die;
			// $this->layout = 'main';

			// $this->layout = '@app/views/layouts/main'; 
			// $this->render('@app/views/tetra/EditView',$arrRender);

			$this->layout = '@app/views/layouts/main-new'; 
            // $this->render('@app/views/tetra/EditView-old',$arrRender);
            $this->renderPartial('@app/views/tetra/EditView',$arrRender);
			
		
		}
    	// return $this->render('index');
    }
     public function actionEdit()
    {
        $RecordId=Yii::$app->request->get('Record');
        // $RecordId = base64_decode($RecordId);
        //print_r($_SESSION);die;
        //      $ModuleName="Tetra";
        $action="Edit";
        // $this->getClientScript($ModuleName,$action);
        //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);        
        $arrRender=array();

        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;

        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id =  Yii::$app->user->id;
        $uid=Yii::$app->params['dirName'];

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);
        // print_r($rolebasedrecord);
        $actionid = "edit";
        $model=new EditModel($TableName,$FieldId,$ModuleName,$actionid);
        $model->_members[$FieldId]=$RecordId;
        $arrRender['model']=$model;
        $ActionList=$model->getActionList($ModuleName);
        $ActionList['ActionName']="Edit";
        $this->getClientScript($ModuleName,$action);
    
        $arrRender['ActionList']=$ActionList;
        if(isset($_POST['btncancel']))
        {
            $this->redirect(array("$ModuleName/List")); 
        }
        elseif ($this->request->isPost) 
        {
            $tabs =1 ;
             if (Yii::$app->request->isPost) {

                if($model->updateModule($RecordId))
                {
                    //echo "saved";die;
                    Yii::$app->session->setFlash('success', 'Data saved successfully.');
                    return $this->redirect(['list']); // Adjust accordingly
                } else {
                    echo "Failed";die;
                    Yii::$app->session->setFlash('error', 'Failed to save data.');
                }
            }
                
        }
        else
        {
             $model=new EditModel($TableName,$FieldId,$ModuleName,$actionid);
        $model->_members[$FieldId]=$RecordId;
        $arrRender['model']=$model;
        $ActionList=$model->getActionList($ModuleName);
        $ActionList['ActionName']="Edit";
        $this->getClientScript($ModuleName,$action);
           
           // echo "<br>Final Else Case";
            list($Column,$Record)=$model->getFieldDetail($rolebasedrecord);
            $arrRender['Record']=$Record;
            //echo "<pre>"; print_r($arrRender);echo "</pre>";die;
            $arrRender['ColumnList']=$Column;
            // die;
            //rolebase will be implemented later
            // $Column=$model->getFieldDetail($rolebasedrecord);
            //$Column=$model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            //print_r($Column);die;
            $arrRender['ColumnList']=$Column;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower']=$hasadminpower;
            $arrRender['TableName']=$TableName;
            $arrRender['FieldId']=$FieldId;
             $arrRender['action_name']=$actionid;

            // 
            // echo "xdgdf<pre>";
            // print_r($arrRender)  ;die;
            // $this->layout = 'main';

            // $this->layout = '@app/views/layouts/main'; 
            // $this->render('@app/views/tetra/EditView',$arrRender);

            $this->layout = '@app/views/layouts/main-new'; 
            // $this->render('@app/views/tetra/EditView-old',$arrRender);
            $this->renderPartial('@app/views/tetra/EditView',$arrRender);
            
        
        }
        // return $this->render('index');
    }
    public function actionQuickcreatepopup()
    {
        //print_r($_SESSION);die;
  //    $ModuleName="Tetra";
        $action="Edit";
        // $this->getClientScript($ModuleName,$action);
  //              Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/Tetra/jqdt.js', CClientScript::POS_END);      
        $arrRender=array();

        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;

        // echo $uid=Yii::$app->session[Yii::$app->params['dirName'].'_id'];die;
        $id =  Yii::$app->user->id;
        $uid=Yii::$app->params['dirName'];

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);
        // print_r($rolebasedrecord);
        $actionid = "create";
        $model=new EditModel($TableName,$FieldId,$ModuleName,$actionid);
        $arrRender['model']=$model;
        $ActionList=$model->getActionList($ModuleName);
        $ActionList['ActionName']="Create";
        $this->getClientScript($ModuleName,$action);
    
        $arrRender['ActionList']=$ActionList;
        if(isset($_POST['btncancel']))
        {
            $this->redirect(array("$ModuleName/List")); 
        }
        elseif (isset($_POST['savemodule'])) 
        {
            $tabs =1 ;
             if (Yii::$app->request->isPost) {
        $result = $model->saveModule($tabs);

        if ($result) {
            // echo "saved";die;
            $this->redirect(array("$ModuleName/List")); 
            Yii::$app->session->setFlash('success', 'Data saved successfully.');
            return $this->redirect(['view', 'id' => $model->id]); // Adjust accordingly
        } else {
            echo "Failed";die;
            Yii::$app->session->setFlash('error', 'Failed to save data.');
        }
    }
                
        }
        else
        {
            
            // echo "<br>Final Else Case";
            // die;
            //rolebase will be implemented later
            // $Column=$model->getFieldDetail($rolebasedrecord);
            $Column=$model->getFieldDetail($rolebasedrecord);
            // echo "xdgdf<pre>";
            // print_r($Column);die;
            $arrRender['ColumnList']=$Column;
            $arrRender['profile'] = $profile;
            $arrRender['uid'] = $id;
            $arrRender['tabs'] = $tabs;
            $arrRender['hasadminpower']=$hasadminpower;
            $arrRender['TableName']=$TableName;
            $arrRender['FieldId']=$FieldId;

            // 
            // echo "xdgdf<pre>";
            // print_r($arrRender)  ;die;
            // $this->layout = 'main';

            // $this->layout = '@app/views/layouts/main'; 
            // $this->render('@app/views/tetra/EditView',$arrRender);

            $this->layout = '@app/views/layouts/main-new'; 
            $this->renderPartial('@app/views/tetra/QuickEditView',$arrRender);
            
        
        }
        // return $this->render('index');
    }
     public function actionIndex()
    {
        return "This is a common controller!";
    }
     public function actionList()
    {
        $TabId=$this->TabId;
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;
    	$openCards = KanbanCard::find()->where(['pipeline_stage' => 'open'])->orderBy(['position' => SORT_ASC])->all();
        $inProgressCards = KanbanCard::find()->where(['pipeline_stage' => 'in_progress'])->orderBy(['position' => SORT_ASC])->all();
        $doneCards = KanbanCard::find()->where(['pipeline_stage' => 'done'])->orderBy(['position' => SORT_ASC])->all();

        //get customview
         $savedColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => ucfirst($ModuleName)])
            ->column();
            //print_r($savedColumns);die;
        // Get the columns saved for the current user
        $savedColumns = (new \yii\db\Query())
            ->select(['fieldid'])
            ->from('cvcolumnlist')
            ->where(['cvid' => $savedColumns[0]])
            ->column();

            
        // Get all columns for the 'leaddetails' table
        $columns = (new \yii\db\Query())
            ->select(['columnname','columnname AS field', 'fieldlabel AS headerName', 'fieldid'])
            ->from('field')
            ->where(['tabid' => $TabId,'list_view'=>1])
            ->orderBy(['sequence' => SORT_ASC])
            ->all();
            // print_r($columns);die;

        // Set visibility based on whether column is in saved columns
        foreach ($columns as &$column) {
            $column['visible'] = in_array($column['fieldid'], $savedColumns);
        }
        $model=new ListModel($TableName,$FieldId,$ModuleName);
        $filed_name = $model->getColumnList();

     

		$this->layout = '@app/views/layouts/main-one'; 
        $this->render('@app/views/tetra/listview',[ 'openCards' => $openCards,
            'inProgressCards' => $inProgressCards,
            'doneCards' => $doneCards,
            'ModuleName'=>$ModuleName,
            'Tabname'=>ucfirst($ModuleName),
            'columns'=>$columns,
            'filed_name'=>$filed_name
        ]);
    }

     public function actionFilterbylead()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            $labelValue = Yii::$app->request->post('labelValue');
            $inputValue = Yii::$app->request->post('inputValue');
            $fieldId = Yii::$app->request->post('fieldId');
            $filterOperator = Yii::$app->request->post('filteroperator');

            if (!empty($fieldId) && !empty($inputValue)) {
                $field = (new \yii\db\Query())
                    ->select('columnname')
                    ->from('field')
                    ->where(['fieldid' => $fieldId])
                    ->one();

                if ($field && isset($field['columnname'])) {
                    $columnName = $field['columnname'];
                    $query = Leaddetails::find();

                    switch ($filterOperator) {
                        case 'Equals':
                            $query->andWhere([$columnName => $inputValue]);
                            break;
                        case 'Not_Equals':
                            $query->andWhere(['<>', $columnName, $inputValue]);
                            break;
                        case 'Contains':
                            $query->andWhere(['like', $columnName, $inputValue]);
                            break;
                        case 'Not_Contains':
                            $query->andWhere(['not like', $columnName, $inputValue]);
                            break;
                        case 'In':
                            $query->andWhere(['in', $columnName, $inputValue]); // $inputValue should be an array
                            break;
                        case 'Not_In':
                            $query->andWhere(['not in', $columnName, $inputValue]); // $inputValue should be an array
                            break;
                        case 'is_Empty':
                            $query->andWhere(['or', [$columnName => null], [$columnName => '']]);
                            break;
                        case 'is_Not_Empty':
                            $query->andWhere(['and', ['is not', $columnName, null], ['<>', $columnName, '']]);
                            break;
                        case 'Begins_with':
                            $query->andWhere(['like', $columnName, "$inputValue%", false]); // Searches for values beginning with $inputValue
                            break;
                        default:
                            return $this->asJson([
                                'success' => false,
                                'message' => 'Invalid filter operator',
                            ]);
                    }

                    $filteredLeads = $query->asArray()->all(); // Convert to array

                    return $this->asJson([
                        'success' => true,
                        'data' => $filteredLeads,
                    ]);
                } else {
                    return $this->asJson([
                        'success' => false,
                        'message' => 'Column not found for the provided field ID',
                    ]);
                }
            } else {
                return $this->asJson([
                    'success' => false,
                    'message' => 'Field ID or input value is missing',
                ]);
            }
        } else {
            return $this->asJson([
                'success' => false,
                'message' => 'Invalid request',
            ]);
        }
    }

     public function actionSaveselectedcolumns()
    {
        $this->enableCsrfValidation = false;//disable csrf
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Get selected and deselected columns from the POST request
        $selectedColumns = Yii::$app->request->post('selectedColumns', []);
        $deselectedColumns = Yii::$app->request->post('deselectedColumns', []);
        $ModuleName=$this->ModuleName;

        $deleted = false;

        //get customview
         $savedColumns = (new \yii\db\Query())
            ->select(['cvid'])
            ->from('customview')
            ->where(['entitytype' => ucfirst($ModuleName)])
            ->column();
        $cvid = $savedColumns[0];
            //($savedColumns);die;
            

        // Process selected columns: insert if they don't exist
        foreach ($selectedColumns as $column) {
            $exists = (new \yii\db\Query())
                ->from('cvcolumnlist')
                ->where(['fieldid' => $column['field_id']])
                ->exists();

            if (!$exists) {
                Yii::$app->db->createCommand()->insert('cvcolumnlist', [
                    'cvid' => $cvid, // Update with relevant ID
                    'columnindex' => $column['field_id'],
                    'columnname' => $column['columnname'],
                    'fieldid' => $column['field_id'],
                ])->execute();
            }
        }

        // Process deselected columns: delete if they exist
        foreach ($deselectedColumns as $field_id) {
            $deletedRows = Yii::$app->db->createCommand()->delete('cvcolumnlist', [
                'fieldid' => $field_id,
            ])->execute();

            if ($deletedRows > 0) {
                $deleted = true;
            }
        }

        // Return different messages based on actions performed
        if ($deleted) {
            return ['status' => 'success', 'message' => 'Deselected columns deleted successfully.'];
        } else {
            return ['status' => 'success', 'message' => 'Selected columns saved successfully.'];
        }
    }

     public function actionGettabledata()
    {
      
        //echo "xcx";die;
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;

          $model=new AccessCheck();
        $id =  Yii::$app->user->id;
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);
        $modulepermission = $model->modulepermission($profile,$tabs);
        // Fetch data from the 'leaddetails' table
        //$leads = Leaddetails::find()->asArray()->all();
        $action="List";
        $model=new ListModel($TableName,$FieldId,$ModuleName);
        $this->getClientScript($ModuleName,$action);    
        $ActionList=$model->getActionList($ModuleName);
        $ActionList['OrderBy']='';//Yii::app()->request->getParam('OrderBy');
        $ActionList['SortOrder']='';//Yii::app()->request->getParam('SortOrder');
        $curPageNo='';//$_REQUEST['pagejump'];
         list($ColumnList,$RecordList,$totalitemcount)=$model->getListRecord($ActionList['OrderBy'],$ActionList['SortOrder'],$rolebasedrecord,$modulepermission);  
            // print_r($RecordList);
        return $RecordList;
    }

    /**
	 * Load Module Script  
	 */	
	public function getClientScript($ModuleName,$action)
		{
			$baseUrl = Yii::$app->HomeUrl; 
			$scriptPath=$baseUrl."js/$ModuleName/$action.js";
			$this->view->registerJsFile($scriptPath, ['depends' => [\yii\web\JqueryAsset::class]]);
		}

		///////list related functions
		 public function actionGetLeads()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Fetch data from the database
        $leads = Leaddetails::find()->asArray()->all();
        
        return $leads;
    }

    public function actionAgTable(){
        AdminAsset::register(Yii::$app->view);
        $this->layout = "main-new";
        return $this->render('ag-table');
    }

    public function actionTable()
    {
        AdminAsset::register(Yii::$app->view);
        $this->layout = "main-new";
        return $this->render('table');
    }

    public function actionGetData()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $models = TableList::find()->all();
            $data = [];

            foreach ($models as $model) {
                $data[] = [
                    'id' => $model->id,
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'email' => $model->email,
                    'phone' => $model->phone,
                    'country' => $model->country,
                    'city' => $model->city,
                    'owner' => is_array($model->owner) ? implode(', ', $model->owner) : $model->owner,  // Example of array handling
                    'company_name' => $model->company_name,
                    'address' => $model->address,
                    'company_address' => $model->company_address,
                    'company_website' => $model->company_website,
                    'employee_age' => $model->employee_age,
                    'employee_name' => $model->employee_name,
                    'created_at' => $model->created_at,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionGetTable()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $models = TableList::find()->all();
            $data = [];

            foreach ($models as $model) {
                $data[] = [
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'email' => $model->email,
                    'phone' => $model->phone,
                    'city' => $model->city,
                    'company_name' => $model->company_name,
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actionUpdateStage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $pipeline_stage = Yii::$app->request->post('pipeline_stage');

        $card = KanbanCard::findOne($id);
        if ($card) {
            $card->pipeline_stage = $pipeline_stage;
            if ($card->save()) {
                return ['success' => true];
            }
        }
        return ['success' => false];
    }
    public function actionPostnotes()
    {
         // print_r($_POST);die;
        $TabId=$this->TabId;
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;
      
        
        $id =  Yii::$app->user->id;
        $data = $_POST;

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);

        $model = new Modnotes();
        $model->notecontent = $_POST['modnotes'];
        $model->related_to = $TabId;
        $model->related_id = $_POST['Recordid'];
        $model->userid = Yii::$app->user->id;
        if($model->validate()) {
            if($model->save())
            {
                $modlog = new ModtrackerBasic();
                $modlog->auditlog('',$model,"modnotes",$model->modnotesid,0,$model->userid);
                $allnotes= getnotes();
                return $allnotes;
                //get all notes
            }
            else return false;
        }

    }
    public function getnotes()
    {
         // print_r($_POST);die;
        $TabId=$this->TabId;
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;
      
        
        $id =  Yii::$app->user->id;
        $data = $_POST;
        $Record = 24;//$_POST['Recordid'];

        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);

        $model = new Modnotes();

        // $allnotes = $model->find()
        // ->where(['related_to' => $TabId])
        // ->andWhere(['related_id' => $Record])
        // ->orderBy(['modnotesid' => SORT_DESC])
        // ->all();
       $records=  $model->find()
    ->joinWith(['modtrackerBasic'])
    ->where(['modnotes.related_to' => $TabId])
    ->andWhere(['modnotes.related_id' => $Record])
    ->orderBy(['modnotes.modnotesid' => SORT_DESC])
    ->all();
    $detail = array();
    foreach ($records as $record) {
        $a = array();
        $username = $this->getuser($record->userid);
        
        $a['notecontent'] = $record->notecontent;
        $a['notebyuser'] = $username;
        //print_r($record->modtrackerBasic);die;
        // Access related ModtrackerBasic fields
        if($record->modtrackerBasic) {
            $timestamp = strtotime($record->modtrackerBasic->changedon);

            // Format the date
            $enteredat = date('M d, Y \a\t g.i a', $timestamp); // Format: Oct 22, 2024 at 9.16 am
            $a['notedon'] = $enteredat;
        }
        else $a['notedon']='';

        // } else {
        //     echo "No ModtrackerBasic found for this Modnotes.\n";
        // }
        array_push($detail, $a);
    }
    // echo "<pre>";print_r($detail);
    //die;
    return $detail;


    }
    function getuser($userid)
    {

        $connection = Yii::$app->db;
        // $command=$connection->createCommand("select targettable,targetfield,dispfield  from picklist where     fieldid=:fieldid")->bindParam(':fieldid'=>$fieldid);
        

        $command=$connection->createCommand("select id,email,concat(first_name,' ',last_name) as showfield from user  where deleted =0 and id=:id")->bindValue(":id",$userid);
        $Columns = $command->queryOne();
       
        return $Columns['showfield'];
       
    }
    function actionDeleteselectedrow()
    {
        $TabId=$this->TabId;
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;
      
        
        $id =  Yii::$app->user->id;
       
        
        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);

         if (Yii::$app->request->isAjax) {

            $leadIds = Yii::$app->request->post('leadIds', []);
            if(!empty($leadIds))
            {
                $ids = implode(', ',$leadIds);
                //print_r($ids);die;
                $transaction = Yii::$app->db->beginTransaction();
                try 
                {
                $sql = "Update $TableName set deleted = 1 where $FieldId in ($ids)";
                $result = Yii::$app->db->createCommand($sql)->execute();
                // print_r($result);die;
                if($result)
                {
                    $transaction->commit();
                 return $this->asJson([
                    'status' => 'success',
                ]);
                }

                }
                catch (\Exception $e) {
                    echo "Failed to Archieve data: " .$e->getMessage()." ".$e->getTraceAsString();die;
                    // Rollback the transaction if something goes wrong
                   $transaction->rollBack();
                    return $this->asJson([
                        'status' => 'error',
                        'message' => "Failed to Archieve data: " .$e->getMessage()." ".$e->getTraceAsString(),
                    ]);
                   
                }
                
            }
        }


    }
      public function actionBulkupdate()
    {
        $TabId=$this->TabId;
        $FieldId=$this->FieldId;
        $ModuleName=$this->ModuleName;
        $TableName=$this->TableName;
      
        
        $id =  Yii::$app->user->id;
       
        
        $model=new AccessCheck();
        $tabs=$model->tabs($id,$ModuleName);
        $profile=$model->profile($id,$tabs,$ModuleName);
        $modelaccess=$model->moduleaccess($id,$profile,$tabs);
        $rolebasedrecord=$model->rolebasedrecord($id,$profile);
        $hasadminpower = $model->hasadminpower($profile);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Set response format as JSON

        $request = \Yii::$app->request;

        // Check if the request is POST
        if ($request->isPost) {
            // Retrieve data from the POST request
            $leadIds = $request->post('hiddenLeadIds'); // Comma-separated IDs (e.g., "1,2,4")
            $fieldcolumn = $request->post('selectedValue'); // Field ID
            $userInput = $request->post('userInput'); // User-provided value to update
                //print_r($_POST);die;
            // Validate input
            if (empty($leadIds) || empty($fieldcolumn) || $userInput === null) {
                return [
                    'success' => false,
                    'message' => 'Invalid input data.',
                ];
            }

            // Convert lead IDs to an array
            $leadIdsArr = explode(',', $leadIds);
            $leadIdsArray = array_map('intval', $leadIdsArr); // Ensure the values are integers
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Fetch the column name from the `field` table based on `fieldid`
                $fieldtable = (new \yii\db\Query())
                    ->select('tablename')
                    ->from('field')
                    ->where(['columnname' => $fieldcolumn,'tabid'=>$TabId])
                    ->scalar();
                    //echo $field;die;


                if (!$fieldtable) {
                    return [
                        'success' => false,
                        'message' => 'Invalid field.',
                    ];
                }
                 foreach ($leadIdsArr as $valueid) {
                    // echo $valueid;die;
                    //get old values
                     $oldvalue = Yii::$app->db->createCommand("
                        SELECT $fieldcolumn 
                        FROM $fieldtable 
                        WHERE $FieldId = :valueid
                    ")
                    ->bindValue(':valueid', $valueid)
                    ->queryScalar();
                    //echo $oldvalue;die;
                    $oldattrbute =  array($fieldcolumn => $oldvalue);
                    $newattrbute =  array($fieldcolumn => $userInput);
                    // print_r($newattrbute);die;
                    $modlog = new ModtrackerBasic();
                    $auditstatus = 1;
                    $modlog->auditlog($oldattrbute,$newattrbute,$ModuleName,$valueid,$auditstatus,Yii::$app->user->id);
                    # code...
                     // Perform the bulk update
                    $affectedRows = \Yii::$app->db->createCommand()
                    ->update(
                        $fieldtable,        // Table name
                        [$fieldcolumn => $userInput],   // Column to update and its new value
                        [$FieldId => $valueid] // Condition: leadid in array
                    )
                    ->execute();
                }
                $transaction->commit();

               

               

                return [
                    'success' => true,
                    'message' => "Bulk update successful. ",//$affectedRows rows updated.",
                ];
            } catch (\Exception $e) {
                $transaction->rollBack();

                return [
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Invalid request method.',
        ];
    }
	 public function getHistory($id)
				{
					if (!is_numeric($id)) {
						throw new \yii\web\BadRequestHttpException('Invalid Record ID.');
					}

					$connection = Yii::$app->db;

					try {
						$command = $connection->createCommand("
							SELECT `id`, `fieldname`, `prevalue`, `postvalue` FROM `modtracker_detail` 
							WHERE id = :id
						")->bindValue(":id", $id);

						return $command->queryAll();
					} catch (\Exception $e) {
						Yii::error($e->getMessage(), __METHOD__);
						throw new \yii\web\ServerErrorHttpException('An error occurred while fetching the record.');
					}
				}

	public function actionDetailhistory($Record)
			{
				if (!is_numeric($Record)) {
					throw new \yii\web\BadRequestHttpException('Invalid Record ID.');
				}

				$connection = Yii::$app->db;

				try {
					// Fetch basic record data
					$columns = $connection->createCommand("
						SELECT `id`, `crmid`, `module`, `whodid`, `changedon`, `status` FROM `modtracker_basic` 
						WHERE crmid = :id
					")->bindValue(":id", $Record)->queryOne();
					//echo "SELECT `id`, `crmid`, `module`, `whodid`, `changedon`, `status` FROM `modtracker_basic`						WHERE id =$Record ";
			// print_r($columns); die;
					if (!$columns) {
						throw new \yii\web\NotFoundHttpException('Record not found in basic tracker.');
					}

					// Log the fetched columns
					Yii::info('Fetched columns: ' . json_encode($columns), __METHOD__);

					// Fetch detailed history using the ID
					$recordData = $this->getHistory($columns['crmid']); // Adjust key if necessary
					
					if (!$recordData) {
						Yii::error('No history found for id: ' . $columns['id'], __METHOD__);
						throw new \yii\web\NotFoundHttpException('Detailed history not found.');
					}

					// Optional logging
					if (!empty($recordData['fieldname'])) {
						Yii::info("Fieldname: " . $recordData['fieldname'], __METHOD__);
					}
					//print_r($recordData);  
					// Return the result as JSON
					return $this->asJson($recordData);

				} catch (\Exception $e) {
					Yii::error($e->getMessage(), __METHOD__);
					throw new \yii\web\ServerErrorHttpException('An error occurred while processing the request.');
				}
			}
			
			
}
