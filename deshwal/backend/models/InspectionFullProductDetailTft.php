<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inspection_full_product_detail_tft".
 *
 * @property int $inspection_product_detail_id
 * @property int $inspection_id
 * @property int|null $prod_category
 * @property string|null $serial_number
 * @property int|null $make
 * @property int|null $model
 * @property int|null $screen_size
 * @property int|null $screen_broken
 * @property int|null $physical_dent
 * @property int|null $quantity
 * @property string|null $image_screen
 * @property string|null $image_front
 * @property string|null $image_back
 * @property int|null $critical
 * @property string|null $remarks
 */
class InspectionFullProductDetailTft extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspection_full_product_detail_tft';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inspection_id'], 'required'],
            [['inspection_id', 'prod_category', 'make', 'model', 'screen_size', 'screen_broken', 'physical_dent', 'critical'], 'integer'],
            [['serial_number', 'image_screen', 'image_front', 'image_back', 'remarks'], 'string', 'max' => 200],
            [[ 'quantity',], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inspection_product_detail_id' => 'Inspection Product Detail ID',
            'inspection_id' => 'Inspection ID',
            'prod_category' => 'Prod Category',
            'serial_number' => 'Serial Number',
            'make' => 'Make',
            'model' => 'Model',
            'screen_size' => 'Screen Size',
            'screen_broken' => 'Screen Broken',
            'physical_dent' => 'Physical Dent',
            'quantity' => 'Quantity',
            'image_screen' => 'Image Screen',
            'image_front' => 'Image Front',
            'image_back' => 'Image Back',
            'critical' => 'Critical',
            'remarks' => 'Remarks',
        ];
    }
    public function saveAttachedFiles($file)
    {
        if (empty($file)) {
            return "";
        }

         // Security: Validate file extension and MIME type
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'xls', 'xlsx', 'zip', 'eml','msg'];
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            // For .eml files
            "message/rfc822",           // standard MIME type for .eml
            "application/vnd.ms-outlook", // sometimes used for Outlook .msg/.eml
            'application/zip',
            'multipart/x-zip',
            'application/x-compressed',
            "application/x-zip-compressed","application/octet-stream"
        ];

        $fileExtension = pathinfo($file->name, PATHINFO_EXTENSION);
        if ($fileExtension)
            $fileExtension = strtolower($fileExtension);
        if (!in_array($fileExtension, $allowedExtensions) || !in_array($file->type, $allowedMimeTypes)) {
            return ['success' => false, 'message' => "Invalid file type. $fileExtension is not allowed"];
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
        // echo "<br/>".$file->name."=>".$filesavepath;
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
            return ['success' => false, 'message' => 'Failed to save the file.'.$file->name];
        }
    }

    public function saveInspectionFullProductDetailTft($entityId)
    {
        if (empty($_POST['inspection_full_product_detail_tft']) || !is_array($_POST['inspection_full_product_detail_tft'])) {
            return false;
        }
        else{
             //delete old record from child table
            
             $sql = "Delete from inspection_full_product_detail_tft where inspection_id = :inspection_id";
             Yii::$app->db->createCommand($sql)->bindValue(":inspection_id", $entityId)->execute();
       }
        // file attach code added by ptpatel on date 23-04-25
        // $attachmentDetails = $_POST['inspection_full_product_detail'];
        $uploadedFiles = $_FILES['inspection_full_product_detail_tft']??null;
        $grn_items=$_POST['inspection_full_product_detail_tft'];
		if(count($grn_items)>0)
		{
            $i=0;
			foreach($grn_items as $prodkey=>$product_detail)
			{
			    $product_detail['inspection_id']=$entityId;  
                //code added by ptpatel
                if($uploadedFiles)
                {
                foreach ($uploadedFiles['name'][$prodkey] as $field => $fileGroup) {
                        $hasNewUpload = isset($uploadedFiles['name'][$prodkey][$field]) &&
                                        $uploadedFiles['error'][$prodkey][$field] === UPLOAD_ERR_OK;
                
                        if ($hasNewUpload) {
                            // Create UploadedFile manually
                            $file = new \yii\web\UploadedFile();

                            $file->name = $uploadedFiles['name'][$prodkey][$field];
                            $file->type = $uploadedFiles['type'][$prodkey][$field];
                            $file->tempName = $uploadedFiles['tmp_name'][$prodkey][$field];
                            $file->error = $uploadedFiles['error'][$prodkey][$field];
                            $file->size = $uploadedFiles['size'][$prodkey][$field];
                
                            $result = $this->saveAttachedFiles($file); // your custom method
                            // echo "save attach file";print_r($product_detail);die;
                            if ($result['success']) {
                                $product_detail[$field] = $result['fileName']; // save file name
                                // echo "<pre>";print_r($product_detail);die;
                            } else {
                                echo $result['message'] ?? 'File saving failed';
                                die();
                            }
                        }elseif (!empty($grn_items[$prodkey]["{$field}_hidden"])) {
                            $product_detail["{$field}"] = $product_detail["{$field}_hidden"];
                        }
                    // }
                }
                }
                //end code added by ptpatel
			$product_detail_obj=new InspectionFullProductDetailTft();	
			$product_detail_obj->attributes=$product_detail;
            // print_r($product_detail_obj->attributes);die;
			$product_detail_obj->validate();
			$product_detail_obj->save(false);
			}
            $i++;
		}

		// if(count($grn_items)>0)
		// {
		// 	foreach($grn_items as $product_detail)
		// 	{
		// 	$product_detail['inspection_id']=$entityId;
		// 	$product_detail_obj=new InspectionFullProductDetail;	
		// 	$product_detail_obj->attributes=$product_detail;
        //     // print_r($product_detail_obj->attributes);die;
		// 	$product_detail_obj->validate();
		// 	$product_detail_obj->save(false);
		// 	}
		// }
    }
}
