var app = {
  'params': {
    'task_id': 0,
    'board_id': 0
  }
};

$(document).ready(function() {
  initTabs();
  initCustomTextarea();
  initSelectric();
  initCalendar();
  initMiniPopup();
  changeField();
  sortableCheckList();
  sortableCheckListItems();
  initCustomEditor();
  initProgressBars();
  initDateTimePicker();

  localStorage.private_start = Date.now();

  setInterval(function() {
    if(!localStorage.private || localStorage.private == 0) {
      if(Date.now() - localStorage.private_start >= 300000) {
        $.ajax({
          url: 'index.php?action=board/setPrivateMode&private=1',
          type: 'get',
          dataType: 'json',
          success: function(json) {
            removeAlert(2000);
            if(json['success']) {
              localStorage.private = 1;
              location.reload();
            }

            removeAlert(0);
          }
        });
      }
    }
  }, 1000);

  document.addEventListener("visibilitychange", function(){

    if (document.hidden && (!localStorage.private || localStorage.private == 0)){
      $.ajax({
        url: 'index.php?action=board/setPrivateMode&private=1',
        type: 'get',
        dataType: 'json',
        success: function(json) {
          removeAlert(2000);
          if(json['success']) {
            localStorage.private = 1;
            location.reload();
          }

          removeAlert(0);
        }
      });
    }
  });

  $('[target="modal"]').on('click', function() {
      var modal_id = $(this).data('modal');

      modal(modal_id);
  });

  var res_top = document.getElementById('resizable-top');
  var res_bottom = document.getElementById('resizable-bottom');
  var res_bar = document.getElementById('board-dragbar');
  var board_heights = [];
  if(localStorage.res_top) {
    board_heights = JSON.parse(localStorage.res_top);
  }

  if(res_top && res_bottom) {
    
    if(board_heights[app.params.board_id] != undefined) {
      res_top.style.height = board_heights[app.params.board_id];
    }
    
    const drag = (e) => {
      document.selection ? document.selection.empty() : window.getSelection().removeAllRanges();
      new_height = (e.pageY - 70 - res_bar.offsetHeight / 2) + 'px';
      res_top.style.height = new_height;

      board_heights[app.params.board_id] = new_height;
      localStorage.res_top = JSON.stringify(board_heights);
    }

    res_bar.addEventListener('mousedown', () => {
      document.addEventListener('mousemove', drag);
    });

    document.addEventListener('mouseup', () => {
      document.removeEventListener('mousemove', drag);
    });
  }

  // $(".board-column-tasks").droppable({
  //     activeClass: "ui-state-highlight",
  //     accept: ".bc-task",
  //     drop: function(event, ui) {
  //       let list_id = $(this).parent().data('list-id'),
  //           task_id = ui.draggable.data('task-id');

  //         ui.draggable.find('input[name="list_id"]').val(list_id);
  //         ui.draggable.prop('style', 'position: relative;');
  //         console.log(task_id);
  //         changeTaskList(task_id, list_id);

  //         $(this).append(ui.draggable);
  //         return true;
  //     }
  // });
  // $(".bc-task" ).draggable({
  //     classes: {
  //         "ui-draggable": "highlight"
  //     }
  // });

  $(".sortable-board" ).sortable({
    axis: 'x',
    cursor: "move",
    placeholder: "sortable-board placeholder",
    scroll: true,
    scrollSensitivity: 50,
    sort: function(event, ui) {
      $('.sortable-board.placeholder').css({
        width: ui.item.outerWidth() + 'px',
        'min-width': ui.item.outerWidth() + 'px',
        height: ui.item.outerHeight() + 'px'
      });
    },
    update: function(event, ui) {
      changeListSortOrder();
    }
  });

  $(".board-menu-sortable" ).sortable({
    axis: 'y',
    cursor: "move",
    placeholder: "board-menu-sortable placeholder",
    scroll: true,
    scrollSensitivity: 50,
    sort: function(event, ui) {
      $('.board-menu-sortable.placeholder').css({
        width: ui.item.outerWidth() + 'px',
        'min-width': ui.item.outerWidth() + 'px',
        height: ui.item.outerHeight() + 'px'
      });
    },
    update: function(event, ui) {
      changeBoardSortOrder();
    }
  });

  $(".sortable-list" ).sortable({
    connectWith: ['.sortable-list:not(#'+ $(this).attr('id')+')'],
    cursor: "move",
    placeholder: "board-column placeholder",
    scroll: true,
    scrollSensitivity: 50,
    sort: function(event, ui) {
      $('.board-column.placeholder').css({
        width: ui.item.outerWidth() + 'px',
        height: ui.item.outerHeight() + 'px'
      });
    },
    receive: function(event, ui) {
      task_id = ui.item.data('task-id');
      list_id = ui.item.closest('.board-column').data('list-id');
      
      ui.item.find('input[name^="list_id"]').val(list_id);
      changeTaskList(task_id, list_id);
    },
    update: function(event, ui) {
      task_id = ui.item.data('task-id');

      prev_sort = parseFloat($(ui.item[0].previousElementSibling).find('input[name^="sort_order"]').val());
      next_sort = parseFloat($(ui.item[0].nextElementSibling).find('input[name^="sort_order"]').val());
      if(!prev_sort) {
        prev_sort = 0;
      }
      if(!next_sort) {
        if(prev_sort) {
          next_sort = prev_sort + 1000;
        } else {
          next_sort = 500000;
        }
      }

      def = (next_sort - prev_sort) > 0 ? (next_sort - prev_sort) : 1;
      curent_sort = next_sort - (def / 1024);

      ui.item.find('input[name^="sort_order"]').val(curent_sort);
      changeTaskSortOrder(task_id, curent_sort);
    }
  });

});

