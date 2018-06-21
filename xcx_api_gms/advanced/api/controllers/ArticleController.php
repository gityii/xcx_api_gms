<?php
namespace api\controllers;

use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use common\models\Post;

class ArticleController extends ActiveController
{
    //http://xcx.api.com/index.php/article
    public $modelClass = 'common\models\Post';

    public  function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        return new ActiveDataProvider(
            [
                'query'=>$modelClass::find()->asArray(),
                'pagination'=>['pageSize'=>5],
            ]
        );
    }

    public function actionSearch() {
        return Post::find()->where(['like','title',$_POST['keyword']])->all();
    }
}