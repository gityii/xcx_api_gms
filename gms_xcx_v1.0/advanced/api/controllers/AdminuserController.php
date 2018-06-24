<?php
namespace api\controllers;

use yii;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use api\models\ApiLoginForm;
use api\models\ApiSignupForm;
class AdminuserController extends ActiveController
{
    public $modelClass = 'common\models\Adminuser';
    
    public function actionLogin()
    {
        $model = new ApiLoginForm();
        
        $model->username = $_POST['username'];
        $model->password = $_POST['password'];
        
        if ($model->login()) {
            return ['access_token' => $model->login()];
        }
        else {
            $model->validate();
            return $model;
        }
        
    }


    public function actionSignup()
    {
        $model = new ApiSignupForm();

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->signup()) {
            return ['resulte' => '注册成功！'];
        }
        else {
            $model->validate();
            return $model;
        }
    }
}