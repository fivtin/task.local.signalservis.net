<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Employe;

/**
 * SearchEmploye represents the model behind the search form about `app\models\Employe`.
 */
class EmployeSearch extends Employe
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eid', 'did', 'status', 'tab_task'], 'integer'],
            [['fio', 'fio_short', 'post', 'dgroup', 'note'], 'safe'],
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
        $query = Employe::find();

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
            'eid' => $this->eid,
            'did' => $this->did,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'fio', $this->fio])
            ->andFilterWhere(['like', 'fio_short', $this->fio_short])
             ->andFilterWhere(['like', 'post', $this->post])
                ->andFilterWhere(['like', 'dgroup', $this->dgroup]);

        return $dataProvider;
    }
}
