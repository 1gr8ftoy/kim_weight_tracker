@entry
Feature: User Entry Relationship
  In order to track and calculate
  goals and goal progress
  I need a working relationship

  Background:
    Given The database is empty
    And I have a user "TestUser" with password "secret" and a height of "67"

    Scenario: User has an entry
      Given I have an entry with a deficit of "1000" a weight of "165"
       When I assign the entry to user "TestUser"
        And I save the entry to the database
       Then I should find that the user "TestUser" has an entry with a deficit of "1000" a weight of "165"

    Scenario: User has 10 entries
      Given I have 10 entries
       When I assign the entries to user "TestUser"
        And I save the entries to the database
       Then I should find that the user "TestUser" has "10" entries