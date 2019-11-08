<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payout".
 *
 * @property integer $id
 * @property integer $salary_id
 * @property string $info
 * @property string $payment
 * @property string $base
 * @property string $depends
 * @property string $type
 * @property integer $completed
 */
class Payout extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['salary_id', 'info'], 'required'],
            [['salary_id', 'completed'], 'integer'],
            [['payment'], 'number'],
            [['info'], 'string', 'max' => 256],
            [['base', 'depends', 'type'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'salary_id' => 'Salary ID',
            'info' => 'Info',
            'payment' => 'Payment',
            'base' => 'Base',
            'depends' => 'Depends',
            'type' => 'Type',
            'completed' => 'Completed',
        ];
    }
}