function sortableCheckList() {
  $(".sortable-check-list" ).sortable({
    axis: 'y',
    cursor: "move",
    placeholder: "sortable-check-list placeholder",
    scroll: true,
    scrollSensitivity: 50,
    sort: function(event, ui) {
      $('.sortable-check-list.placeholder').css({
        height: ui.item.outerHeight() + 'px',
        'min-height': ui.item.outerHeight() + 'px',
        width: ui.item.outerWidth() + 'px'
      });
    },
    update: function(event, ui) {
      changeCheckListSortOrder();
    }
  });
} 
function sortableCheckListItems() {
  $(".sortable-check-list-items").sortable({
    axis: 'y',
    cursor: "move",
    placeholder: "sortable-check-list-items placeholder",
    scroll: true,
    scrollSensitivity: 50,
    sort: function(event, ui) {
      $('.sortable-check-list-items.placeholder').css({
        height: ui.item.outerHeight() + 'px',
        'min-height': ui.item.outerHeight() + 'px',
        width: ui.item.outerWidth() + 'px'
      });
    },
    update: function(event, ui) {
      changeCheckListItemsSortOrder(ui);
    }
  });
} 

function initMiniPopup() {
  $('.js-show-mini-popup').on('click', function() {
    $(this).closest('.mini-popup').find('.mini-popup-content').toggleClass('active');
  });

  $(document).mouseup( function(e){
		var div = $('.mini-popup-content.active');
		if (!div.is(e.target)
		    && div.has(e.target).length === 0 ) { 
			div.removeClass('active');
		}
	});

  $('.mini-popup-close').on('click', function() {
    $('.mini-popup-content.active').removeClass('active');
  });
}

function initCalendar() {
  if($("#date-start-calendar").length) {
    var field_start = $("#date-start-calendar").next('input'),
        date_start = $(field_start).val();

    var dateStartCalendar = jsCalendar.new({
      target : "#date-start-calendar",
      navigator : true,
      monthFormat : "month YYYY",
      dayFormat : "DD",
      firstDayOfTheWeek: 2,
      language : "ru"
    });

    // field_start.on('change', function() {
    //   dateStartCalendar.clearselect();
    //   if($(field_start).val()) {
    //     dateStartCalendar.set(new Date($(field_start).val()));
    //   }
    // });

    if(date_start) {
      dateStartCalendar.set(new Date(date_start));
    }

    dateStartCalendar.onDateClick(function(event, date){
      date_start = date;
      field_start.val(date_start ? jsCalendar.tools.dateToString(date_start, "yyyy-MM-DD") : '').trigger('change');
    });
  }

  if($("#home-page-calendar").length) {
    var homePageCalendar = jsCalendar.new({
      target : "#home-page-calendar",
      navigator : true,
      monthFormat : "month YYYY",
      dayFormat : "DD",
      firstDayOfTheWeek: 2,
      language : "ru"
    });
    

    var date_start = $("#home-page-calendar").data('selected-date') ? $("#home-page-calendar").data('selected-date') : false;
    if (date_start) {
      homePageCalendar.set(new Date(date_start));
    }

    homePageCalendar.onDateClick(function(event, date){
      location.href = '/?selected_date=' + jsCalendar.tools.dateToString(date, "yyyy-MM-DD");
    });
  }
}

