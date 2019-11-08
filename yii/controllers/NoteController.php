<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use Yii;
use app\controllers\ExtController;
use app\models\Note;
use app\models\Log;
use app\models\Year;

/**
 * Description of NoteController
 *
 * @author vitt
 */
class NoteController extends ExtController {
    
    public function actionIndex () {
        
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('nid') == 0) {
                $note = new Note();
                $note->attributes = Yii::$app->request->post();
                if ($note->validate()) $note->save ();
            }
            else {
                $note = Note::findOne(Yii::$app->request->post('nid'));
                $note->attributes = Yii::$app->request->post();
                if ($note->validate()) {
                    
                    if ($note->status == 1) {
                        if ($note->prompt != 99) $note->status = 0;
                    }
                    else {
                        if ($note->prompt == 99) $note->status = 1;
                    }
                    $note->save();
                }
                
            }
        }
        $note = Note::find()->where(['uid' => Yii::$app->user->id])->orderBy('status, notedate')->all();
        Log::recLog('note', 'view');
        return $this->render('index', ['note' => $note]);
    }
    
    public function actionRemove ($nid) {
        
        $note = Note::findOne($nid);
        $note->delete();
        Log::recLog('note', 'remove='.$nid);
        Log::recLog('note', $note->title);
        return Yii::$app->response->redirect(['note/index']);
    }
    
    public function actionRead ($nid) {
        
        $note = Note::findOne($nid);
        if ($note->prompt == 2) {  // постоянное напоминание - устанавливаем дату на след. рабочий день
            
            //$note->notedate = date("Y-m-d", strtotime($note->notedate) + 86400);
            $note->notedate = Year::getNextWDay();
        }
        if ($note->prompt == 3) {  // однократно - устанавливаем статус в 1
            
            $note->status = 1;
        }
        
        //$note->delete();
        $note->save();
        Log::recLog('note', 'read='.$nid);
        return Yii::$app->response->redirect(['task/index']);
        
    }
    
}
