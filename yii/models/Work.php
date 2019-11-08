<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;
use app\models\Typework;

/**
 * Description of Work
 *
 * @author vitt
 */
class Work extends ActiveRecord {
    
    public static function tableName() {
        
        return 'work';
    }
    
    public function getTypework () {        
        
        return $this->hasOne(Typework::className(), ['twid' => 'twid'])->select('twid, title, cost, status');
    }
    
    public function getWorkOnTid ($tid) {
        $array = array();
        $arr_work = array(); // ссылка на id типа работ
        $arr_cost = array(); // реальная стоимость
        $arr_nrep = array(); // реальное количество повторов
        $arr_info = array();
        $arr_status = array();
        $work = self::find()->asArray()->where(['tid' => $tid])->with('typework')->all();
        foreach ($work as $item) {
            $arr_work[] = $item['twid'];
            $arr_nrep[] = $item['nrepeat'];
            $arr_cost[] = $item['cost'];            
            $arr_info[] = $item['info'];
            $arr_status[] = $item['typework']['status'];
        }        
        $array['work'] = $arr_work;
        $array['nrep'] = $arr_nrep;
        $array['cost'] = $arr_cost;
        $array['info'] = $arr_info;
        $array['status'] = $arr_status;
        //echo var_dump($array); die;
        return $array;
        //return self::find()->asArray()->where(['tid' => $tid])->with('typework')->all();
    }
}
