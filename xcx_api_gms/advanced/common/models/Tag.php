<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tag".
 *
 * @property int $id
 * @property string $name
 * @property int $frequency
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['frequency'], 'integer'],
            [['name'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'frequency' => 'Frequency',
        ];
    }

    public static  function string2array($tags)
    {
        return preg_split('/\s*,\s*/',trim($tags),-1,PREG_SPLIT_NO_EMPTY);
    }

    public static  function array2string($tags)
    {
        return implode(', ',$tags);
    }

    public static function findTagWeights($limit=20)
    {
        $tag_size_level = 5;

        $models=Tag::find()->orderBy('frequency desc')->limit($limit)->all();
        $total=Tag::find()->limit($limit)->count();

        $stepper=ceil($total/$tag_size_level);

        $tags=array();
        $counter=1;

        if($total>0)
        {
            foreach ($models as $model)
            {
                $weight=ceil($counter/$stepper)+1;
                $tags[$model->name]=$weight;
                $counter++;
            }
            ksort($tags);
        }
        return $tags;
    }
}
