<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;
use app\models\Employe;

/**
 * Description of Relewk
 *
 * @author vitt
 */
class Relemp extends ActiveRecord {
    
    public static function tableName() {
        
        return 'relemp';
    }
    
    public function getFio () {
        
        return $this->hasOne(Employe::className(), ['eid' => 'eid'])->select('eid, fio_short');
    }
    
    public function getRelempForTIDs ($tids) { // вызов функции нигде не встречается
        
        return self::find()->asArray()->with('fio')->where(['IN', 'tid', $tids])->all();
    }
    
    
    // Employe - возвращает массив сотрудников для задачи
    public function getEidOnTid ($tid) {
        
        $array = array();
        $relemp = self::find()->asArray()->where(['tid' => $tid])->orderBy('eid')->all();
        foreach ($relemp as $item) {
            $array[] = $item['eid'];
        }        
        return $array;
    }
    
    public function getTask() {
        
        return $this->hasMany(Task::className(), ['tid' => 'tid'])->with('worklist');
    }
}