function initSelectric() {
  $('.selectric').selectric({});
}

function initCustomTextarea() {
  $('.custom-textarea .custom-textarea__preview').on('click', function(e) {
    let content_height = $(this).outerHeight() + 50;

    $(this).prev('textarea').css('height', content_height + 'px').show().focus();
  });
  $('.custom-textarea textarea').on('keyup', function(e) {
    var textarea_height = $(this).outerHeight();
    var content_height = $(this)[0].scrollHeight;

    if(content_height > textarea_height) {
      $(this).css('height', (content_height + 20) + 'px');
    }
  });
  $('.custom-textarea textarea').on('change', function(e) {
    $(this).hide();
    if($(this).val()) {
      preview_value = $(this).val();
    } else {
      preview_value = 'Добавить описание';
    }
    $(this).next('.custom-textarea__preview').html(preview_value);
  });
  $('.custom-textarea textarea').on('focusout', function(e) {
    $(this).hide();
    if($(this).val()) {
      preview_value = $(this).val();
    } else {
      preview_value = 'Добавить описание';
    }
    $(this).next('.custom-textarea__preview').html(preview_value);
  });

  setTimeout(function() {
    const new_block = document.querySelector('.custom-textarea__field');
    document.addEventListener('click', (e) => {
      const withinBoundaries = e.composedPath().includes(new_block);
    
      if (!withinBoundaries ) {
        if($(new_block).is(':visible')) {
          //$(new_block).hide();
          //$(new_block).prev().show();
        }
      }
    });
  }, 100);
}

function initCustomEditor() {
  var editor;
  
  if($('#custom-editor').length) {
    ClassicEditor.create(document.querySelector('#custom-editor'), {
      toolbar: {
        items: [
          'selectAll', '|',
          'bold', 'italic', '|',
          'bulletedList', 'numberedList', '|',
          'outdent', 'indent', '|',
          'undo', 'redo','|',
          'link', 
          'blockQuote', 
        ]
      }
    }
    ).then( newEditor => {
      editor = newEditor;

      editor.ui.focusTracker.on( 'change:isFocused', ( evt, name, is_focused ) => {
        if(!is_focused) {
          editor_data = editor.getData();
          $('#custom-editor').parent().find('.custom-editor__preview').html(editor_data != '' ? editor_data : $('#custom-editor').attr('placeholder'));
          $('#custom-editor').val(editor_data).trigger('change');
        }
      });
    }).catch( error => {
        console.error( error );
    });

    $('.custom-editor .custom-editor__preview').on('click', function(e) {
      $(this).closest('.custom-editor').toggleClass('active');

      if($(this).closest('.custom-editor').hasClass('active')) {
        if($(this).closest('.custom-editor').find('#custom-editor')) {
          editor.focus();
        } else {
          $(this).closest('.custom-editor').find('textarea').focus();
        }
      }
    });
    $('.custom-editor textarea').on('change, focusout', function(e) {
      $(this).closest('.custom-editor').toggleClass('active');
      $(this).parent().find('.custom-editor__preview').html($(this).val() != '' ? $(this).val() : $(this).attr('placeholder'));
    });
    $('#custom-editor').on('change', function(e) {
      $(this).closest('.custom-editor').toggleClass('active');
    });
  }
}

function initTabs() {
  $('.nav-tabs__link').on('click', function(e) {
    e.preventDefault();
    $(this).closest('.nav-tabs').find('.nav-tabs__link').removeClass('active');
    $(this).addClass('active');

    tab_id = $(this).attr('href');
    $(tab_id).closest('.tab-content').find('.tab-pane').removeClass('active');
    $(tab_id).addClass('active');
  });
}

