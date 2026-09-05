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

        </div> 

        <div class="main-content">
          <div class="main-content__data">
            
            <div class="task-calendar">
              <div class="task-calendar__top">
                <div class="task-calendar__left">
                  <h1 class="task-calendar__title">Report</h1>

                </div>

              </div>
              <div class="task-calendar__bottom">
                <div class="task-calendar__days">
                  <div class="task-calendar__list">
                      <a href="/" class="task-calendar__day active">
                        <span>01.01</span>
                      </a>
                      <a href="/" class="task-calendar__day ">
                        <span>02.01</span>
                      </a>
                      <a href="/" class="task-calendar__day ">
                        <span>03.01</span>
                      </a>
                  </div>
                </div>

                <div class="task-calendar__days">
                  <div class="task-calendar__list">
                      <?php foreach($reports as $k => $report) { ?>
                      <a href="<?php echo $report['href']; ?>" class="task-calendar__day <?php echo $k == $period ? ' active ' : ''; ?>">
                        <span><?php echo $report['name']; ?></span>
                      </a>
                      <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            

            <div class="reports">
                <div class="task-report__list">
                    <?php foreach($task_reports as $task_report) { ?>
                    <div class="task-report__item">
                        <div><?php echo $task_report['name']; ?></div>
                        <div>Progress: <?php echo $task_report['done']; ?> / <?php echo $task_report['total_tasks']; ?></div>
                        <div class="progress-bar progress-bar--horizontal" data-total="<?php echo $task_report['total_tasks']; ?>" data-progress="<?php echo $task_report['done']; ?>"></div>
                    </div>
                    <?php } ?>
                </div>
                <?php if($period != 'day') { ?>
                <div class="task-report__graph">
                    <canvas id="report-graph"></canvas>
                </div>
                <?php } ?>
            </div>
            
          </div>
        </div>

      </div>
    </div>
<?php echo $footer; ?>
<script>
$(document).ready(function() {
    const ctx = document.getElementById('report-graph');

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
              display: true,
              text: 'Graph'
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
                    display: true // подписи под графиком
                },
                grid: {
                    drawTicks: true // маленькие деления
                }
              },
              y: {
                min: 0,
                max: 100,
                beginAtZero: true,
                ticks: {
                  color: '#000',
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