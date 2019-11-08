<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use Yii;
use yii\db\ActiveRecord;
use app\models\Work;
use app\models\Relemp;
use app\models\Reletm;
use app\models\Whour;
use app\models\Typework;
use app\models\User;

/**
 * Description of Task
 *
 * @author vitt
 */
class Task extends ActiveRecord {
    
    public static function tableName() {
        return 'task';
    }
    
    // EmployeWidget
    public function getEmplist () {
        
        return $this->hasMany(Employe::className(), ['did' => 'did'])->orderBy('fio_short');
    }
    
    // WhourWidget
    public function getWhourlist () {
        
        return $this->hasMany(Whour::className(), ['did' => 'did'])->orderBy('htext');
    }
    
    // CalendarWidget
    public function getWorks () {
        
        return $this->hasMany(Work::className(), ['tid' => 'tid'])->select('tid, wid'); //->with('typework');
    }
    
    // Report::getPersonalRep
    public function getWorkrep () {
        
        return $this->hasMany(Work::className(), ['tid' => 'tid']); //->with('typework');
    }
    
    // Test
    public function getWorklist () {
        
        return $this->hasMany(Work::className(), ['tid' => 'tid'])->with('typework');
        //return $this->hasMany(Work::className(), ['tid' => 'tid'])->with('typework', 'relemp', 'reletm');
    }
    
    // CalendarWidget, Report
    public function getRelemp () {
        
        return $this->hasMany(Relemp::className(), ['tid' => 'tid']);
    }
    
    // CalendarWidget
    public function getReletm () {
        
        return $this->hasMany(Reletm::className(), ['tid' => 'tid']);
    }
    
    // TlistWidget, WorklistWidget
    public function getUser () {
        
        return $this->hasOne(User::className(), ['uid' => 'uid']);
    }
    
    // TlistWidget, WorklistWidget, Report
    public function getWhour () {
        
        return $this->hasMany(Whour::className(), ['hid' => 'hid'])->viaTable('reletm', ['tid' => 'tid'])->orderBy('htext')->select('hid, htext, hcount');
    }
    
    public function getEid () {
        
        return $this->hasMany(Relemp::className(), ['tid' => 'tid']);
    }
    
    public function getHid () {
        
        return $this->hasMany(Whour::className(), ['hid' => 'hid'])->viaTable('reletm', ['tid' => 'tid'])->orderBy('htext')->select('hid, htext');
    }
    
    // TlistWidget, WorklistWidget
    public function getEmploye () {
        
        return $this->hasMany(Employe::className(), ['eid' => 'eid'])->viaTable('relemp', ['tid' => 'tid'])->orderBy('fio_short')->select('eid, fio_short');
    }
    
    // WorklistWidget
    public function getTypework () {
        
        return $this->hasMany(Typework::className(), ['twid' => 'twid'])->viaTable('work', ['tid' => 'tid']);
    }

    // CalendarWidget
    public function getTaskForCalendar ($start, $finish) {
        
        if (Yii::$app->user->identity->role[0] == 'w')
        
            return self::find()->asArray()->
                         select('tid, dttask, status')->
                         with(['relemp', 'reletm', 'works'])->
                         where(['>=', 'dttask', $start])->                // фильтр по начальной дате
                         andWhere(['<=', 'dttask', $finish])->               // фильтр по дате окончания
                         orderBy(['dttask' => SORT_ASC])->all();             // сортировка по дате задачи
        else
            
            return self::find()->asArray()->
                         select('tid, dttask, status')->
                         with(['relemp', 'reletm', 'works'])->
                         where(['did' => Yii::$app->user->identity->did])->  // фильтр по отделу
                         andWhere(['>=', 'dttask', $start])->                // фильтр по начальной дате
                         andWhere(['<=', 'dttask', $finish])->               // фильтр по дате окончания
                         orderBy(['dttask' => SORT_ASC])->all();             // сортировка по дате задачи
        
    }
    
//    public function getTaskActive () {
//        
//        return self::find()->asArray()->
//                     where(['did' => Yii::$app->user->identity->did])->
//                     andWhere(['status' => 0])->
//                     orderBy(['dttask' => SORT_ASC])->all();
//    }
//    
//    public function getTaskOnDate ($date) {
//        
//        return self::find()->asArray()->
//                     where(['did' => Yii::$app->user->identity->did])->
//                     andWhere(['dttask' => $date])->
//                     orderBy(['status' => SORT_DESC])->all();
//    }
 