function setRowsCount(rows_count, board_id) {
  $.ajax({
    url: 'index.php?action=board/setRowsCount',
    type: 'post',
    data: 'rows_count=' + rows_count + '&board_id=' + board_id,
    dataType: 'json',
    success: function(json) {
      location.reload();
    }
  });
}

function addItem(object, param, field, value) {
  $.ajax({
    url: 'index.php?action=common/item/add',
    type: 'post',
    data: 'object=' + object + '&param=' + param + '&' + field + '=' + encodeURIComponent(value),
    dataType: 'json',
    success: function(json) {
      if(object == 'check_list' || object == 'check_list_item') {
        loadCheckLists(app.params.task_id);
      }
      if(object == 'task_schedule_exception') {
        loadTaskScheduleExceptionLists(app.params.task_id);
      }
    }
  });
}
function deleteItem(object, param, field, value) {
  let confirm_delete = confirm("Удалить?");
  if(confirm_delete) {
    $.ajax({
      url: 'index.php?action=common/item/delete',
      type: 'post',
      data: 'object=' + object + '&param=' + param + '&' + field + '=' + encodeURIComponent(value),
      dataType: 'json',
      success: function(json) {
        if(object == 'check_list' || object == 'check_list_item') {
          loadCheckLists(app.params.task_id);
        }
        if(object == 'task') {
          location.reload();
        }
        if(object == 'task_schedule_exception') {
          loadTaskScheduleExceptionLists(app.params.task_id);
        }
      }
    });
  }
}

function changeField() {
  $('input[type="text"][save-on-change], textarea[save-on-change], select[save-on-change]').on('change', function() {
    saveField($(this).attr('name'), $(this).val());
  });

  $('input[type="checkbox"][save-on-change]').on('change', function() {
    if($(this).attr('checkbox-multy') !== undefined) {
      field = false;
      value = {};

      console.log($(this).parent().find('input[checkbox-multy]:checked'));

      $(this).parent().find('input[checkbox-multy]:checked').each(function(i,e) {
       let name = $(e).attr('name');

        if (!value[name]) {
          value[name] = [];
        }

        value[name].push($(e).val());
      });
    } else {
      field = $(this).attr('name');
      if($(this).prop('checked')) {
        value = 1;
      } else {
        value = 0;
      }
    }
    saveField(field, value);
  });
}
function saveField(field, value) {
  if(!field) {
    postdata = value;
  } else {
    postdata = field + '=' + encodeURIComponent(value);
  }

  console.log(postdata);

  $.ajax({
    url: 'index.php?action=common/field/edit',
    type: 'post',
    data: postdata,
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        //location.reload();
      }
      if(json['error']) {
        
      }

      removeAlert(2000);
    }

  });
}
function saveForm(form) {
  $.ajax({
    url: 'index.php?action=common/field/edit',
    type: 'post',
    data: $(form).find('input[type="text"], input[type="hidden"], input[type="checkbox"]:checked, input[type="radio"]:checked, textarea, select'),
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        //location.reload();
      }
      if(json['error']) {
        
      }

      removeAlert(2000);
    }

  });
}

