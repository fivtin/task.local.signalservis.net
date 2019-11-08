<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use yii\base\Model;

/**
 * Description of UploadForm
 *
 * @author vitt
 */
class UploadForm extends Model {
    
    public $file;
 
    public function rules() {
        
        return [
        // username and password are both required

        [['file'], 'file', 'extensions' => 'png, jpg, pdf, gif, rar, zip, swf, avi', 
                           'skipOnEmpty' => false]];
    }
     
}
