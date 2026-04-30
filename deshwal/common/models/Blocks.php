<?php

namespace common\models;

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
            [['blocklabel','blocktype'], 'string', 'max' => 100],
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
            'blocktype'=>'Block Type'
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

   public function getFields()
    {
        return $this->hasMany(Field::class, ['block' => 'blockid']) 
            // ->where(['edit_view' => 1])
            ->orderBy(['sequence' => SORT_ASC]);
    }

}
