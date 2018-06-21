<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use yii\web\Link;
use yii\web\Linkable;
use yii\helpers\Url;
/**
 * This is the model class for table "Post".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $tags
 * @property int $status
 * @property int $create_time
 * @property int $update_time
 * @property int $author_id
 */
class Post extends \yii\db\ActiveRecord implements Linkable
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content', 'status', 'author_id'], 'required'],
            [['content', 'tags'], 'string'],
            [['status', 'create_time', 'update_time', 'author_id'], 'integer'],
            [['title'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'tags' => '标签',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'author_id' => '作者',
        ];
    }


    public function getAuthor()
    {
        return $this->hasOne(Adminuser::className(), ['id' => 'author_id']);
    }

    public function getStatus0()
    {
        return $this->hasOne(Poststatus::className(), ['id' => 'status']);
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            if($insert)
            {
                $this->create_time = time();
                $this->update_time = time();
            }
            else
            {
                $this->update_time = time();
            }

            return true;

        }
        else
        {
            return false;
        }
    }

    public function getUrl()
    {
        return Yii::$app->urlManager->createUrl(
            //['post/detail','id'=>$this->id,'title'=>$this->title]);
            ['post/detail','id'=>$this->id]);
    }

    public function getBeginning($length=288)
    {
        $tmpStr = strip_tags($this->content);
        $tmpLen = mb_strlen($tmpStr);

        $tmpStr = mb_substr($tmpStr,0,$length,'utf-8');
        return $tmpStr.($tmpLen>$length?'...':'');
    }

    public function  getTagLinks()
    {
        $links=array();
        foreach(Tag::string2array($this->tags) as $tag)
        {
            $links[]=Html::a(Html::encode($tag),array('post/index','PostSearch[tags]'=>$tag));
        }
        return $links;
    }

    public function getCommentCount()
    {
        return Comment::find()->where(['post_id'=>$this->id,'status'=>2])->count();
    }

    public function getActiveComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id'])
            ->where('status=:status',[':status'=>2])->orderBy('id DESC');
    }

    //重写field方法
    public function fields()
    {
        return [
            'id',
            'title',
            '内容'=>'content',
            '状态'=> function($model){
                return  $model->status0->name;
            },

            '作者'=>function($model) {
                return $model->author->username;
            }
        ];
    }

    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['article/view','id'=>$this->id],true),
            'edit'=>Url::to(['article/update','id'=>$this->id],true),
            'index'=>Url::to(['articles'],true),
        ];
    }
}
