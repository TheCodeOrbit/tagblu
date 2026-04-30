<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Reference;
use backend\models\AccessCheck;
$baseUrl = Yii::$app->HomeUrl;
if (!empty($relatemodules)) {
    //print_r($relatemodules);die;

    $i = 1;
    $cnt = 0;
    foreach ($relatemodules as $key => $value) {
        $head = array();
        $ColumnList = array();
        $mval = '';
        //get count of records
        if ($value['related_recordfieldnme'] == 'related_to_id') {
            // echo $sql = "select count(".$value['related_tablekeyid'].") as cnt from `".$value['related_table']."` where related_to ='$TabId'  and related_to_id=$Recordid";
            // $sql = "select count(" . $value['related_tablekeyid'] . ") as cnt from `" . $value['related_table'] . "` where related_to =:module  and related_to_id=:record and ownerid=:ownerid";
            $sql = "select count(" . $value['related_tablekeyid'] . ") as cnt from `" . $value['related_table'] . "` where related_to =:module  and related_to_id=:record ";
            $v = Yii::$app->db->createCommand($sql)
                ->bindValue(":module", $TabId)
                ->bindValue(":record", $Recordid)
                // ->bindValue(":ownerid", Yii::$app->user->id)
                ->queryOne();
            $cnt = $v['cnt'];
            if ($cnt > 0) {
                //get header view columns

                $sql_h = "select fieldname as showfield,uitype from field where tabid = :related_module and headerview=1";
                $vhead = Yii::$app->db->createCommand($sql_h)
                    ->bindValue(":related_module", $value['related_module'])
                    ->queryOne();

                if (!empty($vhead)) {
                    $showfield = $vhead['showfield'];
                    //get show fields from table
                    if (!empty($showfield)) {
                        // print_r($showfield);                               
                        $sql_h = "select " . $showfield . " from `" . $value['related_table'] . "` where related_to =:module  and related_to_id=:record limit 1";
                        $head = Yii::$app->db->createCommand($sql_h)
                            ->bindValue(":module", $TabId)
                            ->bindValue(":record", $Recordid)
                            ->queryOne();
                        if (!empty($head))
                            $showfieldval = $head[$showfield];
                        else
                            $showfieldval = '';
                    }
                }
            }
        } else {

            // $sql = "select count(" . $value['related_tablekeyid'] . ") as cnt from `" . $value['related_table'] . "` where " . $value['related_recordfieldnme'] . " =:record  and ownerid=:ownerid";
            $sql = "select count(" . $value['related_tablekeyid'] . ") as cnt from `" . $value['related_table'] . "` where " . $value['related_recordfieldnme'] . " =:record ";
            $v = Yii::$app->db->createCommand($sql)
                ->bindValue(":record", $Recordid)
                // ->bindValue(":ownerid", Yii::$app->user->id)
                ->queryOne();
            $cnt = $v['cnt'];
            if ($cnt > 0) {
                //get header view columns
                $sql_h = "select fieldname as showfield,uitype from field where tabid = :related_module and headerview=1";
                $vhead = Yii::$app->db->createCommand($sql_h)
                    ->bindValue(":related_module", $value['related_module'])
                    ->queryOne();
                // print_r($vhead);
                if (!empty($vhead)) {
                    $showfield = $vhead['showfield'];
                    //get show fields from table 
                    if (!empty($showfield)) {


                        $sql_h = "select " . $showfield . " from `" . $value['related_table'] . "` where `" . $value['related_fieldname'] . "`=:record limit 1";
                        $head = Yii::$app->db->createCommand($sql_h)
                            // ->bindValue(":module", $TabId)
                            ->bindValue(":record", $Recordid)
                            ->queryOne();

                        if ($showfield == 'contacts_id' && !empty($head)) {
                            //get contact name from  contatcs
                            $sql_h = "select concat(first_name,' ',if(last_name is null,'',last_name)) as fullname from contacts where contacts_id=:record limit 1";
                            $headval = Yii::$app->db->createCommand($sql_h)
                                // ->bindValue(":module", $TabId)
                                ->bindValue(":record", $head[$showfield])
                                ->queryOne();
                            $showfieldval = $headval['fullname'];
                        } else {
                            $showfieldval = $head[$showfield];
                        }
                    }
                }
            }
        }
        if ($cnt > 0) {
            $id = Yii::$app->user->id;
            $model = new AccessCheck();
            $tabs = $model->tabs($id, $value['related_module']);
            $profile = $model->profile($id, $tabs, $value['related_module']);
            $modelaccess = $model->moduleaccess($id, $profile, $tabs);
            $rolebasedrecord = $model->rolebasedrecord($id, $profile);
            $hasadminpower = $model->hasadminpower($profile);
            $modulepermission = $model->modulepermission($profile, $tabs);

            $model1 = new Reference($value['related_table'], $value['related_tablekeyid']);
            list($ColumnList, $RecordList, $totalitemcount) = $model1->getListRecord_relatedsidemenu('', '', $rolebasedrecord, $modulepermission, $value['related_module']);
            // print_r($ColumnList);
        }

        //get module name from modulename
        $sql = "select tablabel from tab where name=:module";
        $cmd = Yii::$app->db->createCommand($sql)->bindValue(":module", $value['modulename'])->queryOne();
        if ($cmd)
            $modulelabel = $cmd['tablabel'];
        else
            $modulelabel = '';


?>
        <div class="collapse-container">
            <!-- related Section -->
            <label class="collapse-header" for="toggle-<?= ucfirst($value['modulename']); ?>">
                <img src="<?= $baseUrl ?>thememain/img/module-icon/<?= $value['modulename']; ?>.png"
                    class="<?= ucfirst($value['modulename']); ?>" alt="<?= ucfirst($value['modulename']); ?> Icon">
                <?= $modulelabel ?> (<?= $cnt; ?>)

                <a href="<?= $baseUrl; ?><?= $value['modulename']; ?>/create?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>"><i class="fa-solid fa-plus" title = "add new"></i></a>

                <i class="fa-solid fa-angle-down icon-right"></i>
            </label>
            <input type="checkbox" id="toggle-<?= ucfirst($value['modulename']); ?>" onchange="toggleCollapseIcon(this)">
            <div class="collapse-content">
                <?php
                if (!empty($head)) { ?>
                    <p style="color:#1391ff; font-size:16px"><?= $showfieldval; ?></p>
                <?php
                } ?>
                <?php
                if (!empty($RecordList)) {
                    foreach ($RecordList as $key => $mval) {
                        if ($key != "RecordId") {
                            if (isset($ColumnList[$key])) { ?>
                                <p><?= $ColumnList[$key] ?>: <?= $mval ?></p>
                <?php }
                        }
                    }
                }
                ?>
                <?php if ($cnt > 0) { ?>
                    <a href="<?= $baseUrl; ?><?= $value['modulename']; ?>/list?sourcemodule=<?= $TabId; ?>&sourceid=<?= $Recordid; ?>">View
                        All</a>
                       
                <?php } ?>
               
            </div>
        </div>
<?php
        $i++;
    }
} ?>
<!-- <div class="collapse-container">
    -- Document Section -->
