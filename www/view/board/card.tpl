<div class="bc-task" data-task-id="<?php echo $task['task_id']; ?>" onclick="getTaskForm(<?php echo $task['task_id']; ?>);">
    <input type="hidden" name="list_id[<?php echo $task['task_id']; ?>]" value="<?php echo $task['list_id']; ?>" />
    <input type="hidden" name="sort_order[<?php echo $task['task_id']; ?>]" value="<?php echo $task['sort_order']; ?>" />
    <?php echo $task['name']; ?>

    <br />
    <div class="bc-task__bottom">
        <small><?php echo $task['project_name']; ?></small>
        <?php if($task['date_end'] != '0000-00-00 00:00:00') { ?>
        <small><?php echo date('d-m-Y', strtotime($task['date_end'])); ?></small>
        <?php } ?>
    </div>
</div>