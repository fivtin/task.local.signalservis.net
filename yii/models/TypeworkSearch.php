<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Typework;

/**
 * TypeworkSearch represents the model behind the search form about `app\models\Typework`.
 */
class TypeworkSearch extends Typework
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['twid', 'status', 'did'], 'integer'],
            [['title', 'detail', 'info'], 'safe'],
            [['cost'], 'number'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Typework::find();

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
            'twid' => $this->twid,
            'status' => $this->status,
            'cost' => $this->cost,
            'did' => $this->did,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'detail', $this->detail])
            ->andFilterWhere(['like', 'info', $this->info]);

        return $dataProvider;
    }
}
