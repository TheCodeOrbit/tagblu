<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments_attachment_detail".
 *
 * @property int $payments_attachment_id
 * @property int $paymentsid
 * @property string|null $document_name
 * @property string|null $upload
 * @property int $deleted
 */
class PaymentsAttachmentDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payments_attachment_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payments_id'], 'required'],
            [['payments_id', 'deleted'], 'integer'],
            [['document_name', 'upload'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'payments_attachment_id' => 'Payments Attachment ID',
            'payments_id' => 'Payments id',
            'document_name' => 'Document Name',
            'upload' => 'Upload',
            'deleted' => 'Deleted',
        ];
    }

    //code by bhavitha

    public function savePaymentsAttachmentDetail($entityId)
    {

        if (isset($_POST['payments_attachment_detail'])) {
            $attachmentDetails = $_POST['payments_attachment_detail'];
            $uploadedFiles = $_FILES['payments_attachment_detail'];
    
            foreach ($attachmentDetails as $key => $detail) {
                $detail['payments_id'] = $entityId;
    
                $hasNewUpload = isset($uploadedFiles['name'][$key]['upload']) &&
                                $uploadedFiles['error'][$key]['upload'] === UPLOAD_ERR_OK;
    
                if ($hasNewUpload) {
                    // Construct UploadedFile manually
                    $file = new \yii\web\UploadedFile();
                    $file->name = $uploadedFiles['name'][$key]['upload'];
                    $file->type = $uploadedFiles['type'][$key]['upload'];
                    $file->tempName = $uploadedFiles['tmp_name'][$key]['upload'];
                    $file->error = $uploadedFiles['error'][$key]['upload'];
                    $file->size = $uploadedFiles['size'][$key]['upload'];
    
                    $result = $this->saveAttachedFiles($file);
                    if ($result['success']) {
                        $detail['upload'] = $result['fileName']; // set attachment ID
                    } else {
                        echo $result['message'] ?? 'File saving failed';
                        die();
                    }
                } elseif (!empty($detail['upload_hidden'])) {
                    // Use previously uploaded attachment ID
                    $detail['upload'] = $detail['upload_hidden'];
                }
    
                $attachmentObj = new PaymentsAttachmentDetail();
                $attachmentObj->attributes = $detail;
                $attachmentObj->validate();
                $attachmentObj->save(false);
            }
        }
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
}
