<?php
namespace common\models;
use Yii;
/**
 * Tab class.
 * Tab is the data structure for keeping
 * Tab form data. It is used by the 'Receipt' action of 'JPLController'.
 */

/**
 * This is the model class for table "tab".
 *
 * @property int $tabid
 * @property string $name
 * @property int $presence
 * @property int|null $tabsequence
 * @property string|null $tablabel
 * @property int|null $modifiedby
 * @property int|null $modifiedtime
 * @property int|null $customized
 * @property int|null $ownedby
 * @property int $isentitytype
 * @property int $trial
 * @property string|null $version
 * @property string|null $parent
 * @property string|null $source
 * @property int|null $issyncable
 * @property int|null $allowduplicates
 * @property int|null $sync_action_for_duplicates
 *
 * @property YiiBlocks[] $yiiBlocks
 * @property YiiCustomview[] $yiiCustomviews
 * @property YiiField[] $yiiFields
 */
class Tab extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tab';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tabid', 'name'], 'required'],
            [['tabid', 'presence', 'tabsequence', 'modifiedby', 'modifiedtime', 'customized', 'ownedby', 'isentitytype', 'trial', 'issyncable', 'allowduplicates', 'sync_action_for_duplicates'], 'integer'],
            [['name'], 'string', 'max' => 25],
            [['tablabel'], 'string', 'max' => 100],
            [['version'], 'string', 'max' => 10],
            [['parent'], 'string', 'max' => 30],
            [['source'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['tabid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tabid' => 'Tabid',
            'name' => 'Name',
            'presence' => 'Presence',
            'tabsequence' => 'Tabsequence',
            'tablabel' => 'Tablabel',
            'modifiedby' => 'Modifiedby',
            'modifiedtime' => 'Modifiedtime',
            'customized' => 'Customized',
            'ownedby' => 'Ownedby',
            'isentitytype' => 'Isentitytype',
            'trial' => 'Trial',
            'version' => 'Version',
            'parent' => 'Parent',
            'source' => 'Source',
            'issyncable' => 'Issyncable',
            'allowduplicates' => 'Allowduplicates',
            'sync_action_for_duplicates' => 'Sync Action For Duplicates',
        ];
    }

    /**
     * Gets query for [[YiiBlocks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBlocks()
    {
        return $this->hasMany(Blocks::class, ['tabid' => 'tabid']);
    }

    /**
     * Gets query for [[YiiCustomviews]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomviews()
    {
        return $this->hasMany(Customview::class, ['entitytype' => 'name']);
    }

    /**
     * Gets query for [[YiiFields]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFields()
    {
        return $this->hasMany(Field::class, ['tabid' => 'tabid']);
    }
	// public function relations()
	//     {
		
	// 	if($mineral_type_extracted == "Coal"){
	// 		$rel_condition = array(self::HAS_MANY, 'Block','tabid','condition'=>'Blocks.iron_view in (0,1)','order'=>'Blocks.sequence ASC','with'=>'Fields');
	// 	}else{
	// 		//'condition'=>'Blocks.iron_view in (0,2)',
	// 		$rel_condition = array(self::HAS_MANY, 'Block','tabid','condition'=>'Blocks.iron_view in (0,2)','order'=>'Blocks.sequence ASC','with'=>'Fields');
	// 	}
	// 	return array(
	// 	   'Blocks'=>$rel_condition,
	// 	   'DetailBlocks'=>array(self::HAS_MANY, 'Block', 'tabid','order'=>'DetailBlocks.sequence ASC','with'=>'DetailFields'),
	// 	   //'Fields'=>array(self::HAS_MANY, 'Field', 'tabid','order'=>'sequence ASC'),					
	// 	);
	//     }
	
}
