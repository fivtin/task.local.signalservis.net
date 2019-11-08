<?php

namespace app\models;

use Yii;
use app\models\Payout;
use app\models\Employe;

/**
 * This is the model class for table "salary".
 *
 * @property integer $sid
 * @property integer $eid
 * @property integer $sldate
 * @property string $payment
 * @property string $award
 * @property integer $block
 */
class Paysalary extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paysalary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eid', 'sldate'], 'required'],
            [['eid', 'sldate', 'block'], 'integer'],
            [['payment', 'award'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'eid' => 'Eid',
            'sldate' => 'Sldate',
            'payment' => 'Payment',
            'award' => 'Award',
            'block' => 'Block',
        ];
    }
    
    public function getPayout () {
        
        //return $this->hasMany(Payout::className(), ['salary_id' => 'id'])->orderBy('info');
        return $this->hasMany(Payout::className(), ['salary_id' => 'id']);
    }
    
    public function getEmploye () {
        
        return $this->hasOne(Employe::className(), ['eid' => 'eid'])->select('eid, fio_short');
    }
}
