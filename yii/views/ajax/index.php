<!-- <pre>
    <?= ''//var_dump($result) ?>
</pre>
-->
<div id="app">
    <select v-model="user">
        <option>Tom</option>
        <option>Bob</option>
        <option>Sam</option>
    </select>
    <span>Выбрано: {{user}}</span>
</div>
<script src="https://unpkg.com/vue"></script>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            user:''
        }
    });
</script>