<!-- <label class="collapse-header" for="toggle-documents">
        <img src="<?= $baseUrl ?>thememain/img/detail/file_120.png" class="document" alt="Document Icon">
        Document
        (0)
        <i class="fa-solid fa-angle-down icon-right"></i>
    </label>
    <input type="checkbox" id="toggle-documents" onchange="toggleCollapseIcon(this)">
    <div class="collapse-content">
        <div class="upload-section">
            <button class="upload-btn">Upload Files</button>
            <p>Or drop files</p>
        </div>
    </div>
</div> -->
<script>
    function toggleCollapseIcon(checkbox) {
        const labelIcon = checkbox.parentNode.querySelector('.icon-right');
        if (checkbox.checked) {
            labelIcon.classList.remove('fa-angle-down');
            labelIcon.classList.add('fa-angle-up');
        } else {
            labelIcon.classList.remove('fa-angle-up');
            labelIcon.classList.add('fa-angle-down');
        }
    }
</script>
<script>
    // Show popup
    function showPopup(popupId) {
        document.getElementById(popupId).style.display = 'flex';
    }

    // Hide popup
    function hidePopup(event) {
        if (event.target.classList.contains('popup-overlay') || event.target.classList.contains('close-btn')) {
            event.target.closest('.popup-overlay').style.display = 'none';
        }
    }
</script>
<script>
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', () => {
            const content = header.nextElementSibling;

            header.classList.toggle('active');
            if (content.style.display === 'block') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
            }
        });
    });
</script>


<script>
    document.querySelectorAll('.dropdown-btn').forEach(button => {
        button.addEventListener('click', () => {
            const dropdown = button.closest('.dropdown');
            dropdown.classList.toggle('show');
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dropdown => dropdown.classList.remove('show'));
        }
    });
</script>


<script>
    document.querySelectorAll('.dropdown-btn').forEach(button => {
        button.addEventListener('click', () => {
            const dropdown = button.closest('.dropdown');
            dropdown.classList.toggle('show');
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dropdown => dropdown.classList.remove('show'));
        }
    });
</script>
<?php
die;?>