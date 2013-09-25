@behavior @javascript
Feature: User needs to be able to manage entries

  Background:
    Given The database is empty
      And I have a user "TestUser" with password "secret" and a height of "67"
      And I am logged in with password "secret"
     Then I should be on the homepage

  Scenario: User views unfiltered entry history
    Given I have 47 entries
     When I assign the entries to user "TestUser"
      And I save the entries to the database
      And I follow "View History"
     Then I should see "Weight & Deficit Entries"
      And I should see 11 "table.entries_table tr" elements
      And I should not see "<< First"
      And I should not see "< Prev"
      And I should see "Next >"
      And I should see "Last >>"
     When I press "Next >"
      And I wait for the response
     Then I should see "<< First"
      And I should see "< Prev"
      And I should see "Next >"
      And I should see "Last >>"
     When I press "Last >>"
      And I wait for the response
     Then I should see "<< First"
      And I should see "< Prev"
      And I should not see "Next >"
      And I should not see "Last >>"
      And I should see 5 "table.entries_table tr" elements

  Scenario: User views filtered entry history
    Given I have 47 entries
     When I assign the entries to user "TestUser"
      And I save the entries to the database
      And I follow "View History"
     Then I should see "Weight & Deficit Entries"
      And I should see 11 "table.entries_table tr" elements
      And I should see "Currently displaying page 1 of 3"
     When I check "show_all_entries"
      And I wait for the response
     Then I should see "Currently displaying page 1 of 5"
      And I should not see "<< First"
      And I should not see "< Prev"
      And I should see "Next >"
      And I should see "Last >>"
     When I press "Last >>"
      And I wait for the response
     Then I should see "Currently displaying page 5 of 5"
      And I should see "<< First"
      And I should see "< Prev"
      And I should not see "Next >"
      And I should not see "Last >>"
      And I should see 8 "table.entries_table tr" elements
     When I filter entries from the next 7 days
      And I press "Search"
      And I wait for the response
     Then I should not see "<< First"
      And I should not see "< Prev"
      And I should not see "Next >"
      And I should not see "Last >>"
      And I should see 8 "table.entries_table tr" elements
     When I uncheck "show_all_entries"
      And I wait for the response
     Then I should see 5 "table.entries_table tr" elements
     When I press "Clear search"
      And I wait for the response
     Then I should see 11 "table.entries_table tr" elements


  Scenario: User adds an entry
    Given I should see "Create entry"
     When I fill in "entry_entry_date" with "2001-01-01"
      And I fill in "entry_deficit" with "1000"
      And I press "Create entry"
     Then I should see "Entry created successfully"
     When I follow "View History"
     Then I should see 1 "table.entries_table tr" element
     When I check "show_all_entries"
      And I wait for the response
     Then I should see 2 "table.entries_table tr" elements

  Scenario: User edits an entry
    Given I have an entry with a deficit of "1000" a weight of "165"
     When I assign the entries to user "TestUser"
      And I save the entries to the database
      And I follow "View History"
     Then I should see "Weight & Deficit Entries"
      And I should see 2 "table.entries_table tr" elements
     When I click on the "table.entries_table tr:nth-of-type(2) td:nth-of-type(4) a" element
     Then I should see "Edit Entry"
     When I fill in "entry_weight" with "137"
      And I fill in "entry_deficit" with "566"
      And I press "Update entry"
     Then I should see "Weight & Deficit Entries"
      And I should see "Entry updated successfully"
      And I should see "137"
      And I should see "566"

  Scenario: User deletes an entry
    Given I have 1 entry
     When I assign the entries to user "TestUser"
      And I save the entries to the database
      And I follow "View History"
     Then I should see "Weight & Deficit Entries"
      And I should see 2 "table.entries_table tr" elements
     When I click on the "table.entries_table tr:nth-of-type(2) td:nth-of-type(5) a" element
      And I confirm the popup
      And I wait for the response
     Then I should see 1 "table.entries_table tr" element