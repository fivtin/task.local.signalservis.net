<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;

/**
 * Description of MyForm
 *
 * @author vitt
 */
class MyForm extends Model {
    
    public $name;
    public $email;
    public $file;


    public function rules () {        
        return [
            [['name', 'email'], 'required', 'message' => 'Это обязательное поле.'],
            ['email', 'email', 'message' => 'Укажите правильный e-mail.'],
            [['file'], 'file', 'extensions' => 'jpg, png', 'message' => 'Вы можете загрузить только графические файлы (jpg и png).']
        ];
    }
}
