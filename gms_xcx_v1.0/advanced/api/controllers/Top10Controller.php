<?php
namespace api\controllers;

use yii\rest\Controller;
use common\models\Post;
use yii\db\Query;

class Top10Controller extends Controller
{
    public function actionIndex()
    {
        $top10 = (new Query())
            ->from('post')
            ->select(['author_id','Count(id) as creatercount'])
            ->groupBy(['author_id'])
            ->orderBy('creatercount DESC')
            ->limit(5)
            ->all();
        
        return $top10;
    }
 
}