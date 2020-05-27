<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "whour".
 *
 * @property integer $hid
 * @property integer $hour
 * @property string $htext
 * @property integer $status
 * @property string $dayYr
 * @property integer $did
 */
class Whour extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'whour';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hour', 'htext', 'dayYr', 'did'], 'required'],
            [['hour', 'status', 'did'], 'integer'],
            [['hcount'], 'float'],
            [['htext'], 'string', 'max' => 32],
            [['dayYr'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hid' => 'ID',
            'hour' => 'СортЧас',
            'htext' => 'Отображение',
            'status' => 'Статус',
            'dayYr' => 'День года',
            'hcount' => 'Часов',
            'did' => 'Отдел',
        ];
    }
    
    private function getArrayHid($array) {
        $result = array();
        foreach ($array as $item) {
            $result[] = $item['hid'];
        }
        return $result;
    }
    
    public function getWhourlist ($hids) {
        
        return self::find()->
                      asArray()->
                      select('hid, htext, did, status')->
                      where(['!=', 'status', 0])->
                      andWhere(['did' => Yii::$app->user->identity->did])->
                      orWhere(['IN', 'hid', $hids])->
                      orderBy(['htext' => SORT_ASC])->
                      all();

    }
    
    // WhourWidget - получаем список интервалов для задачи
    public function getWhourOnTask($task) {
        
        $hourlist = array();
        // возвращает массив с полями [hid],[htext],[hide],[select]
        
        $reletm = Reletm::getHidOnTid($task->tid);
        if ($task->status == 1) {
            
            // выполнена, нужны только интервалы в задаче
            //$reletm = Reletm::getHidOnTid($task->tid);
            $whour = self::find()->select('hid, htext, did, status')->asArray()->where(['IN', 'hid', $reletm])->orderBy('hour')->all();
        }
        else {
            
            // не выполнена, нужны все интервала отдела или возможно проставленные вручную
            //$reletm = Reletm::getHidOnTid($task->tid);
            if (count($reletm) > 0)
                $whour = self::find()->select('hid, htext, did, status')->asArray()->where(['<>', 'status', 0])->andWhere(['did' => Yii::$app->user->identity->did])->orWhere(['IN', 'hid', $reletm])->orderBy('hour')->all();
            else $whour = self::find()->select('hid, htext, did, status')->asArray()->where(['<>', 'status', 0])->andWhere(['did' => Yii::$app->user->identity->did])->orderBy('hour')->all();
        }
        $i = 0;
        foreach ($whour as $itemH) {
            $hourlist[$i]['hid'] = $itemH['hid'];
            $hourlist[$i]['htext'] = $itemH['htext'];
            $hourlist[$i]['did'] = $itemH['did'];
            $hourlist[$i]['status'] = $itemH['status'];
            if ($itemH['status'] > 1)
                $hourlist[$i]['hide'] = true;
            else $hourlist[$i]['hide'] = false;
            $hourlist[$i]['select'] = false;
            foreach ($reletm as $itemR) {
                if ($itemH['hid'] == $itemR)
                    $hourlist[$i]['select'] = true;
            }
            $i++;
        }
        
        return $hourlist;
    }
}
