<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;
use app\models\User;

/**
 * Description of Login
 *
 * @author vitt
 */

// модель для получения и проверки данных логин/пароль через модель User


class Login extends Model {
    
    public $username;
    public $password;
    
    public function rules () {
        
        return [            
            [['username', 'password'], 'required'],
            [['username', 'password'], 'trim'],
            [['username', 'password'], 'string', 'length' => [4, 64]],
            ['password', 'validatePassword'],
        ];
    }
    
    public function attributeLabels () {
        
        return [
            'username' => 'Имя пользователя', 'password' => 'Пароль',
        ];        
    }
    
    public function validatePassword ($attribute, $params) {
        
        if (!$this->hasErrors()) {
        
            $user = $this->getUser();  //получаем запись по имени пользователя
            
            if (!$user) $this->addError('username', "Ошибка! Проверьте правильность введенных данных.");
            if (!$user || (!$user->validatePassword($this->password, $user->salt)))  // валидация использует функцию в модели User
                $this->addError('password', "Ошибка! Проверьте правильность введенных данных.");
            
        }
    }
    
     public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), 3600*12);
        }
        return false;
    }
    
    public function getUser () {
        
        return User::findOne(['login'=> $this->username]);
    }
}
