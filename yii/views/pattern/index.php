<!-- ВЫВОД ШАБЛОНОВ -->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-success">
                <div class="panel-heading">
                    Шаблоны задач
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <?= Yii::$app->user->identity->role[0] == 'w' ? '<th>Отдел</th>' : '' ?>

                        </tr>
<?php
foreach ($pattern as $item) {
// при полном доступе дополнительно выводится id шаблона и отдел
// при ограниченном - только ссылка на шаблон
?>
                        <tr>
                            <td><?=  Yii::$app->user->identity->role[0] == 'w' ? $item['tid'] : $item['dttask'] ?></td>
                            <td><a href="/task/new/<?= $item['tid'] ?>" target="_blank"><?= $item['title'] ?></a></td>
                            <?= Yii::$app->user->identity->role[0] == 'w' ? '<td>'.$item['did'].'</td>' : ''?>
                            
                        </tr>
<?php
}
?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>