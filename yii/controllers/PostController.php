<?php

namespace app\controllers;
use app\models\Post;

/**
 * Description of PostController
 *
 * @author vitt
 */
class PostController extends AppController {
    
    public function actionView () {
        $id = \Yii::$app->request->get('id');
        $post = Post::findOne($id);
        if (empty($post)) throw new \yii\web\HttpException('404', 'Такой страницы не существует.');
        return $this->render('view', compact('post'));
        
    }
    
    public function actionIndex () {
        //$posts = Post::find()->select('id, title, excerpt')->all();
        $query = Post::find()->select('id, title, excerpt');
        $pages = new \yii\data\Pagination(['totalCount' => $query->count(), 'pageSize' => 3, 'pageSizeParam' => false, 'forcePageParam' => false]);
        $posts = $query->offset($pages->offset)->limit($pages->limit)->all();
        //$this->debug($arr);
        return $this->render('index', compact('posts', 'pages'));
    }
    
    public function actionTest () {
        return $this->render('test');
        
    }
    
}
