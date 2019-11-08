<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "material".
 *
 * @property integer $mid
 * @property integer $eqid
 * @property string $name
 * @property string $title
 * @property string $unit
 * @property integer $rating
 * @property integer $status
 */
class Material extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eqid', 'name', 'title', 'status'], 'required'],
            [['eqid', 'rating', 'status'], 'integer'],
            [['name'], 'string', 'max' => 128],
            [['title'], 'string', 'max' => 32],
            [['unit'], 'string', 'max' => 4],
            [['name', 'title'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mid' => 'ID',
            'eqid' => 'Тип',
            'name' => 'Название',
            'title' => 'Обозначение',
            'unit' => 'Ед.изм.',
            'rating' => 'Рейтинг',
            'status' => 'Статус',
        ];
    }
}
