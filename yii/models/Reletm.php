<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;
use app\models\Whour;

/**
 * Description of Reletm
 *
 * @author vitt
 */
class Reletm extends ActiveRecord {
    
    public static function tableName() {
        
        return 'reletm';
    }
    
    public function getHtext () {
        
        return $this->hasOne(Whour::className(), ['hid' => 'hid'])->select('hid, htext');
    }
    
    public function getReletmForTIDs ($tids) {
        
        return self::find()->asArray()->with('htext')->where(['IN', 'tid', $tids])->all();
    }
   
    public function getHidOnTid ($tid) {
        
        $array = array();
        $reletm = self::find()->asArray()->where(['tid' => $tid])->all();        
        foreach ($reletm as $item) {
            $array[] = $item['hid'];
        }        
        return $array;
    }
}
