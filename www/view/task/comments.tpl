<?php if($comments) { ?>
    <?php foreach($comments as $comment) { ?>
    <div class="task-comment__item js-comment" data-comment-id="<?php echo $comment['comment_id']; ?>" >
        <div class="task-comment__top">
            <p><?php echo $comment['user_name']; ?></p>
            <small>добавлен: <?php echo $comment['date_added']; ?></small>
            <?php if($comment['date_modified'] != $comment['date_added']) { ?>
            <small>изменен: <?php echo $comment['date_modified']; ?></small>
            <?php } ?>
        </div>
        <div class="js-editor">
            <p class="js-comment-text"><?php echo $comment['text']; ?></p>

            <?php if($user_id == $comment['user_id']) { ?>
            <a href="javascript:" onclick="editComment($(this).closest('.js-comment'), <?php echo $task_id; ?>)"><?php echo $text_edit; ?></a>
            <a href="javascript:" onclick="deleteComment(<?php echo $comment['comment_id']; ?>, <?php echo $task_id; ?>)"><?php echo $text_delete; ?></a>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
<?php } ?>