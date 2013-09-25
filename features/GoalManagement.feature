@behavior @javascript
Feature: User needs to be able to manage goals

  Background:
    Given The database is empty
      And I have a user "TestUser" with password "secret" and a height of "67"
      And I am logged in with password "secret"
     Then I should be on the homepage

  Scenario: User views goals
    Given I have "2" goals
     When I assign the goals to user "TestUser"
      And I save the goals to the database
      And I follow "Manage Goals"
      And I wait until "div#entries_tables" is visible
     Then I should see 2 "ul#goals_list li.ui-menu-item" elements

  Scenario: User adds a goal
    Given I follow "Manage Goals"
     When I wait until "div#entries_tables" is visible
     Then I should see 0 "ul#goals_list li.ui-menu-item" elements
     When I press "Create a new goal"
      And I wait for the response
      And I fill in "form_startDate" with "2000-01-01"
      And I fill in "form_endDate" with "2000-01-01"
      And I fill in "form_startWeight" with "211"
      And I fill in "form_endWeight" with "111"
      And I press "Save goal"
     Then I should see "Goal created successfully"
      And I should see 1 "ul#goals_list li.ui-menu-item" elements

  Scenario: User edits a goal
    Given I have "2" goals
     When I assign the goals to user "TestUser"
      And I save the goals to the database
      And I follow "Manage Goals"
      And I wait until "div#entries_tables" is visible
     Then I should see 2 "ul#goals_list li.ui-menu-item" elements
     When I follow "ui-id-1"
      And I wait for the response
     Then I should see 4 "div#edit_goal div.field" elements
     When I fill in "form_startDate" with "2000-01-01"
      And I fill in "form_endDate" with "2001-01-01"
      And I fill in "form_startWeight" with "211"
      And I fill in "form_endWeight" with "111"
     Then I press "Update goal"
     When I wait for the response
     Then I should see "Goal updated successfully"
      And I should see "01/01/2001 - 111"

  Scenario: User deletes a goal
    Given I have "2" goals
     When I assign the goals to user "TestUser"
      And I save the goals to the database
      And I follow "Manage Goals"
      And I wait until "div#entries_tables" is visible
     Then I should see 2 "ul#goals_list li.ui-menu-item" elements
     When I follow "ui-id-1"
      And I wait for the response
     Then I should see 4 "div#edit_goal div.field" elements
     When I press "Delete goal"
      And I confirm the popup
      And I wait for the response
     Then I should see "Goal deleted successfully"
      And I should see 1 "ul#goals_list li.ui-menu-item" element

  Scenario: User views goal stats
    Given I have a goal "155" lbs in "30" days
     When I assign the goal to user "TestUser"
      And I save the goal to the database
      And I reload the page
      And I click on the "div#single-goal-header" element
      And I wait for the response
     Then I should see 1 "ul#goals_list li.ui-menu-item" element
     When I click on the "ul#goals_list li.ui-menu-item a" element
      And I wait for the response
     Then I should see "1 / 31"
      And I should see "273000"
