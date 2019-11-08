<style>
td pre {
    /* font-size: 16px; */
    margin-right: 24px;
    background-color: #ffffff;
    /* border: 1px solid #cccccc; */
    /* border-radius: 4px; */
    padding: 4px 16px 8px;
}
</style>

<?php
if ($twork) {
    $i = 1;
?>
<h3 class="text-center">Список видов работ</h3>
<table class="table table-striped table-condensed">
    <tr>
        <th style="width: 10%;">№ п/п</th>
        <th style="width: 70%;">Название / состав</th>
        <th style="width: 20%;">Норма выполнения</th>
    </tr>
<?php
    foreach ($twork as $item) {
?>
    <tr>
        <td style="padding-top: 14px; "><?= $i ?></td>
        <td><h4><?= $item['title'] ?></h4><pre><?= $item['detail'] ?></pre></td>
        <td style="padding-top: 14px; "><?= $item['info'] ?></td>
    </tr>
<?php
        
        $i++;
    }
?>
</table>
<?php
}
?>