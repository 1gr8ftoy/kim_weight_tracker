@user
Feature: User stats
  User stats are calculated based
  on goals and entries in the database

  Background:
    Given The database is empty
    And I have a user "TestUser" with password "secret" and a height of "67"

  Scenario: A user has a goal and three entries
    Given I have a goal "155" lbs in "30" days
      And I have an entry with a deficit of "1000" a weight of "170" with a negative day offset "3"
      And I have an entry with a deficit of "800" a weight of "165" with a negative day offset "2"
      And I have an entry with a deficit of "900" a weight of "0" with a negative day offset "1"
     When I assign the goal to user "TestUser"
      And I assign the entries to user "TestUser"
      And I save the goal to the database
      And I save the entries to the database
     Then I should have valid user stats for user "TestUser" using a goal of "155" lbs in "30" days and an entry with a deficit of "1000,800,900" and a weight of "170,165,0"