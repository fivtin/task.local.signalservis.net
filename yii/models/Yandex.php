<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "yandex".
 *
 * @property integer $yid
 * @property string $street
 * @property string $home
 * @property string $xcor
 * @property string $ycor
 */
class Yandex extends ActiveRecord {
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        
        return 'yandex';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        
        return [
            [['street', 'home', 'xcor', 'ycor'], 'required'],
            [['street'], 'string', 'max' => 128],
            [['home', 'xcor', 'ycor'], 'string', 'max' => 16],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        
        return [
            'yid' => 'Yid',
            'street' => 'Street',
            'home' => 'Home',
            'xcor' => 'Xcor',
            'ycor' => 'Ycor',
        ];
    }
    
    public function LoadDB() {
        
        $result = 0;
        $file = file('../../yandex.txt');
//        $yandex = fopen('../../yandex.txt', 'r');
//        $result = $result.fgets($yandex);
//        fclose($yandex);
//        
        // очищаем таблицу
        Self::deleteAll();
        
        for ($i = 0; $i < (count($file) / 2); $i++) {
            
            $line1 = $file[$i * 2];
            $line2 = $file[$i * 2 + 1];
            
            // часть строки1 до запятой - улица, после запятой - дом 
            // часть строки2 до запятой - широта, после запятой - долгота
            $s1 = strpos($line1, ',');
            $s2 = strpos($line2, ',');
            
            $model = new Yandex;
            
            $model->street = trim(substr($line1, 0, $s1));
            $model->home = trim(substr($line1, $s1 + 1));
            $model->xcor = trim(substr($line2, 0, $s2));
            $model->ycor = trim(substr($line2, $s2 + 1));
            
            $model->save();
            
            $result++;
        }
        return $result;
    }
    
}