    // TlistWidget
    public function getActiveWithRel() {
        
        if (Yii::$app->user->identity->role[0] == 'w')
        
            return self::find()->asArray()->with(['whour', 'employe', 'works', 'user'])->
                         where(['>', 'tid', 0])->
                         andWhere(['status' => 0])->
                         orderBy(['did' => SORT_ASC, 'status' => SORT_ASC, 'dttask' => SORT_ASC])->all();
        else 
            
            return self::find()->asArray()->with(['whour', 'employe', 'works', 'user'])->
                         where(['>', 'tid', 0])->
                         andWhere(['did' => Yii::$app->user->identity->did])->
                         andWhere(['status' => 0])->
                         orderBy(['did' => SORT_ASC, 'status' => SORT_ASC, 'dttask' => SORT_ASC])->all();
    }
    
    // TlistWidget
    public function getOnDateWithRel ($date) {
        
        if (Yii::$app->user->identity->role[0] == 'w')
            
            return self::find()->asArray()->with(['whour', 'employe', 'works', 'user'])->
                         where(['dttask' => $date])->
                         orderBy(['did' => SORT_ASC, 'status' => SORT_ASC])->all();
            
        else
        
            return self::find()->asArray()->with(['whour', 'employe', 'works', 'user'])->
                         where(['did' => Yii::$app->user->identity->did])->
                         andWhere(['dttask' => $date])->
                         orderBy(['did' => SORT_ASC, 'status' => SORT_ASC])->all();
    }
    
// TlistWidget
    public function getSearchWithRel ($search) {
        
        if (Yii::$app->user->identity->role[0] == 'w')
            
            return self::find()->asArray()->with(['whour', 'employe', 'works', 'user'])->
                         where(['LIKE', 'title', $search])->
                         orWhere (['LIKE', 'descr', $search])->
                         andWhere (['IN', 'status', [0, 1]])->
                         orderBy(['status' => SORT_ASC, 'dttask' => SORT_ASC])->all();
            
        else
        
            return self::find()->asArray()->with(['whour', 'employe', 'works', 'user'])->
                         where(['AND', ['did' => Yii::$app->user->identity->did], ['OR', ['LIKE', 'title', $search], ['LIKE', 'descr', $search]]])->
                         //where(['did' => Yii::$app->user->identity->did])->
                         //andWhere (['LIKE', 'title', $search], ['LIKE', 'descr', $search])->
                         andWhere (['IN', 'status', [0, 1]])->
                         orderBy(['status' => SORT_ASC, 'dttask' => SORT_ASC])->all();
    }
    
    
    
//    // Tunit
//    public function getOnTidWithRel ($tid) {
//        
//        return self::find()->asArray()->with(['whourlist', 'emplist', 'whour', 'employe', 'works'])->
////                     where(['>', 'tid', 0])->
//                     where(['did' => Yii::$app->user->identity->did])->
//                     andWhere(['tid' => $tid])->limit(1)->
////        andWhere(['status' => '0'])->
//                     one();
//    }
    
    // Tunit
    public function getOnTid ($tid) {
        
        if (Yii::$app->user->identity->role[0] == 'w')
        
            return self::find()->asArray()->
                         where(['tid' => $tid])->
                         andWhere(['<>', 'status', '5'])->
                         limit(1)->
                         one();
        else
            
            return self::find()->asArray()->
                         where(['did' => Yii::$app->user->identity->did])->
                         andWhere(['tid' => $tid])->
                         andWhere(['<>', 'status', '5'])->
                         limit(1)->
                         one();
    }
    
    // Tunit
    public function getPatternOnTid ($tid) {
        
        if (Yii::$app->user->identity->role[0] == 'w')
        
            return self::find()->asArray()->
                         where(['tid' => $tid])->
                         andWhere(['=', 'status', '5'])->
                         limit(1)->
                         one();
        else
            
            return self::find()->asArray()->
                         where(['did' => Yii::$app->user->identity->did])->
                         andWhere(['tid' => $tid])->
                         andWhere(['=', 'status', '5'])->
                         limit(1)->
                         one();
    }    
    
    // Report
    public function getTaskOnEid($start, $finish, $eid) {
        
        return self::find()->
                asArray()->
                with('works', 'employe')->
                //leftJoin('relemp', '{{task}}.tid={{relemp}}.tid')->
                //leftJoin('employe', '{{relemp}}.eid={{employe}}.eid')->
                where(['>=', 'dttask', $start])->
                andWhere(['<=', 'dttask', $finish])->
                //andWhere(['=', '{{employe}}.eid', $eid])->
                //andWhere('=', '`employe`.`eid`', $eid)->
                
                orderBy('dttask')->
                all();
    }

}
