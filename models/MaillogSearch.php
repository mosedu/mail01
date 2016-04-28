<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Maillog;

/**
 * MaillogSearch represents the model behind the search form about `app\models\Maillog`.
 */
class MaillogSearch extends Maillog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mlog_id', 'mlog_mail_id', 'mlog_type'], 'integer'],
            [['mlog_createtime', 'mlog_text'], 'safe'],
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
        $query = Maillog::find();

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
            'mlog_id' => $this->mlog_id,
            'mlog_createtime' => $this->mlog_createtime,
            'mlog_mail_id' => $this->mlog_mail_id,
            'mlog_type' => $this->mlog_type,
        ]);

        $query->andFilterWhere(['like', 'mlog_text', $this->mlog_text]);

        return $dataProvider;
    }
}