// Task
function addTask(list_id) {
  let task_block = '<div class="bc-task ui-sortable-handle new-task-block"><textarea style="height:70px;resize: none;" name="task_name"></textarea></div>';
  $('#column-' + list_id + ' .board-column-tasks').append(task_block);
  $('.new-task-block textarea').focus();

  $('.new-task-block textarea').on('keyup', function(e) {
    var textarea_height = $(this).outerHeight();
    var content_height = $(this)[0].scrollHeight;

    if(content_height > textarea_height) {
      $(this).css('height', (content_height + 20) + 'px');
    }

    if (e.keyCode === 13) {
      new_task_name = $('.new-task-block textarea').val();
      $('.new-task-block').remove();
      createTask(list_id, new_task_name);
    }
  });

  setTimeout(function() {
    new_block = document.querySelector('.new-task-block');
    document.addEventListener('click', (e) => {
      if($('.new-task-block textarea').length) {
        withinBoundaries = e.composedPath().includes(new_block);
      
        if (!withinBoundaries ) {
          new_task_name = $('.new-task-block textarea').val();
          createTask(list_id, new_task_name);
          new_block.remove();
        }
      }
    });
  }, 100);
}
function createTask(list_id, name) {
  if(name != '') {
    $.ajax({
      url: 'index.php?action=task/form&ajax=1' + (list_id ? '&list_id=' + list_id : ''),
      type: 'post',
      data: 'name=' + name + '&type=simple',
      dataType: 'json',
      success: function(json) {
        removeAlert(0);
        $('.new-task-block').remove();
        if(json['task_info']) {
          $('div[data-list-id="' + list_id + '"] .board-column-tasks').append(getTaskCard(json['task_info']));
        }
        if(json['error']) {
          $('#taskForm').prepend('<div class="alert alert-danger">Заполните все поля</div>');
        }

        removeAlert(2000);
      }

    });
  }
}
function getTaskCard(task_info) {
  return '<div class="bc-task" data-task-id="' + task_info.task_id + '" onclick="getTaskForm(' + task_info.task_id + ');"><input type="hidden" name="list_id[' + task_info.task_id + ']" value="' + task_info.list_id + '" /><input type="hidden" name="sort_order[' + task_info.task_id + ']" value="' + task_info.sort_order + '" />' + task_info.name +'<br /><div class="bc-task__bottom"><small>' + task_info.date_end + '</small></div></div>';
}
function getTaskForm(task_id = 0, list_id = 0) {
  app.params.task_id = task_id;
  
  $.ajax({
    url: 'index.php?action=task/form' + (task_id ? '&task_id=' + task_id : '') + (list_id ? '&list_id=' + list_id : ''),
    type: 'get',
    dataType: 'html',
    success: function(html) {
      $('#taskForm').remove();
      if(html) {
        $('body').append(html);
        loadComments(task_id);
        initTabs();
        initCustomTextarea();
        initSelectric();
        initMiniPopup();
        initCalendar();
        changeField();
        sortableCheckList();
        sortableCheckListItems();
        initCustomEditor();
        modal('taskForm');
        initDateTimePicker();
      }
    }
  });
}

function changeTaskList(task_id, list_id) {
  $.ajax({
    url: 'index.php?action=task/changeTaskList' + (task_id ? '&task_id=' + task_id : '') + (list_id ? '&list_id=' + list_id : ''),
    type: 'get',
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        $('main').prepend('<div class="alert alert-success">Изменения сохранены</div>');
      }
      if(json['error']) {
        $('main').prepend('<div class="alert alert-danger">Ошибка</div>');
      }

      removeAlert(2000);
    }

  });
}
function changeTaskSortOrder(task_id, sort_order) {
  $.ajax({
    url: 'index.php?action=task/changeTaskSortOrder' + (task_id ? '&task_id=' + task_id : '') + '&sort_order=' + (sort_order ? sort_order : 0),
    type: 'get',
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        $('main').prepend('<div class="alert alert-success">Изменения сохранены</div>');
      }
      if(json['error']) {
        $('main').prepend('<div class="alert alert-danger">Ошибка</div>');
      }

      removeAlert(2000);
    }

  });
}
function addTaskStatusHistory(task_id, status_id, date_added) {
  $.ajax({
    url: 'index.php?action=task/addTaskStatusHistory' + (task_id ? '&task_id=' + task_id : '') + '&status_id=' + (status_id ? status_id : 0) + '&date_added=' + (date_added ? date_added : ''),
    type: 'get',
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        $('main').prepend('<div class="alert alert-success">Изменения сохранены</div>');
      }
      if(json['error']) {
        $('main').prepend('<div class="alert alert-danger">Ошибка</div>');
      }

      removeAlert(2000);
    }

  });
}


