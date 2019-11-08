<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;
use app\models\Work;
use app\models\Typework;

/**
 * Description of Reletw
 *
 * @author vitt
 */
class Reletw extends ActiveRecord {
    
    public static function tableName() {
        
        return 'reletw';
    }
    
    public static function getWork($tid) {
        
        $result = array();
        $data = self::find()->where(['tid' => $tid])->all();
        foreach ($data as $item) :
            $array = array();
            $array[] = $item->wid;
            $work = Work::find(['wid' => $item->wid])->One();
            $array[] = $work->twid;
            $twid = $work->twid;
            $array[] = $work->info;
            $array[] = $work->cost;
            $tpwk = Typework::find()->where(['twid' => $twid])->One();
            $array[] = $tpwk->title;
            $result[] = $array;
        endforeach;
        return $result;
    }
}
