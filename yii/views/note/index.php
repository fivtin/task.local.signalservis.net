<?php use yii\helpers\Html; ?>
<style>
    .btn-right {
        position: relative;
        float: right;
        top: -4px;
        margin-left: 8px;
    }
    
    .alert-default {
        color: #33333;
        background-color: #eeeeee;
        border-color: #888888;
    }
    
</style>
<div class="alert alert-success">
    <div>
        <strong>Новая запись</strong>
<!--        <button type="button" class="btn btn-danger btn-sm btn-right" onclick="deletenote(this);" >X</button>-->
        <button type="button" class="btn btn-success btn-sm btn-right glyphicon glyphicon-plus" onclick="editnote(this); "></button>
    </div>
    <?= Html::beginForm() ?>
    <?= Html::hiddenInput('nid', 0) ?>
    <?= Html::hiddenInput('uid', Yii::$app->user->id) ?>
    <?= Html::hiddenInput('shedule', '') ?>
    <div style="display: none; ">
        <div class="row">
            <div class="col-md-9">
                
                <div class="form-group">
                    <?= Html::label('Сообщение', null, ['class' => 'control-label']) ?>
                    <?= Html::input('text', 'title', '', ['class' => 'form-control', 'placeholder' => 'Новая запись', 'required' => 'required']) ?>
                </div>
                <div class="form-group">
                    <?= Html::label('Комментарий', null, ['class' => 'control-label']) ?>
                    <?= Html::textarea('info', '', ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?= Html::label('Расписание', null, ['class' => 'control-label']) ?>
                    <?= Html::dropDownList('prompt', '0', ['0' => 'не напоминать', '1' => '_каждый раз при входе', '2' => 'постоянно', '3' => 'однократно',
                    '4' => '_ежедневно после даты', '5' => '_ежедневно до даты', '6' => '_по расписанию', '99' => 'ПРОЧИТАНО'], ['class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= Html::label('Напоминание', null, ['class' => 'control-label']) ?>
                    <?= Html::input('date', 'notedate', date("Y-m-d"), ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
                <?= Html::button('Отмена', ['class' => 'btn btn-danger pull-right', 'onclick' => 'cancel(this);']) ?>
            </div>
        </div>
    </div>
    <?= Html::endForm() ?>
</div>
<?php
    if (count($note) > 0)
        foreach($note as $item) {
?>
<div class="alert <?= $item->status == 0 ? 'alert-default' : 'alert-info' ?>">
    <div>
        <strong><?= $item->title.($item->status == 0 ? '' : ' [прочитано]') ?></strong>
<!--        <button type="button" class="btn btn-danger btn-sm btn-right" onclick="deletenote(this);" >X</button>-->
        <button type="button" class="btn btn-primary btn-sm btn-right" onclick="editnote(this); ">&#9660;</button>
    </div>
    <?= Html::beginForm() ?>
    <?= Html::hiddenInput('nid', $item->nid) ?>
    <?= Html::hiddenInput('shedule', $item->shedule) ?>
    <div style="display: none;">        
        <div class="row">
            <div class="col-md-9">
                
                <div class="form-group">
                    <?= Html::label('Сообщение', null, ['class' => 'control-label']) ?>
                    <?= Html::input('text', 'title', $item->title, ['class' => 'form-control', 'required' => 'required']) ?>
                </div>
                <div class="form-group">
                    <?= Html::label('Комментарий', null, ['class' => 'control-label']) ?>
                    <?= Html::textarea('info', $item->info, ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?= Html::label('Расписание', null, ['class' => 'control-label']) ?>
                    <?= Html::dropDownList('prompt', $item->prompt, ['0' => 'не напоминать', '1' => '_каждый раз при входе', '2' => 'постоянно', '3' => 'однократно',
                    '4' => '_ежедневно после даты', '5' => '_ежедневно до даты', '6' => '_по расписанию', '99' => 'ПРОЧИТАНО'], ['class' => 'form-control']) ?>
                </div>
                <div class="form-group">
                    <?= Html::label('Напоминание', null, ['class' => 'control-label']) ?>
                    <?= Html::input('date', 'notedate', ($item->notedate == '') ? date("Y-m-d") : $item->notedate, ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                <?= Html::button('Удалить', ['class' => 'btn btn-danger', 'onclick' => 'remove('.$item->nid.');']) ?>
                <?= Html::button('Отмена', ['class' => 'btn btn-warning pull-right', 'onclick' => 'cancel(this);']) ?>
            </div>
        </div>
    </div>
    <?= Html::endForm() ?>
</div>
<?php        
        }
?>
<script>
    function editnote(elem) {
        var parent = elem.parentNode.parentNode;
        var divhide = parent.getElementsByTagName("DIV")[0];
        var divshow = parent.getElementsByTagName("DIV")[1];
        divhide.style.display = 'none';
        divshow.style.display = 'block';
        //elem.style.display = 'none';
    }
    function cancel(elem) {
        var parent = elem.parentNode.parentNode.parentNode.parentNode.parentNode;
        var divhide = parent.getElementsByTagName("DIV")[1];
        var divshow = parent.getElementsByTagName("DIV")[0];
        divhide.style.display = 'none';
        divshow.style.display = 'block';
        //elem.style.display = 'none';
    }
    
    function remove (id) {
        if (confirm('Вы уверены, что хотите удалить это напоминание?'))
            document.location.href = '/note/remove/' + id;
    }
</script>