@goal
Feature: User Goal Relationship
  In order to track and calculate
  goals and goal progress
  I need a working relationship

  Background:
    Given The database is empty
    And I have a user "TestUser" with password "secret" and a height of "67"

    Scenario: A user has a goal
      Given I have a goal "155" lbs in "30" days
       When I assign the goal to user "TestUser"
        And I save the goal to the database
       Then I should find goal "155" lbs in "30" days in user "TestUser" goals

    Scenario: A user has 10 goals
      Given I have "10" goals
       When I assign the goals to user "TestUser"
        And I save the goals to the database
       Then I should find that user "TestUser" has "10" goals