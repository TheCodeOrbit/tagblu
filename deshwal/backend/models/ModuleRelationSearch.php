<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ModuleRelation;

/**
 * ModuleRelationSearch represents the model behind the search form of `backend\models\ModuleRelation`.
 */
class ModuleRelationSearch extends ModuleRelation
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'source_module', 'related_module', 'sequence', 'deleted'], 'integer'],
            [['related_table', 'related_tablekeyid', 'related_fieldname', 'related_recordfieldnme', 'relation_with_account', 'actions', 'related_columns'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = ModuleRelation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'source_module' => $this->source_module,
            'related_module' => $this->related_module,
            'sequence' => $this->sequence,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'related_table', $this->related_table])
            ->andFilterWhere(['like', 'related_tablekeyid', $this->related_tablekeyid])
            ->andFilterWhere(['like', 'related_fieldname', $this->related_fieldname])
            ->andFilterWhere(['like', 'related_recordfieldnme', $this->related_recordfieldnme])
            ->andFilterWhere(['like', 'relation_with_account', $this->relation_with_account])
            ->andFilterWhere(['like', 'actions', $this->actions])
            ->andFilterWhere(['like', 'related_columns', $this->related_columns]);

        return $dataProvider;
    }
}
