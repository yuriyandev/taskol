<?php echo $header; ?>
<div class="board-container">
    <div class="main">
        <div class="sidebar">
            <div class="board-menu">
                <div class="board-menu-sortable shadow-element">
                    <?php if($boards) { ?>
                    <?php foreach($boards as $board) { ?>
                    <div class="board-menu_item" data-board-id="<?php echo $board['board_id']; ?>">
                        <a class="board-menu_link <?php echo $board_id == $board['board_id'] ? ' active' : ''; ?>" href="index.php?action=board/view&board_id=<?php echo $board['board_id']; ?>">
                            <span>
                                <svg fill="#fff" width="800px" height="800px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21,7H3V4A1,1,0,0,1,4,3H20a1,1,0,0,1,1,1ZM3,20V9H21V20a1,1,0,0,1-1,1H4A1,1,0,0,1,3,20Zm3-6H18V12H6Zm0,4h6V16H6Z"/></svg>
                                <?php echo $board['name']; ?>
                            </span>
                            <?php if($board_id == $board['board_id']) { ?>
                            <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 9L9.99998 16L6.99994 13" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php } else { ?>
                            <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 12L4 12M20 12L14 18M20 12L14 6" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?php } ?>
                        </a>
                    </div>
                    <?php } ?>
                    <?php } ?>
                </div>
                <div class="list-rows shadow-element">
                    <button class="btn btn-fill" type="button" onclick="setRowsCount(1, <?php echo $board_id; ?>)">В одну строку</button>
                    <button class="btn btn-fill" onclick="setRowsCount(2, <?php echo $board_id; ?>)">В две строки</button>
                </div>
            </div>
        </div>

        <div class="board">
            <div class="board-wrapper sortable-board <?php if($rows_count == 2) { ?> two-rows <?php } ?>" id="resizable-top">
                <?php if($lists) { ?>
                <?php foreach($lists as $list) { ?>
                <?php if($rows_count == 2 && $list['row'] == 2) continue; ?>
                <div class="board-column" id="column-<?php echo $list['list_id']; ?>" data-list-id="<?php echo $list['list_id']; ?>">
                    <div class="board-column__inner">
                        <div class="board-column__top">
                            <h4><?php echo $list['name']; ?></h4>
                            <a class="list-edit" href="javascript:" onclick="getListForm(<?php echo $board_id; ?>, <?php echo $list['list_id']; ?>)">
                                <i class="fas fa-pen"></i>
                            </a>
                        </div>

                        <div class="board-column-tasks sortable-list">
                            <?php if(isset($tasks[$list['list_id']]) && $tasks[$list['list_id']]) { ?>
                            <?php $sort_inc = 1; ?>
                            <?php foreach($tasks[$list['list_id']] as $task) { ?>
                            <div class="bc-task" data-task-id="<?php echo $task['task_id']; ?>" onclick="getTaskForm(<?php echo $task['task_id']; ?>);">
                                <input type="hidden" name="list_id[<?php echo $task['task_id']; ?>]" value="<?php echo $task['list_id']; ?>" />
                                <input type="hidden" name="sort_order[<?php echo $task['task_id']; ?>]" value="<?php echo $task['sort_order']; ?>" />
                                <?php if($task['cover_value']) { ?>
                                <div class="task-cover">
                                    <div class="task-cover__area" style="background-color: <?php echo $task['cover_value']; ?>;"></div>
                                </div>
                                <?php } ?>
                                <?php echo $task['name']; ?>

                                <br />
                                <div class="bc-task__bottom">
                                    <?php if($task['date_end'] != '0000-00-00 00:00:00') { ?>
                                    <small><?php echo date('d-m-Y', strtotime($task['date_end'])); ?></small>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php $sort_inc++; ?>
                            <?php } ?>
                            <?php } ?>
                        </div>

                        <div class="board-column__bottom">
                            <a class="task-add" href="javascript:" onclick="addTask(<?php echo $list['list_id']; ?>)">
                                <i class="fas fa-plus"></i> <?php echo $text_task_add; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>
                <div class="board-column list-add" onclick="getListForm(<?php echo $board_id; ?>, 0, 1)">
                    <div class="board-column__inner">
                        <div>
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($rows_count == 2) { ?>
            <div id="board-dragbar"></div>
            <div class="board-wrapper sortable-board" id="resizable-bottom">
                <?php if($lists) { ?>
                <?php foreach($lists as $list) { ?>
                <?php if($list['row'] == 1) continue; ?>
                <div class="board-column" id="column-<?php echo $list['list_id']; ?>" data-list-id="<?php echo $list['list_id']; ?>">
                    <div class="board-column__inner">
                        <div class="board-column__top">
                            <h4><?php echo $list['name']; ?></h4>
                            <a class="list-edit" href="javascript:" onclick="getListForm(<?php echo $board_id; ?>, <?php echo $list['list_id']; ?>)">
                                <i class="fas fa-pen"></i>
                            </a>
                        </div>

                        <div class="board-column-tasks sortable-list">
                            <?php if(isset($tasks[$list['list_id']]) && $tasks[$list['list_id']]) { ?>
                            <?php $sort_inc = 1; ?>
                            <?php foreach($tasks[$list['list_id']] as $task) { ?>
                            <div class="bc-task" data-task-id="<?php echo $task['task_id']; ?>" onclick="getTaskForm(<?php echo $task['task_id']; ?>);">
                                <input type="hidden" name="list_id[<?php echo $task['task_id']; ?>]" value="<?php echo $task['list_id']; ?>" />
                                <input type="hidden" name="sort_order[<?php echo $task['task_id']; ?>]" value="<?php echo $task['sort_order']; ?>" />
                                <?php echo $task['name']; ?>

                                <br />
                                <div class="bc-task__bottom">
                                    <?php if($task['date_end'] != '0000-00-00 00:00:00') { ?>
                                    <small><?php echo date('d-m-Y', strtotime($task['date_end'])); ?></small>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php $sort_inc++; ?>
                            <?php } ?>
                            <?php } ?>
                        </div>

                        <div class="board-column__bottom">
                            <a class="task-add" href="javascript:" onclick="addTask(<?php echo $list['list_id']; ?>)">
                                <i class="fas fa-plus"></i> <?php echo $text_task_add; ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php } ?>
                <div class="board-column shadow-element list-add" onclick="getListForm(<?php echo $board_id; ?>, 0, 2)">
                    <i class="fas fa-plus"></i>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php echo $footer; ?>

<script>
    app.params.board_id = <?php echo $board_id; ?>;
</script>