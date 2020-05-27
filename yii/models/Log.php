<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "log".
 *
 * @property integer $lid
 * @property string $dtcreate
 * @property string $ip
 * @property integer $uid
 * @property string $mclass
 * @property string $message
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dtcreate'], 'safe'],
            [['ip', 'uid', 'mclass', 'message'], 'required'],
            [['uid'], 'integer'],
            [['ip'], 'string', 'max' => 15],
            [['mclass'], 'string', 'max' => 8],
            [['message'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lid' => 'Lid',
            'dtcreate' => 'Dtcreate',
            'ip' => 'Ip',
            'uid' => 'Uid',
            'mclass' => 'Mclass',
            'message' => 'Message',
        ];
    }

    public function recLog ($mclass, $message) {
    
        if (($mclass == 'alarm') || ($mclass == 'open')) {
            $rec = new Log();
            $rec->ip = Yii::$app->request->userIP;
            $rec->uid = 0;
            $rec->mclass = $mclass;
            $rec->message = $message;
            $rec->save();
        }

        if (Yii::$app->user->id != 1) {
            $rec = new Log();
            $rec->ip = Yii::$app->request->userIP;
            $rec->uid = Yii::$app->user->getId();
            $rec->mclass = $mclass;
            $rec->message = $message;
            $rec->save();
        }
    }

    public function recLogNY($mclass = 'new_year') {
        $rec = new Log();
        $rec->ip = Yii::$app->request->userIP;
        $rec->uid = 0;
        $rec->mclass = $mclass;
        $rec->message = 'view';
        $rec->save();
    }

}
