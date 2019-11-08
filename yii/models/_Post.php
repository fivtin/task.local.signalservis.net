<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;

/**
 * Description of Post
 *
 * @author vitt
 */
class Post extends ActiveRecord {
    
    public static function tableName() {
        
        return 'post';
    }    
}
