<?php
use app\models\Typework;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$tab = Typework::find()->asArray()->where(['status' => 1])->all();
?>

<table class="table">
<?php $No = 1; ?>
<?php foreach ($tab as $item) : ?>
<tr>
    <td><?= $No ?></td>
    <td><input type="checkbox" id="cb_<?= $No ?>" name="twid[<?= $item['twid'] ?>]"</td>
    <td><label title="<?= $item['detail'] ?>" for="cb_<?= $No ?>"><?= $item['title'] ?></label></td>
    <td>
        <input type="number" title="<?= $item['info'] ?>" with="4" min="-1" max="12" maxlength="2" name="edit[<?= $item['twid'] ?>]" value="<?= $item['cost'] ?>">
        <input type="hidden" name="cost[<?= $item['twid'] ?>]" value="<?= $item['cost'] ?>">
    </td>
<?php $No++; ?>
</tr>
<?php endforeach; ?>
<tr>
    <td colspan="10">
        <button type="submit" name="action" value="cancel" class="btn btn-danger btn-xs glyphicon glyphicon-remove pull-left">
        </button>
        <button type="submit" name="action" value="save" class="btn btn-success btn-xs glyphicon glyphicon-ok pull-right">
        </button>
    </td>
</tr>
</table>