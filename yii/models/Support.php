<?php

namespace app\models;

use Yii;
use app\models\Yandex;

/**
 * This is the model class for table "support".
 *
 * @property integer $sis
 * @property integer $did
 * @property integer $yid
 * @property string $sservice
 * @property string $stime
 * @property string $scoment
 * @property integer $status
 */
class Support extends \yii\db\ActiveRecord {
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'support';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        
        return [
            [['did', 'yid', 'sservice', 'sdate', 'stime', ], 'required'],
            [['did', 'yid', 'status'], 'integer'],
            [['sservice', 'sdate', 'stime'], 'string', 'max' => 16],
            [['scomment', 'sreport'], 'string', 'max' => 512],
            [['scomment', 'sreport', 'stype'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        
        return [
            'sid' => 'Sid',
            'did' => 'Did',
            'yid' => 'Yid',
            'sservice' => 'Sservice',
            'sdate' => 'Sdate',
            'stime' => 'Stime',
            'scomment' => 'Scomment',
            'stype' => 'Stype',
            'sreport' => 'Sreport',
            'status' => 'Status',
        ];
    }
    
    public function getXy () {
        
        return $this->hasOne(Yandex::className(), ['yid' => 'yid']);
    }
    
    public function remove ($sid) {

        $remove = self::findOne($sid);
        $remove->delete();
    }

    public function execute($sid, $prompt) {

        //$execute = self::find()->where(['sid' => $sid, 'did' => Yii::$app->user->identity->did])->one();
        $execute = self::find()->where(['sid' => $sid])->one();
        $execute->did = 5;
        
        $execute->status = 1;
        $execute->sreport = htmlspecialchars($prompt);
        $execute->save();
    }
    
    
    
}
