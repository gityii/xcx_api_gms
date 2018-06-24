<?php
namespace api\controllers;

use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use common\models\Post;
use yii\helpers\ArrayHelper;
use yii\filters\auth\QueryParamAuth;
use common\models\Adminuser;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\RateLimiter;
class ArticleController extends ActiveController
{
    //http://xcx.api.com/index.php/article
    public $modelClass = 'common\models\Post';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
        ];
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'enableRateLimitHeaders' => true,
        ];
        return $behaviors;
    }

//http基本认证
//         public function behaviors()
//     {
//          return ArrayHelper::merge(parent::behaviors(), [
//             'authenticatior' => [
//                 'class' => HttpBasicAuth::className(),
//                 'auth' => function ($username, $password) {
//                         $user = Adminuser::find()->where(['username' => $username])->one();
//                         if ($user->validatePassword($password)) {
//                             return $user;
//                         }
//                         return null;
//                         }
//                      ]
//             ]);
//     }

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