// Check list
function loadCheckLists(task_id) {
  if(app.params.task_id) {
    $('.check-lists').load('index.php?action=task/getCheckLists&task_id=' + app.params.task_id, function() {
      changeField();
      sortableCheckList();
      sortableCheckListItems();
      initCustomTextarea();
    });
  }
}
function changeCheckListSortOrder() {
  let index = 0, check_list = [];
  $('.sortable-check-list > .check-list').each(function(i,e) {
    check_list_id = $(e).data('check-list-id');
    if(check_list_id) {
      check_list[i] = check_list_id;
      index++;
    }
  });

  if(index == $('.sortable-check-list > .check-list').length) {
    $.ajax({
      url: 'index.php?action=task/sortCheckList',
      type: 'post',
      data: {check_list},
      dataType: 'json',
      success: function(json) {
        removeAlert(2000);
        if(json['success']) {
          
        }
  
        if(json['error']) {
          alert(json['error']);
        }
  
        removeAlert(0);
      }
    });
  }
}
function changeCheckListItemsSortOrder(el) {
  let index = 0, check_list_items = [];

  $(el.item).closest('.sortable-check-list-items').find('.check-list__item').each(function(i,e) {
    check_list_item_id = $(e).data('check-list-item-id');
    if(check_list_item_id) {
      check_list_items[i] = check_list_item_id;
      index++;
    }
  });

  if(index == $(el.item).closest('.sortable-check-list-items').find('.check-list__item').length) {
    $.ajax({
      url: 'index.php?action=task/sortCheckListItems',
      type: 'post',
      data: {check_list_items},
      dataType: 'json',
      success: function(json) {
        removeAlert(2000);
        if(json['success']) {
          
        }
  
        if(json['error']) {
          alert(json['error']);
        }
  
        removeAlert(0);
      }
    });
  }
}

//Board scripts
function changeBoardSortOrder() {
  let board = [];
  $('.board-menu-sortable .board-menu_item').each(function(i,e) {
    board_id = $(e).data('board-id');
    if(board_id) {
      board[i] = board_id;
    }


    if((i+1) == $('.board-menu-sortable .board-menu_item').length) {
      $.ajax({
        url: 'index.php?action=board/sortBoard',
        type: 'post',
        data: {board},
        dataType: 'json',
        success: function(json) {
          removeAlert(2000);
          if(json['success']) {
            
          }
    
          if(json['error']) {
            alert(json['error']);
          }
    
          removeAlert(0);
        }
      });
    }
  });
}
function getBoardForm(board_id = 0) {
  $.ajax({
    url: 'index.php?action=board/form' + (board_id ? '&board_id=' + board_id : ''),
    type: 'get',
    dataType: 'html',
    success: function(html) {
      $('#boardForm').remove();
      if(html) {
        $('body').append(html);
        modal('boardForm');

        changeField();
      }
    }
  });
}
function saveBoard(board_id = 0) {
  $.ajax({
    url: 'index.php?action=board/form' + (board_id ? '&board_id=' + board_id : ''),
    type: 'post',
    data: $('#boardForm input[type="text"], #boardForm input[type="checkbox"]:checked, #boardForm input[type="radio"]:checked, #boardForm select, #boardForm textarea'),
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        location.reload();
      }
      if(json['error']) {
        $('#boardForm').prepend('<div class="alert alert-danger">Заполните все поля</div>');
      }

      removeAlert(2000);
    }

  });
}
function deleteBoard(board_id) {
  let confirm_delete = confirm("Удалить доску?");
  if(confirm_delete) {
    $.ajax({
      url: 'index.php?action=board/delete&board_id=' + board_id,
      type: 'get',
      dataType: 'json',
      success: function(json) {
        removeAlert(2000);
        if(json['success']) {
          location.reload();
        }

        removeAlert(0);
      }

    });
  }
}
function setPrivateMode(cbx) {
  let pin_code = '';

  if(!$(cbx).prop('checked')) {
    pin_code = prompt('Введите пин-код', '');

    if(pin_code != '4221') {
      alert('Неверный пин-код!');
      $('input[name="private_mode"]').prop('checked', true);
      return;
    }
  }

  localStorage.private = $(cbx).prop('checked') ? 1 : 0;

  $.ajax({
    url: 'index.php?action=board/setPrivateMode&private=' + ($(cbx).prop('checked') ? 1 : 0),
    type: 'get',
    dataType: 'json',
    success: function(json) {
      removeAlert(2000);
      if(json['success']) {
        location.reload();
      }

      removeAlert(0);
    }
  });
}


