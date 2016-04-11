<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Domain;

/**
 * DomainSearch represents the model behind the search form about `app\models\Domain`.
 */
class DomainSearch extends Domain
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain_id', 'domain_status'], 'integer'],
            [['domain_createtime', 'domain_name'], 'safe'],
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
        $query = Domain::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'domain_id' => $this->domain_id,
            'domain_createtime' => $this->domain_createtime,
            'domain_status' => $this->domain_status,
        ]);

        $query->andFilterWhere(['like', 'domain_name', $this->domain_name]);

        return $dataProvider;
    }
}
