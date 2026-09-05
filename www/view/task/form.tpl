<div class="modal modal-form" id="taskForm" <?php if(isset($task_id) && $task_id) { ?> data-task-id="<?php echo $task_id; ?>" <?php } ?> >
    <?php if (isset($cover_value) && $cover_value) { ?>
    <div class="task-cover">
        <div class="task-cover__area" style="background-color: <?php echo $cover_value; ?>;"></div>
    </div>
    <?php } ?>
    <div class="modal-header">
        <div class="form-buttons">

        </div>
        <button class="btn btn-fill" onclick="modal('taskForm', 'hide');"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-content">
        <div class="modal-main">
            <div class="modal-body">
                <div class="task-form" id="taskForm">
                    <div class="form-param">
                        <label>
                            <p><?php echo $text_name; ?></p>
                            <div class="custom-editor">
                                <textarea save-on-change name="task[<?php echo $task_id; ?>][name]" placeholder="<?php echo $text_name; ?>"><?php echo isset($name) ? $name : ''; ?></textarea>
                                <div class="custom-editor__preview">
                                    <?php echo isset($name) && $name != '' ? $name : '' ?>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="form-param">
                        <label>
                        <p><?php echo $text_description; ?></p>
                        <div class="custom-editor">
                            <textarea save-on-change name="task[<?php echo $task_id; ?>][description]" placeholder="<?php echo $text_description; ?>" id="custom-editor"><?php echo isset($description) ? $description : ''; ?></textarea>
                            <div class="custom-editor__preview">
                                <?php echo isset($description) && $description != '' ? $description : 'Добавить описание' ?>
                            </div>
                        </div>
                        </label>
                    </div>

                    <div class="check-lists sortable-check-list">
                        <?php echo isset($check_lists) ? $check_lists : ''; ?> 
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <ul class="nav-tabs">
                    <li class="nav-tabs__item">
                        <a class="nav-tabs__link active" href="#tab-schedule">Расписание</a>
                    </li>
                    <li class="nav-tabs__item">
                        <a class="nav-tabs__link" href="#tab-comments"><?php echo $text_comments; ?></a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab-schedule">
                        <div class="form-line">
                            <div>Тип задачи:</div>
                            <label>
                                <select save-on-change name="task[<?php echo $task_id; ?>][type]" onchange="$('.js-type-item').hide(); $('.js-type-' + $(this).val()).show();">
                                    <option value="simple" <?php echo (isset($type) && $type == 'simple') ? 'selected' : ''; ?> >Единоразовая</option>
                                    <option value="date" <?php echo (isset($type) && $type == 'date') ? 'selected' : ''; ?> >Единоразовая по дате</option>
                                    <option value="custom" <?php echo (isset($type) && $type == 'custom') ? 'selected' : ''; ?> >По расписанию</option>
                                    <option value="monthly" <?php echo (isset($type) && $type == 'monthly') ? 'selected' : ''; ?> >Раз в месяц</option>
                                    <option value="yearly" <?php echo (isset($type) && $type == 'yearly') ? 'selected' : ''; ?> >Раз в год</option>
                                    <option value="cumulative" <?php echo (isset($type) && $type == 'cumulative') ? 'selected' : ''; ?> >Накопительная</option>
                                </select>
                            </label>
                        </div>

                        <div class="task-schedule__list form-line js-type-item js-type-date" style="display:<?php echo (isset($type) && $type == 'date') ? 'flex' : 'none'; ?>;">
                            <div class="task-schedule__header">
                                <div>Дата</div>
                                <div>Время с - до:</div>
                            </div>

                            <div class="task-schedule__item">
                                <div class="task-schedule__checkbox">
                                    <input type="text" name="task[<?php echo $task_id; ?>][date_start]" value="<?php echo $date_start; ?>" save-on-change data-date />
                                </div>
                                <div class="task-schedule__inputs">
                                    <input type="text" name="task[<?php echo $task_id; ?>][time_start]" value="<?php echo $time_start; ?>" save-on-change /> - 
                                    <input type="text" name="task[<?php echo $task_id; ?>][time_end]" value="<?php echo $time_end; ?>" save-on-change />
                                </div>
                            </div>
                        </div>

                        <div class="task-schedule__list form-line js-type-item js-type-custom" style="display:<?php echo (isset($type) && $type == 'custom') ? 'flex' : 'none'; ?>;">
                            <div class="task-schedule__header">
                                <div>День недели</div>
                                <div>Время с - до:</div>
                                <div>Дата с - до:</div>
                            </div>
                            <?php for($i = 1; $i <= 7; $i++) { ?>
                            <div class="task-schedule__item">
                                <label class="task-schedule__checkbox">
                                    <input type="checkbox" name="task_schedule[<?php echo $task_id; ?>][<?php echo $i; ?>][status]" value="1" <?php echo isset($schedule_days[$i]) ? 'checked' : ''; ?> save-on-change /> <?php echo ${'text_week_' . $i}; ?> 
                                </label>
                                <div class="task-schedule__inputs">
                                    <input type="text" name="task_schedule[<?php echo $task_id; ?>][<?php echo $i; ?>][time_start]" value="<?php echo isset($schedule_days[$i]) ? $schedule_days[$i]['time_start'] : '00:00:00'; ?>" save-on-change /> - 
                                    <input type="text" name="task_schedule[<?php echo $task_id; ?>][<?php echo $i; ?>][time_end]" value="<?php echo isset($schedule_days[$i]) ? $schedule_days[$i]['time_end'] : '00:00:00'; ?>" save-on-change />
                                </div>
                                <div class="task-schedule__inputs">
                                    <input type="text" name="task_schedule[<?php echo $task_id; ?>][<?php echo $i; ?>][date_start]" value="<?php echo isset($schedule_days[$i]) ? $schedule_days[$i]['date_start'] : '0000-00-00'; ?>" save-on-change data-date /> - 
                                    <input type="text" name="task_schedule[<?php echo $task_id; ?>][<?php echo $i; ?>][date_end]" value="<?php echo isset($schedule_days[$i]) ? $schedule_days[$i]['date_end'] : '0000-00-00'; ?>" save-on-change  data-date />
                                </div>
                            </div>
                            <?php } ?>

                            <hr />

                            <div>Исключения:</div>

                            <div class="task-schedule__exceptions">
                                <div class="task-schedule__exception-action">
                                    <input type="text" name="task_schedule_exception" value="<?php echo date('d-m-Y'); ?>" data-date >
                                    <button class="btn btn-fill" type="button" onclick="addItem('task_schedule_exception', <?php echo $task_id; ?>, 'date', $(this).prev().val());">Добавить</button>
                                </div>
                                
                                <div class="task-schedule__exception-list js-schedule-exceptions">
                                    <?php if($schedule_exceptions) { ?>
                                        <?php echo $schedule_exceptions; ?>
                                    <?php } ?> 
                                </div>
                            </div>

                        </div>

                        <div class="task-schedule__list form-line js-type-item js-type-monthly" style="display:<?php echo (isset($type) && $type == 'monthly') ? 'flex' : 'none'; ?>;">
                            <div class="task-schedule__header">
                                <div>День</div>
                                <div>Время с - до:</div>
                            </div>

                            <div class="task-schedule__item">
                                <div class="task-schedule__checkbox">
                                    <div id="date-start-calendar"></div>
                                    <select name="task[<?php echo $task_id; ?>][day_number]" save-on-change>
                                        <?php for($i = 1; $i <= 31; $i++) { ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i == $day_number ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="task-schedule__inputs">
                                    <input type="text" name="task[<?php echo $task_id; ?>][time_start]" value="<?php echo $time_start; ?>" save-on-change /> - 
                                    <input type="text" name="task[<?php echo $task_id; ?>][time_end]" value="<?php echo $time_end; ?>" save-on-change />
                                </div>
                            </div>
                        </div>

                        <div class="task-schedule__list form-line js-type-item js-type-yearly" style="display:<?php echo (isset($type) && $type == 'yearly') ? 'flex' : 'none'; ?>;">
                            <div class="task-schedule__header">
                                <div>Дата</div>
                                <div>Время с - до:</div>
                            </div>

                            <div class="task-schedule__item">
                                <div class="task-schedule__checkbox">
                                    <input type="text" name="task[<?php echo $task_id; ?>][date_start]" value="<?php echo $date_start; ?>" save-on-change data-date />
                                </div>
                                <div class="task-schedule__inputs">
                                    <input type="text" name="task[<?php echo $task_id; ?>][time_start]" value="<?php echo $time_start; ?>" save-on-change /> - 
                                    <input type="text" name="task[<?php echo $task_id; ?>][time_end]" value="<?php echo $time_end; ?>" save-on-change />
                                </div>
                            </div>
                        </div>

                        <div class="task-schedule__list form-line js-type-item js-type-cumulative" style="display:<?php echo (isset($type) && $type == 'cumulative') ? 'flex' : 'none'; ?>;"></div>
                    </div>

                    <div class="tab-pane" id="tab-comments">
                        <?php if(isset($task_id) && $task_id) { ?>
                        <div class="task-comment">
                            <div class="task-comment__new">
                                <textarea name="comment" placeholder="Ваш комментарий..."></textarea>
                                <button class="btn btn-fill" type="button" onclick="saveComment($(this).prev().val(), <?php echo $task_id; ?>)"><?php echo $text_save; ?></button>
                            </div>
                
                            <div class="task-comment__list js-comment-list">
                                
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-aside">
            <div class="task-menu">
                <button class="btn btn-fill" type="button" onclick="addItem('check_list', <?php echo $task_id; ?>, 'name', 'Чек-лист');" class="btn btn-fill">Чек-лист</button>

                <div class="task-members mini-popup">
                    <button type="button" class="btn btn-info js-show-mini-popup">Участники</button>
                    <div class="task-members__popup mini-popup-content">
                        <table class="members">
                            <thead>
                                <tr>
                                    <td>Email</td>
                                    <td>Только просмотр</td>
                                    <td>Просмотр и Редактирование</td>
                                    <td>Полный доступ</td>
                                    <td>Удалить</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($members) { ?>
                                <?php foreach($members as $member) { ?>
                                <tr class="form">
                                    <td>
                                        <label>
                                            <input type="hidden" name="task[<?php echo $task_id; ?>][members][<?php echo $member['member_id']; ?>][email]" value="<?php echo $member['email']; ?>" readonly /> <?php echo $member['email']; ?>
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input type="radio" name="task[<?php echo $task_id; ?>][members][<?php echo $member['member_id']; ?>][priv]" value="view" <?php echo $member['priv'] == 'view' ? 'checked' : ''; ?> onchange="saveForm($(this).closest('.form'))" />
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input type="radio" name="task[<?php echo $task_id; ?>][members][<?php echo $member['member_id']; ?>][priv]" value="edit" <?php echo $member['priv'] == 'edit' ? 'checked' : ''; ?> onchange="saveForm($(this).closest('.form'))" />
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input type="radio" name="task[<?php echo $task_id; ?>][members][<?php echo $member['member_id']; ?>][priv]" value="all" <?php echo $member['priv'] == 'all' ? 'checked' : ''; ?> onchange="saveForm($(this).closest('.form'))" />
                                        </label>
                                    </td>
                                    <td>
                                        <input type="radio" name="task[<?php echo $task_id; ?>][members][<?php echo $member['member_id']; ?>][priv]" value="del" onchange="saveForm($(this).closest('.form'))" />
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr class="form">
                                    <td>
                                        <label>
                                            <input type="text" name="task[<?php echo $task_id; ?>][members][0][email]" placeholder="Email" value="" />
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input type="radio" name="task[<?php echo $task_id; ?>][members][0][priv]" value="view" checked />
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input type="radio" name="task[<?php echo $task_id; ?>][members][0][priv]" value="edit" />
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <input type="radio" name="task[<?php echo $task_id; ?>][members][0][priv]" value="all" />
                                        </label>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary" onclick="saveForm($(this).closest('.form'))">Добавить</button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <button class="btn mini-popup-close" type="button">Закрыть</button> 
                    </div>
                </div>


                <div class="cover mini-popup">
                    <button type="button" class="btn btn-info js-show-mini-popup">Обложка</button>
                    <div class="cover__popup mini-popup-content form">
                        <div class="cover-list">
                            <?php foreach($covers as $cover) { ?>
                            <div class="cover-item">
                                <input id="cover-<?php echo $cover['cover_id']; ?>" type="radio" name="task[<?php echo $task_id; ?>][cover_id]" value="<?php echo $cover['cover_id']; ?>" <?php echo $cover_id && $cover_id == $cover['cover_id'] ? ' checked ' : ''; ?> />
                                <label for="cover-<?php echo $cover['cover_id']; ?>" style="background-color: <?php echo $cover['value']; ?>;"></label>
                            </div>
                            <?php } ?>
                            <div class="cover-item">
                                <input id="cover-none" type="radio" name="task[<?php echo $task_id; ?>][cover_id]" value="0" />
                                <label for="cover-none" style="background-color: #fff;">Нет</label>
                            </div>
                        </div>
        
                        <div class="deadline__buttons">
                            <button class="btn btn-success js-save-form" type="button" onclick="saveForm($(this).closest('.form'));">Сохранить</button> 
                            <button class="btn mini-popup-close" type="button">Закрыть</button> 
                        </div>
                    </div>
                </div>

                <button type="button" onclick="" class="btn btn-info" disabled>Вложение (v1.1)</button>

                <?php if(isset($task_id) && $task_id) { ?>
                <button type="button" onclick="deleteItem('task', <?php echo $task_id; ?>);" class="btn btn-danger"><?php echo $text_delete; ?></button> 
                <?php } ?>

                <hr />

                <div class="form-param">
                    <div class="form-line">
                        <div><?php echo $text_list; ?>:</div>
                        <label>
                            <select class="selectric selectric-light" save-on-change name="task[<?php echo $task_id; ?>][list_id]">
                                <?php foreach($boards as $board) { ?>
                                <optgroup label="<?php echo $board['name']; ?>">
                                    <?php foreach($lists as $list) { ?>
                                    <?php if($list['board_id'] != $board['board_id']) continue; ?>
                                    <option value="<?php echo $list['list_id']; ?>" <?php echo (isset($list_id) && $list_id == $list['list_id']) ? 'selected' : ''; ?> ><?php echo $list['name']; ?></option>
                                    <?php } ?>
                                </optgroup>
                                <?php } ?>
                            </select>
                        </label>
                    </div>
                    <div class="form-line">
                        <div>Stage:</div>
                        <label>
                            <select class="selectric selectric-light" save-on-change name="task[<?php echo $task_id; ?>][stage_id]">
                                <?php foreach($stages as $stage) { ?>
                                    <option value="<?php echo $stage['stage_id']; ?>" <?php echo isset($stage_id) && $stage_id == $stage['stage_id'] ? 'selected' : ''; ?> ><?php echo $stage['name']; ?></option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>