<?php
namespace asb\yii2\modules\restapi_v0\models;

use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class PostSearch extends Post
{
    public $defaultOrder = ['create_time' => SORT_DESC];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [ // searchable fields
            [['id', 'user_id'], 'integer'],
            [['text'], 'string'],
            [['create_time', 'update_time'], 'safe'],
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
     * @param array $params search parameters
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->load($params);
        $query = Post::find();
        $sort = isset($params['sort']) ? $params['sort'] : ['defaultOrder' => $this->defaultOrder];
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => $sort,
        ]);
        if (!$this->validate()) {
            $query->where('0=1');// do not want to return any records when validation fails
            return $dataProvider;
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);
        $query->andFilterWhere(['like', 'text', $this->text]);
        return $dataProvider;
    }

}
