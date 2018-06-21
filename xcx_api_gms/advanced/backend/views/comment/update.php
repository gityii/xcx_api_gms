<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Comment */

$this->title = '修改评论: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '评论管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="comment-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
