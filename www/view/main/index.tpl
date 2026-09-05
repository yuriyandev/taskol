<?php echo $header; ?>

      <div class="main">

        <div class="sidebar">
          <div class="board-menu">
            <div class="board-menu-sortable shadow-element">
                <?php if($boards) { ?>
                <?php foreach($boards as $board) { ?>
                <div class="board-menu_item" data-board-id="<?php echo $board['board_id']; ?>">
                    <a class="board-menu_link <?php echo $board_id == $board['board_id'] ? ' active' : ''; ?>" href="<?php echo $board['href']; ?>">
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
          </div>

          <!-- <div class="user">
            <div class="card shadow-element">
              <div class="upper">
                <img src="assets/image/profile-bg.jpg" >
              </div>
              <div class="user text-center">
                <div class="profile">
                  <img src="assets/image/avatar.jpg" width="80">
                </div>
              </div>

              <div class="text-center mt-3">
                <span class="user-name">John Dou</span>
                <div class="flex-sb mt-1-5 home-list"> 
                </div>
              </div>
            </div>

            <div class="actions shadow-element">
              <div class="action">
                <button type="button" onclick="getTaskForm()" class="btn btn-primary">
                  <i class="fas fa-plus-square"></i> <?php echo $text_task_add; ?>
                </button>
                <button type="button" onclick="getUserForm()" class="btn btn-primary">
                  <i class="fas fa-plus-square"></i> <?php echo $text_user_add; ?>
                </button>
                <button type="button" onclick="getBoardForm()" class="btn btn-primary">
                  <i class="fas fa-plus-square"></i> <?php echo $text_board_add; ?>
                </button>
              </div>
            </div>
          </div> -->

        </div> 

        <div class="main-content">
          <div class="main-content__data">
            
            <div class="task-calendar">
              <div class="task-calendar__top">
                <div class="task-calendar__left">
                  <h1 class="task-calendar__title">Task list</h1>
                  <?php if($task_report) { ?>
                  <div>
                    <div>Total: <?php echo $task_report['done']; ?> / <?php echo $task_report['total_tasks']; ?> (<?php echo $task_report['percent_done']; ?>%)</div>
                  </div>
                  <?php } ?>
                </div>
                <div class="task-calendar__right">
                  <div class="chart-container">
                    <canvas id="status-graph"></canvas>
                  </div>
                  <!-- <div class="task-calendar__filter">
                    <button type="button" onclick="modal('filterForm');">
                      <i class="fas fa-wrench"></i>
                    </button>
                  </div> -->
                </div>
              </div>
              <div class="task-calendar__bottom">
                <div class="task-calendar__days">
                  
                  <div class="task-calendar__day">
                    <div class="mini-popup">
                      <div class="js-show-mini-popup">
                          <?php echo $selected_year; ?>, <?php echo $selected_month; ?>
                      </div>
                      <div class="mini-popup-content">
                          <div class="home-page__calendar" id="home-page-calendar" data-selected-date="<?php echo $selected_date; ?>"></div>
                          <br>
                          <a href="<?php echo $current_date_href; ?>" class="btn btn-fill">
                            Today
                          </a>
                      </div>
                    </div>
                  </div>
                  <div class="task-calendar__list">
                    <?php foreach($selected_week as $item) { ?>
                    <a href="<?php echo $item['href']; ?>" class="task-calendar__day <?php echo $item['date'] == $selected_date ? ' active' : ''; ?>">
                      <span><?php echo $item['dow']; ?></span>
                      <span><?php echo $item['day']; ?></span>
                    </a>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>

            <?php if($tasks) { ?>
            <div class="task-list">
              <div class="task-list__header">
                <div class="task-list__column">Description</div>
                <div class="task-list__column">Status</div>
                <div class="task-list__column hidden-xs">Schedule </div>
                <div class="task-list__column hidden-xs">Stage</div>
                <div class="task-list__column hidden-xs">Deadline</div>
              </div>
              <?php foreach($tasks as $task) { ?>
              
                <div class="task-item" style="<?php echo $task['list_color'] ? 'border-color: ' . $task['list_color'] : ''; ?>">
                  <div class="task-list__column" onclick="getTaskForm(<?php echo $task['task_id']; ?>);">
                    <div class="task-item__name">
                      <?php echo $task['time_start']; ?><?php echo $task['time_end'] ? ' - ' . $task['time_end'] : ''; ?>
                      <?php echo $task['name']; ?>
                    </div>
                    <div class="task-item__list"><?php echo $task['board_name']; ?> / <?php echo $task['list']; ?></div>
                    <!-- <div class="task-item__date"><?php echo $text_date_added; ?>: <?php echo date('d-m-Y', strtotime($task['date_added'])); ?></div> -->
                  </div>
                  <div class="task-list__column">
                    <div class="custom-radio custom-radio--type1">

                      <label class="task-list__radio-status">
                        <input type="radio" name="task_status[<?php echo $task['task_id']; ?>]" value="1" <?php echo $task['status'] ? 'checked' : ''; ?> onchange="addTaskStatusHistory(<?php echo $task['task_id']; ?>, 1, '<?php echo $selected_date; ?>');" />
                        <span><?php echo $statuses[1]['icon']; ?></span>
                      </label>
                      <label class="task-list__radio-status">
                        <input type="radio" name="task_status[<?php echo $task['task_id']; ?>]" value="0" <?php echo !$task['status'] ? 'checked' : ''; ?> onchange="addTaskStatusHistory(<?php echo $task['task_id']; ?>, 0, '<?php echo $selected_date; ?>');" />
                        <span><?php echo $statuses[0]['icon']; ?></span>
                      </label>

                    </div>
                  </div>
                  <div class="task-list__column hidden-xs">
                    <?php echo $task['type'] == 'simple' ? '-' : ''; ?>
                    <?php echo $task['type'] == 'date' ? $task['date_start'] : ''; ?>
                    <?php echo $task['type'] == 'custom' ? $task['days'] : ''; ?>
                  </div>
                  <div class="task-list__column hidden-xs"></div>
                  <div class="task-list__column hidden-xs"></div>
                </div>
              <?php } ?>
            </div>
            <?php } ?>
          </div>
        </div>

      </div>
    </div>

<div class="modal modal-form" id="filterForm">
    <div class="modal-header">
      <span>Фильтр</span>
    </div>
    <div class="modal-body">

        <div class="filter-form" id="filterForm">
            <div class="form-param">
              
          </div>
        </div>
    </div>
    <div class="modal-footer">
      <button type="button" onclick="setFilter()" class="btn btn-primary">
        Применить
      </button>
    </div>
</div>
<?php echo $footer; ?>
<script>
$(document).ready(function() {
    const ctx = document.getElementById('status-graph');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $status_history_labels; ?>,
            datasets: <?php echo $status_history_values; ?>
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            title: {
              display: false,
              text: 'Title'
            },
            legend: {
              display: false,
              labels: {
                  color: 'rgb(255, 99, 132)'
              }
            },
            subtitle: {
              display: false,
              text: 'Subtitle'
            }
          },
          scales: {
              x: {
                ticks: {
                    display: false // скрываем подписи под графиком
                },
                grid: {
                    drawTicks: false // можно скрыть маленькие деления тоже
                }
              },
              y: {
                min: 0,
                max: 100,
                beginAtZero: true,
                ticks: {
                  color: '#fff',
                  font: {
                      size: 10
                  }
                }
              }
          }
        }
    });
});
</script>