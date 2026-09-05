<?php if($schedule_exceptions) { ?>
<?php foreach($schedule_exceptions as $schedule_exception) { ?>
<div class="task-schedule__exception-item">
    <div><?php echo $schedule_exception['date']; ?></div>
    <button class="btn btn-danger" type="button" onclick="deleteItem('task_schedule_exception', <?php echo $task_id; ?>, 'date', '<?php echo $schedule_exception['date']; ?>');">
        <i class="fas fa-times"></i>
    </button>
</div>
<?php } ?>
<?php } ?> 