<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "timesheet".
 *
 * @property integer $tsid
 * @property integer $eid
 * @property string $tsdate
 * @property string $shift
 */
class Timesheet extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timesheet';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eid', 'tsdate', 'shift'], 'required'],
            [['eid'], 'integer'],
            [['tsdate'], 'string', 'max' => 10],
            [['shift'], 'string', 'max' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tsid' => 'Tsid',
            'eid' => 'Eid',
            'tsdate' => 'Tsdate',
            'shift' => 'Shift',
        ];
    }
    
    public function getTableOnDate($date) {
        
        return self::find()->asArray()->where(['tsdate' => $date])->all();
    }
}
