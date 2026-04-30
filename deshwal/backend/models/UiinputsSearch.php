<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Uiinputs;

/**
 * UiinputsSearch represents the model behind the search form of `backend\models\Uiinputs`.
 */
class UiinputsSearch extends Uiinputs
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'checkbox'], 'integer'],
            [['name', 'hidden_field', 'password', 'textarea', 'file', 'listbox', 'dropdown_single', 'checkboxlist', 'radio_button', 'referencetype', 'DateTimePicker', 'label', 'dropdown_multipe', 'MonthYearPicker', 'DateTime', 'date', 'BatchList', 'maskingdate'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Uiinputs::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'checkbox' => $this->checkbox,
            'DateTimePicker' => $this->DateTimePicker,
          
            'date' => $this->date,
            'maskingdate' => $this->maskingdate,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'hidden_field', $this->hidden_field])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'textarea', $this->textarea])
            ->andFilterWhere(['like', 'file', $this->file])
            ->andFilterWhere(['like', 'listbox', $this->listbox])
            ->andFilterWhere(['like', 'dropdown_single', $this->dropdown_single])
            ->andFilterWhere(['like', 'checkboxlist', $this->checkboxlist])
            ->andFilterWhere(['like', 'radio_button', $this->radio_button])
            ->andFilterWhere(['like', 'referencetype', $this->referencetype])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'dropdown_multipe', $this->dropdown_multipe])
            ->andFilterWhere(['like', 'MonthYearPicker', $this->MonthYearPicker])
            ->andFilterWhere(['like', 'BatchList', $this->BatchList]);

        return $dataProvider;
    }
}
