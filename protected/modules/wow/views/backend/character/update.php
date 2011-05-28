<script type="text/javascript"> 
/*<![CDATA[*/
updateForm = function(id){
        var settings = $.fn.yiiGridView.settings[id];
        var values = [];
        $('#'+id+' .'+settings.tableClass+' > tbody > tr.selected > td').each(function(i){
                 values.push($(this).text());
        });
        $('#Character_race').val(values[1]);
        $('#Character_gender').val(values[2]);
        $('#Character_playerBytes').val(values[3]);
        $('#Character_playerBytes2').val(values[4]);
    };
/*]]>*/
</script> 
<?php
$this->breadcrumbs=array(
    'Characters'=>array('index'),
    $model->name=>array('view','id'=>$model->guid),
    'Update',
);

$this->menu=array(
    array('label'=>'List Characters', 'url'=>array('index')),
    array('label'=>'View Characters', 'url'=>array('view', 'id'=>$model->guid)),
    array('label'=>'Manage Characters', 'url'=>array('admin')),
);
?>

<h1>Update Characters <?php echo $model->guid; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<?php
    $model->unsetAttributes();
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'characters-grid',
        'dataProvider'=>$mapper->search(),
        'filter'=>$model,
        'columns'=>array(
            'name',
            array('name' => 'race', 'value' => '$data->race', 'filter' => false),
            array('name' => 'gender', 'value' => '$data->gender', 'filter' => false),
            array('name' => 'playerBytes', 'value' => '$data->playerBytes', 'filter' => false),
            array('name' => 'playerBytes2', 'value' => '$data->playerBytes2', 'filter' => false),
        ),
        'selectionChanged' => 'updateForm', 
)); ?>