//List scripts
function changeListSortOrder() {
  let list = [];
  $('.sortable-board .board-column').each(function(i,e) {
    list_id = $(e).data('list-id');
    if(list_id) {
      list[i] = list_id;
    }


    if((i+1) == $('.sortable-board .board-column').length) {
      $.ajax({
        url: 'index.php?action=list/sortList',
        type: 'post',
        data: {list},
        dataType: 'json',
        success: function(json) {
          removeAlert(2000);
          if(json['success']) {
            
          }
    
          if(json['error']) {
            alert(json['error']);
          }
    
          removeAlert(0);
        }
      });
    }
  });
}
function getListForm(board_id = 0, list_id = 0, row = 1) {
  $.ajax({
    url: 'index.php?action=list/form' + (list_id ? '&list_id=' + list_id : '') + (board_id ? '&board_id=' + board_id : '') + '&row=' + row,
    type: 'get',
    dataType: 'html',
    success: function(html) {
      $('#listForm').remove();
      if(html) {
        $('body').append(html);
        modal('listForm');
      }
    }
  });
}
function saveList(board_id = 0, list_id = 0) {
  $.ajax({
    url: 'index.php?action=list/form' + (list_id ? '&list_id=' + list_id : '') + (board_id ? '&board_id=' + board_id : ''),
    type: 'post',
    data: $('#listForm input[type="text"], #listForm input[type="checkbox"]:checked, #listForm input[type="radio"]:checked, #listForm select, #listForm textarea'),
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        changeListSortOrder();
        location.reload();
      }
      if(json['error']) {
        $('#listForm').prepend('<div class="alert alert-danger">Заполните все поля</div>');
      }

      removeAlert(2000);
    }

  });
}
function deleteList(list_id) {
  let confirm_delete = confirm("Удалить список?");
  if(confirm_delete) {
    $.ajax({
      url: 'index.php?action=list/delete&list_id=' + list_id,
      type: 'get',
      dataType: 'json',
      success: function(json) {
        removeAlert(2000);
        changeListSortOrder();
        if(json['success']) {
          location.reload();
        }

        removeAlert(0);
      }

    });
  }
}


//User scripts
function getUserForm(user_id = 0) {
  $.ajax({
    url: 'index.php?action=user/form' + (user_id ? '&user_id=' + user_id : ''),
    type: 'get',
    dataType: 'html',
    success: function(html) {
      $('#userForm').remove();
      if(html) {
        $('body').append(html);
        modal('userForm');
      }
    }
  });
}
function saveUser(user_id = 0) {
  $.ajax({
    url: 'index.php?action=user/form' + (user_id ? '&user_id=' + user_id : ''),
    type: 'post',
    data: $('#userForm input[type="text"], #userForm input[type="password"], #userForm input[type="checkbox"]:checked, #userForm input[type="radio"]:checked, #userForm select, #userForm textarea'),
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        location.reload();
      }
      if(json['error']) {
        $('#userForm').prepend('<div class="alert alert-danger">Заполните все поля</div>');
      }

      removeAlert(2000);
    }

  });
}
function loginUser() {
  $.ajax({
    url: 'index.php?action=user/login',
    type: 'post',
    data: $('#loginForm input[type="text"], #loginForm input[type="password"], #loginForm input[type="checkbox"]:checked, #loginForm input[type="radio"]:checked, #loginForm select, #loginForm textarea'),
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['redirect']) {
        location.href = json['redirect'];
      }
      if(json['error']) {
        $('#loginForm').prepend('<div class="alert alert-danger">' + json['error'] + '</div>');
      }

      removeAlert(2000);
    }

  });
}
function registerUser() {
  $.ajax({
    url: 'index.php?action=user/register',
    type: 'post',
    data: $('#registerForm input[type="text"], #registerForm input[type="password"], #registerForm input[type="checkbox"]:checked, #registerForm input[type="radio"]:checked, #registerForm select, #registerForm textarea'),
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['redirect']) {
        location.href = json['redirect'];
      }
      if(json['error']) {
        $('#registerForm').prepend('<div class="alert alert-danger">' + json['error'] + '</div>');
      }

      removeAlert(2000);
    }

  });
}
function deleteUser(user_id) {
  let confirm_delete = confirm("Удалить пользователя?");
  if(confirm_delete) {
    $.ajax({
      url: 'index.php?action=user/delete&user_id=' + user_id,
      type: 'get',
      dataType: 'json',
      success: function(json) {
        removeAlert(2000);
        if(json['success']) {
          location.reload();
        }
        if(json['error']) {
          alert(json['error']);
        }

        removeAlert(0);
      }

    });
  }
}

