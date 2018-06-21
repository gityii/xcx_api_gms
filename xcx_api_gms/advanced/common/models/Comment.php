<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property string $content
 * @property int $status
 * @property int $create_time
 * @property int $userid
 * @property string $email
 * @property string $url
 * @property int $post_id
 * @property int $remind 0:未提醒1：已提醒
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'status', 'userid', 'email', 'post_id'], 'required'],
            [['content'], 'string'],
            [['status', 'create_time', 'userid', 'post_id', 'remind'], 'integer'],
            [['email', 'url'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '评论内容',
            'status' => '状态',
            'create_time' => '发布时间',
            'userid' => '用户',
            'email' => 'Email',
            'url' => 'Url',
            'post_id' => '文章',
            'remind' => '是否提醒',
        ];
    }

    public function getBeginning()
    {
        $tmpStr = strip_tags($this->content);
        $tmpLen = mb_strlen($tmpStr);

        return mb_substr($tmpStr,0,10,'utf-8').(($tmpLen>10)?'...':'');
    }

    public function getStatus0()
    {
        return $this->hasOne(Commentstatus::className(), ['id' => 'status']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userid']);
    }

    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    public function approve()
    {
        $this->status = 2; //设置评论状态为已审核
        return ($this->save()?true:false);
    }

    public static function getPengdingCommentCount()
    {
        return Comment::find()->where(['status'=>1])->count();
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            if($insert)
            {
                $this->create_time=time();
            }
            return true;
        }
        else  return false;
    }


    public static function findRecentComments($limit=10)
    {
        return Comment::find()->where(['status'=>2])->orderBy('create_time DESC')
            ->limit($limit)->all();
    }

}
