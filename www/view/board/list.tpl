<?php echo $header; ?>
<div class="container">
    <h1 class="title"><?php echo $text_board_list; ?></h1>
    <div class="board-list">
        <?php if($boards) { ?>
        <?php foreach($boards as $board) { ?>
        <div class="board-list__item shadow-element">
            <p><?php echo $board['name']; ?></p>
            <a class="btn btn-primary board-list_link" href="index.php?action=board/view&board_id=<?php echo $board['board_id']; ?>">
                <i class="fas fa-eye"></i>
                <?php echo $text_open; ?>
            </a>
            <button class="btn btn-primary board-list_button" type="button" onclick="getBoardForm(<?php echo $board['board_id']; ?>)">
                <i class="fas fa-pen"></i>
                <?php echo $text_edit; ?>
            </button>
        </div>
        <?php } ?>
        <?php } ?>
        <div class="board-list__item shadow-element board-list__add" onclick="getBoardForm()">
            <i class="fas fa-plus"></i>
        </div>
    </div>
</div>
<?php echo $footer; ?>