// Task schedule
function loadTaskScheduleExceptionLists(task_id) {
  if(app.params.task_id) {
    $('.js-schedule-exceptions').load('index.php?action=task/getTaskScheduleExceptions&task_id=' + app.params.task_id, function() {
      // some action
    });
  }
}

function loadComments(task_id) {
  $('.js-comment-list').load('index.php?action=task/getComments' + (task_id ? '&task_id=' + task_id : ''));
}
function saveComment(text = '', task_id = 0, comment_id = 0) {
  if(text != '') {
    $.ajax({
      url: 'index.php?action=task/saveComment' + (task_id ? '&task_id=' + task_id : '') + (comment_id ? '&comment_id=' + comment_id : ''),
      type: 'post',
      data: 'text=' + text,
      dataType: 'json',
      success: function(json) {
        removeAlert(0);
        if(json['success']) {
          loadComments(task_id);
        }
        if(json['error']) {
          alert(json['error']);
        }

        removeAlert(2000);
      }

    });
  }
}
function editComment(comment_block, task_id) {
  comment_id = $(comment_block).data('comment-id');
  comment_text = $(comment_block).find('.js-comment-text').html();
  $(comment_block).find('.js-editor').html('<textarea name="comment">' + comment_text + '</textarea><button class="btn btn-primary" type="button" onclick="saveComment($(this).prev().val(), ' + task_id + ', ' + comment_id + ')">Сохранить</button>');
}
function deleteComment(comment_id, task_id) {
  let confirm_delete = confirm("Удалить комментарий?");
  if(confirm_delete) {
    $.ajax({
      url: 'index.php?action=task/deleteComment&comment_id=' + comment_id,
      type: 'get',
      dataType: 'json',
      success: function(json) {
        removeAlert(2000);
        if(json['success']) {
          loadComments(task_id);
        }
        if(json['error']) {
          alert(json['error']);
        }

        removeAlert(0);
      }

    });
  }
}


function setFilter() {
  $.ajax({
    url: 'index.php?action=main/home/setFilter',
    type: 'post',
    data: $('#filterForm input[type="checkbox"]:checked, #filterForm input[type="radio"]:checked, #filterForm select'),
    dataType: 'json',
    success: function(json) {
      removeAlert(0);
      if(json['success']) {
        location.reload();
      }
      if(json['error']) {
        
      }

      removeAlert(2000);
    }

  });
}

function modal(modal_id, action = 'show') {
  if(action == 'show') {
    $('.modal#' + modal_id).show();
    $('.modal-overlay#overlay-' + modal_id).remove();
    $('body').append('<div class="modal-overlay" id="overlay-' + modal_id + '"></div>');
    $('.modal-overlay').css({
        'display': 'block'
    });

    $('.modal-overlay#overlay-' + modal_id).on('click', function() {
        $('.modal#' + modal_id).hide();
        $(this).remove();
    });
  } else {
    $('.modal#' + modal_id).hide();
    $('.modal-overlay#overlay-' + modal_id).remove();
  }
}

function modalFull(id) {
  $('#' + id).toggleClass('full');
}

function removeAlert(time) {
  if(time) {
    setTimeout(function() {
      $('.alert').remove();
    }, time);
  } else {
    $('.alert').remove();
  }
}

function initProgressBars() {
  if(!$('.progress-bar').length) return;

  $('.progress-bar').each(function(i,e) {
    let total = parseInt($(e).data('total')),
        progress = parseInt($(e).data('progress'));

    if(total && progress) {
      let percent = Math.round(progress / (total / 100));

      if(percent) {
        $(e).html('<div class="progress-bar__progress" style="width: ' + percent + '%">' + percent + '%</div>');
      } else {
        $(e).html('<div class="progress-bar__empty">0%</div>');
      }
    } else {
      $(e).html('<div class="progress-bar__empty">0%</div>');
    }
  });
}

function initDateTimePicker() {
  $.datetimepicker.setLocale('en');

  $('[data-date]').datetimepicker({
    language: 'en',
    format: 'Y-m-d',
    timepicker: false
  });
}