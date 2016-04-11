<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Mail;

/**
 * MailSearch represents the model behind the search form about `app\models\Mail`.
 */
class MailSearch extends Mail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mail_id', 'mail_domen_id', 'mail_status'], 'integer'],
            [['mail_createtime', 'mail_from', 'mail_fromname', 'mail_to', 'mail_toname', 'mail_text', 'mail_html'], 'safe'],
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
        $query = Mail::find();

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
            'mail_id' => $this->mail_id,
            'mail_domen_id' => $this->mail_domen_id,
            'mail_createtime' => $this->mail_createtime,
            'mail_status' => $this->mail_status,
        ]);

        $query->andFilterWhere(['like', 'mail_from', $this->mail_from])
            ->andFilterWhere(['like', 'mail_fromname', $this->mail_fromname])
            ->andFilterWhere(['like', 'mail_to', $this->mail_to])
            ->andFilterWhere(['like', 'mail_toname', $this->mail_toname])
            ->andFilterWhere(['like', 'mail_text', $this->mail_text])
            ->andFilterWhere(['like', 'mail_html', $this->mail_html]);

        return $dataProvider;
    }
}
