<?php foreach(Yii::$app->session->getAllFlashes() as $type => $messages): ?>

        <div class="text-center alert alert-<?= $type ?>" role="alert">
            <button class="close" data-dismiss="alert">×</button>
            <span><?= $messages ?></span>                
        </div>

<?php endforeach ?>
