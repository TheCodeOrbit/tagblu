<?php

namespace backend\modules\sourcingdealcontactroles\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'contactrole';
    public $ModuleName = 'sourcingdealcontactroles';
    public $FieldId = 'contact_roleid';
    public $TableName = 'sourcingdeal_contact_role';
    public $TabLabel = 'Sourcing Deal Contact Role';


    public $TabId = '58';
    /**
     * Renders the index view for the module
     * @return string
     */
    //  public function beforeAction($action)
// {
//     $this->layout = '@app/views/layouts/main';  // Set the main layout before each action
//     return parent::beforeAction($action);
// }

    public function actionExample()
    {
        return $this->render('index');
    }

    public function actionSearch()
    {
        // Query to search records (adjust the table and column names as needed)
        $sql = "SELECT contacts_id as id,concat(first_name,' ',last_name) as name FROM contacts WHERE name LIKE '%$query%'";

        $result = Yii::$app->db->createCommand($sql)->queryAll();

        if ($result->num_rows > 0) {
            // Loop through the results and display them
            while ($row = $result->fetch_assoc()) {
                // Display the result with the corresponding ID in a data attribute
                echo "<div class='result-item' data-id='" . $row['id'] . "'>" . $row['name'] . "</div>";
            }
        } else {
            echo "No results found.";
        }
    }
}
