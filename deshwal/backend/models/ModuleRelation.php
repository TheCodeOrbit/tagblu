<?php

// namespace app\models;
namespace  backend\models;

use Yii;

/**
 * This is the model class for table "module_relation".
 *
 * @property int $id
 * @property int $source_module
 * @property int $related_module
 * @property string $related_table
 * @property string $related_tablekeyid
 * @property string $related_fieldname
 * @property string $related_recordfieldnme
 * @property string|null $relation_with_account
 * @property int $sequence
 * @property int $deleted
 * @property string $actions
 * @property string $related_columns
 */
class ModuleRelation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'module_relation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['relation_with_account'], 'default', 'value' => null],
            [['deleted'], 'default', 'value' => 0],
            [['source_module', 'related_module', 'related_table', 'related_tablekeyid', 'related_fieldname', 'related_recordfieldnme', 'sequence', 'actions', 'related_columns'], 'required'],
            [['source_module', 'related_module', 'sequence', 'deleted'], 'integer'],
            [['related_table', 'related_tablekeyid', 'related_fieldname', 'related_recordfieldnme'], 'string', 'max' => 255],
            [['relation_with_account'], 'string', 'max' => 200],
            [['actions', 'related_columns'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'source_module' => 'Source Module',
            'related_module' => 'Related Module',
            'related_table' => 'Related Table',
            'related_tablekeyid' => 'Related Tablekeyid',
            'related_fieldname' => 'Related Fieldname',
            'related_recordfieldnme' => 'Related Recordfieldnme',
            'relation_with_account' => 'Relation With Account',
            'sequence' => 'Sequence',
            'deleted' => 'Deleted',
            'actions' => 'Actions',
            'related_columns' => 'Related Columns',
        ];
    }

}
