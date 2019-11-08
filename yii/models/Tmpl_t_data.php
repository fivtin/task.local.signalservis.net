<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;

/**
 * Description of Tmpl_t_data
 *
 * @author vitt
 */
class Tmpl_t_data extends ActiveRecord {
    
    public static function tableName() {
        
        return 'tmpl_t_data';
    }
}
