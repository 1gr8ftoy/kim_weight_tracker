@showGoalStats = (id) ->
  $("div#single-goal-stats-div").fadeOut ->
    $("div#loading").fadeIn ->
      url = Routing.generate "b_conway_tracker_goal_view_stats",
        id: id
      $.get(url, (html) ->
        try
          if html.indexOf("login_form") > -1
            window.location = window.location
          else
            $("div#single-goal-stats-div").html html
            $("div#loading").fadeOut ->
              $("div#single-goal-stats-div").fadeIn()

        catch e
          alert "Error loading goal stats"
      ).fail ->
        alert "Error loading goal stats"
        $("div#loading").fadeOut ->
          $("div#single-goal-stats-div").fadeIn()

@displayGoalStats = (stats) ->
  if stats and stats?
    headerStyles =
      cursor: "pointer"
      height: "25px"

    $("div#single-goal-header").text("Goal: " + stats.goalWeight + " lbs by " + stats.endDate).css headerStyles
    $("div#single-goal-header").attr "onclick", ""
    $("div#single-goal-header").attr "onclick", "showGoalList();"
    $("span#single-goal_recent-weighin_weight").text stats.lastWeighInWeight.toString()  if stats.lastWeighInWeight
    $("span#single-goal_calculated-current-weight").text stats.estimatedCurrentWeight.toString()  if stats.estimatedCurrentWeight
    $("span#single-goal_relative-weighins").text stats.relativeWeighinsCount.toString()  if stats.relativeWeighinsCount
    $("span#single-goal_days-remaining").text stats.goalDaysRemaining.toString() + "/" + stats.goalLength.toString()  if stats.goalLength and stats.goalDaysRemaining
    $("span#single-goal_current-calorie-deficit").text stats.currentCalorieDeficit.toString()  if stats.currentCalorieDeficit
    $("span#single-goal_total-deficit-remaining").text stats.totalCaloriesToLoseRemaining.toString()  if stats.totalCaloriesToLoseRemaining
    $("span#single-goal_total-average-deficit-needed").text stats.totalAverageDeficitNeeded.toString()  if stats.totalAverageDeficitNeeded
    $("span#single-goal_average-deficit-needed").text stats.currentAverageDeficitNeeded.toString()  if stats.currentAverageDeficitNeeded
    $("span#single-goal_average-deficit").text stats.currentAverageDeficit.toString()  if stats.currentAverageDeficit
    if stats.deficitProgress
      $("div#single-goal_progress-text").text stats.deficitProgress.toString() + "%"
      $("div#single-goal_progress-bar_bar").css width: stats.deficitProgress.toString() + "px"
    $("span#single-goal_start-weight").text stats.startWeight.toString()  if stats.startWeight
    $("span#single-goal_end-weight").text stats.goalWeight.toString()  if stats.goalWeight
    $("span#single-goal_start-date").text stats.startDate.toString()  if stats.startDate
    $("span#single-goal_end-date").text stats.endDate.toString()  if stats.endDate
    $("span#single-goal_most-recent-weighin-calorie-deficit").text stats.deficitSinceLastWeighin.toString()  if stats.deficitSinceLastWeighin

@showGoalSettings = (id) ->
  url = Routing.generate "b_conway_tracker_goal_edit",
    id: id
  $("div#edit_goal").fadeOut ->
    $("div#loading").fadeIn ->
      $.get(url, (html) ->
        try
          $("div#edit_goal").html html
          $("div#loading").fadeOut ->
            $("div#edit_goal").fadeIn()

          $("input.date_picker").datepicker dateFormat: "yy-mm-dd"
        catch e
          alert "Error loading goal settings"
      ).fail ->
        alert "Error loading goal settings"

@createNewGoal = ->
  url = Routing.generate("b_conway_tracker_goal_create")
  $("div#edit_goal").fadeOut ->
    $("div#loading").fadeIn ->
      $.get(url, (html) ->
        try
          $("div#edit_goal").html html
          $("div#loading").fadeOut ->
            $("div#edit_goal").fadeIn()

          $("input.date_picker").datepicker dateFormat: "yy-mm-dd"
        catch e
          alert "Error creating new goal"
      ).fail ->
        alert "Error creating new goal"