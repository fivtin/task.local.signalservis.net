<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "typework".
 *
 * @property integer $twid
 * @property string $title
 * @property string $detail
 * @property string $info
 * @property integer $status
 * @property string $cost
 */
class Typework extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'typework';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'detail', 'info', 'did', 'cost'], 'required'],
            [['detail'], 'string'],
            [['status', 'did'], 'integer'],
            [['cost'], 'number'],
            [['title', 'info'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'twid' => 'ID',
            'did' => 'Отдел',
            'title' => 'Название',
            'detail' => 'Элементы',
            'info' => 'Выполнение',
            'status' => 'Статус',
            'cost' => 'Стоимость',
        ];
    }
}
