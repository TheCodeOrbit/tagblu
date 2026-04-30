<?php

namespace backend\modules\opportunitycontactrole\controllers;

use common\controllers\ModuleController;
use Yii;
/**
 * Default controller for the `leads` module
 */
// class DefaultController extends Controller
class DefaultController extends ModuleController
{
    public $layout = 'contactrole';
    public $ModuleName = 'opportunitycontactrole';
    public $FieldId = 'contact_roleid';
    public $TableName = 'opportunity_contact_role';
    public $TabLabel = 'Opportunity Contact Role';


    public $TabId = '53';
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
        $query = filter_var($_GET['query'],FILTER_SANITIZE_SPECIAL_CHARS);
        $opportunity = filter_var($_GET['opportunity'],FILTER_SANITIZE_SPECIAL_CHARS);
        // Query to search records (adjust the table and column names as needed)
         $sql = "SELECT contacts_id as id,concat(first_name,' ',last_name) as name FROM contacts WHERE
        contacts_id not in (select contacts_id from opportunity_contact_role where  opportunity_id=:opportunity) and (vendor_account_name in (select vendor_account_name from opportunity where opportunity_id=:opportunity))
        and concat(first_name,' ',last_name) LIKE '%$query%'";

        $result = Yii::$app->db->createCommand($sql)->bindValue(":opportunity",$opportunity)
        ->queryAll();

        if ($result) {
            // Loop through the results and display them
            foreach($result as $row) {
                // Display the result with the corresponding ID in a data attribute
                echo "<div class='result-item' data-id='" . $row['id'] . "'>" . $row['name'] . "</div>";
            }
        } else {
            echo "No results found.";
        }
    }
}
