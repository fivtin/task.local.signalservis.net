<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Description of User
 *
 * @author vitt
 */
class User extends ActiveRecord implements IdentityInterface {
    
    public static function tableName() {
        
        return 'user';
    }
    
    public function validatePassword ($password, $salt) {
        
        return $this->password === md5($password.$salt);
    }
    
    public static function findIdentity($id) {
        
        return self::findOne($id);
    }    
    
    public function getId() {
        
        return $this->uid;
    }
    
    public static function findIdentityByAccessToken($token, $type = null) {
        
    }
    
    public function getAuthKey() {
        
    }
    
    public function validateAuthKey($authKey) {
        
    }
    
}
