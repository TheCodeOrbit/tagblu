<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vc_director_details".
 *
 * @property int $vc_director_details_id
 * @property int $vendoraccid
 * @property int|null $director_pan
 * @property string|null $pan_copy_attachment
 * @property string|null $director_aadhar_card
 * @property string|null $aadhar_card_attachment
 * @property string|null $photo
 * @property string|null $email
 * @property string|null $phone_no
 */
class VcDirectorDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vc_director_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vendoraccid'], 'required'],
            [['vendoraccid'], 'integer'],
            [['pan_copy_attachment', 'aadhar_card_attachment', 'photo', 'email'], 'string', 'max' => 200],
            [['director_aadhar_card','director_pan'], 'string', 'max' => 20],
            [['phone_no'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vc_director_details_id' => 'Vc Director Details ID',
            'vendoraccid' => 'Vendoraccid',
            'director_pan' => 'Director Pan',
            'pan_copy_attachment' => 'Pan Copy Attachment',
            'director_aadhar_card' => 'Director Aadhar Card',
            'aadhar_card_attachment' => 'Aadhar Card Attachment',
            'photo' => 'Photo',
            'email' => 'Email',
            'phone_no' => 'Phone No',
        ];
    }

   
    public function saveVcDirectorDetails($entityId)
    {
         if (empty($_POST['vc_director_details']) || !is_array($_POST['vc_director_details'])) {
            return false;
        }
        else{
             //delete old record from child table
            
             $sql = "Delete from vc_director_details where vc_director_details_id = :vc_director_details_id";
             Yii::$app->db->createCommand($sql)->bindValue(":vc_director_details_id", $entityId)->execute();
          }
        // $attachmentDetails = $_POST['inspection_full_product_detail'];
        $uploadedFiles = $_FILES['vc_director_details']??null;
        $grn_items=$_POST['vc_director_details'];
		if(count($grn_items)>0)
		{
            $i=0;
			foreach($grn_items as $prodkey=>$product_detail)
			{
			    $product_detail['vendoraccid']=$entityId;  
                
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
			$product_detail_obj=new VcDirectorDetails();	
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
}
