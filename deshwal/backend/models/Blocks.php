<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blocks".
 *
 * @property int $blockid
 * @property int $tabid
 * @property string $blocklabel
 * @property int|null $sequence
 * @property int|null $show_title
 * @property int $visible
 * @property int $create_view
 * @property int $edit_view
 * @property int $detail_view
 * @property int $display_status
 * @property int $iscustom
 * @property string $blocktype
 *
 * @property YiiTab $tab
 */
class Blocks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $_fields;
    public static function tableName()
    {
        return 'blocks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['blockid', 'tabid', 'blocklabel'], 'required'],
            [['blockid', 'tabid', 'sequence', 'show_title', 'visible', 'create_view', 'edit_view', 'detail_view', 'display_status', 'iscustom'], 'integer'],
            [['blocklabel', 'blocktype'], 'string', 'max' => 100],
            [['blockid'], 'unique'],
            [['tabid'], 'exist', 'skipOnError' => true, 'targetClass' => Tab::class, 'targetAttribute' => ['tabid' => 'tabid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'blockid' => 'Blockid',
            'tabid' => 'Tabid',
            'blocklabel' => 'Blocklabel',
            'sequence' => 'Sequence',
            'show_title' => 'Show Title',
            'visible' => 'Visible',
            'create_view' => 'Create View',
            'edit_view' => 'Edit View',
            'detail_view' => 'Detail View',
            'display_status' => 'Display Status',
            'iscustom' => 'Iscustom',
            'blocktype' => 'Block Type'
        ];
    }

    /**
     * Gets query for [[Tab]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTab()
    {
        return $this->hasOne(Tab::class, ['tabid' => 'tabid']);
    }

    // public function getFields()
    //  {
    //      return $this->hasMany(Field::class, ['block' => 'blockid']) 
    //      //->join('fieldtype', 'fieldtype.uitype', '=', 'field.uitype')
    //          // ->where(['edit_view' => 1])
    //          ->orderBy(['field.sequence' => SORT_ASC]);
    //  }
    public function getEdititemsfields()
    {
        return $this->hasMany(Field::class, ['block' => 'blockid'])
            //->join('fieldtype', 'fieldtype.uitype', '=', 'field.uitype')
            ->where(['field.edit_view' => 1])
            ->orderBy(['field.sequence' => SORT_ASC]);
    }
    public function getEditfields()
    {
        return $this->hasMany(Field::class, ['block' => 'blockid'])
            //->join('fieldtype', 'fieldtype.uitype', '=', 'field.uitype')
            ->where(['field.edit_view' => 1])
            ->orderBy(['field.sequence' => SORT_ASC]);
    }
    public function getCreatefields()
    {
        return $this->hasMany(Field::class, ['block' => 'blockid'])
            //->join('fieldtype', 'fieldtype.uitype', '=', 'field.uitype')
            ->where(['field.create_view' => 1])
            ->orderBy(['field.sequence' => SORT_ASC]);
    }
    public function getDetailfields()
    {
        return $this->hasMany(Field::class, ['block' => 'blockid'])
            //->join('fieldtype', 'fieldtype.uitype', '=', 'field.uitype')
            ->where(['field.detail_view' => 1])
            ->orderBy(['field.sequence' => SORT_ASC]);
    }
    public function getMasseditfields()
    {
        return $this->hasMany(Field::class, ['block' => 'blockid'])
            //->join('fieldtype', 'fieldtype.uitype', '=', 'field.uitype')
            ->where(['field.masseditable' => 1])
            ->orderBy(['field.sequence' => SORT_ASC]);
    }

    public function getQuickcreatefields()
    {
        return $this->hasMany(Field::class, ['block' => 'blockid'])
            //->join('fieldtype', 'fieldtype.uitype', '=', 'field.uitype')
            ->where(['field.masseditable' => 1])
            ->orderBy(['field.quickcreatesequence' => SORT_ASC]);
    }
    public function getKanbanfields()
    {
        return $this->hasMany(Field::class, ['block' => 'blockid'])
            //->join('fieldtype', 'fieldtype.uitype', '=', 'field.uitype')
            ->where(['field.kanbanview' => 1]);
    }
    

    // Setter for fields
    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    public function getBlockDetail($moduleName = "")
    {
        // Use ActiveRecord to fetch BlockDetail with a relation to Fields
        $blockDetail = self::find()
            ->with('createfields') // Assumes a relation named 'fields' is defined
            ->where(['blockid' => $this->blockid])
            ->one();

        // if ($blockDetail && $blockDetail->createfields) {
        //     foreach ($blockDetail->createfields as $fieldKey => $field) {
        //         // Fetch the FieldType record for the current field
        //         $fieldTypeRecord = FieldType::find()
        //             ->where(['uitype' => $field->uitype])
        //             ->one();

        //         if ($fieldTypeRecord) {
        //             // Set fieldtype and classname properties
        //             $blockDetail->fields[$fieldKey]->fieldtype = $fieldTypeRecord->getFieldType();
        //             if (empty($field->classname)) {
        //                 $blockDetail->fields[$fieldKey]->classname = $fieldTypeRecord->classname;
        //             }
        //         }

        //         // Special handling for uitype == 8
        //         if ($field->uitype == 8) {
        //             $pickList = new PickList();
        //             $pickList->fieldid = $field->fieldid;
        //             $blockDetail->fields[$fieldKey]->fieldoptions = $pickList->getPickListOption($moduleName);
        //         }
        //     }
        // }

        return $blockDetail;
    }


}
