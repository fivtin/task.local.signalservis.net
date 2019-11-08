<?php
    use yii\bootstrap\ActiveForm;
    use yii\helpers\Html;
?>
<style>
    label { font-weight: normal; }
</style>
<div class="container">
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h1 class="panel-title">test</h1>
            </div>
            <div class="panel-body">
                <div class="col-md-10">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="taskdate" class="col-sm-2 control-label">Дата задачи</label>
                            <div class="col-sm-3">
                            <input type="date" class="form-control" id="taskdate" placeholder="Дата">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="tasktitle" class="col-sm-2 control-label">Описание задачи</label>
                            <div class="col-sm-10">
                            <input type="text" class="form-control" id="tasktitle" placeholder="Введите описание задачи">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="taskdescription" class="col-sm-2 control-label">Описание работ</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="taskdescription" placeholder="Краткий перечень и последовательность работ"></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="worklist" class="col-sm-2 control-label">Перечень работ</label>
                            <div class="col-sm-10">
                                <span class="form-control" id="worklist">worklist</span>
                            </div>
                        </div>
                        <div class="form-group hidden">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox"> Запомнить меня
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-success">Подтвердить</button>
                            </div>
                        </div>
                    </form>
                    <?php
                        $form = ActiveForm::begin();
                        ?><input type="text" name="w1" /><input type="submit" value="Send" /><?php
                        ActiveForm::end();
                    ?>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success">Delete</button>
                    <button class="btn btn-success">Add</button>
                    <button class="btn btn-success">Ignore</button>
                    <button class="btn btn-success">Cancel</button>
                </div>
                <div style="clear: both;">
                    &nbsp;
                </div>
                <table>
                    <tr>
                            <td class="col-md-2">
                                1
                            </td>
                            <td class="col-md-8">
                                2
                            </td>
                            <td class="col-md-2">
                                3
                            </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
//$js = <<<JS
//        var www = "test";
// $('w0').on('beforeSubmit', function() {
// alert('Работает!');
// return false;
// });
//JS;
//
//$this->registerJs($js);
?>
<script>
$(document).ready (function () {
var form = document.getElementById("w0");
form.addEventListener("submit", function () { alert("submit"); return false; });
});
</script>