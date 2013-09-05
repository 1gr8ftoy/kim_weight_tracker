@behavior
Feature: User profile
  User needs to be able to
  modify their profile

  Background:
    Given The database is empty
      And I have a user "TestUser" with password "secret" and a height of "67"
      And I am logged in with password "secret"
     Then I should be on the homepage

    Scenario: User logs in
      Given I should see "Logout"

    Scenario: User logs out
      Given I follow "Logout"
       Then I should see "Login"

    Scenario: User unsuccessfully tries to change password
      Given I navigate to edit profile
       When I follow "Change password"
        And I fill in "fos_user_change_password_form_current_password" with "wrongpassword"
        And I fill in "fos_user_change_password_form_plainPassword_first" with "newPassword"
        And I fill in "fos_user_change_password_form_plainPassword_second" with "newPassword"
        And I press "Change password"
       Then I should see "This value should be the user current password."

    Scenario: User successfully changes password
      Given I navigate to edit profile
       When I follow "Change password"
        And I fill in "fos_user_change_password_form_current_password" with "secret"
        And I fill in "fos_user_change_password_form_plainPassword_first" with "newPassword"
        And I fill in "fos_user_change_password_form_plainPassword_second" with "newPassword"
        And I press "Change password"
       Then I should see "Password changed successfully"

    Scenario: User changes height
      Given I navigate to edit profile
       When I fill in "fos_user_profile_form_current_password" with "secret"
        And I fill in "height" with "60"
        And I press "Update"
       Then I should see "Profile updated successfully"

    Scenario: User unsuccessfully tries to change username
      Given I have a user "TestUser2" with password "secret" and a height of "67"
        And I navigate to edit profile
       When I fill in "fos_user_profile_form_username" with "TestUser2"
        And I fill in "fos_user_profile_form_current_password" with "secret"
        And I press "Update"
       Then I should see "The username is already used"

    Scenario: User successfully changes username
      Given I navigate to edit profile
       When I fill in "fos_user_profile_form_username" with "TestUser2"
        And I fill in "fos_user_profile_form_current_password" with "secret"
        And I press "Update"
       Then I should see "Profile updated successfully"

    Scenario: User successfully changes email address
      Given I navigate to edit profile
       When I fill in "fos_user_profile_form_email" with "new_email@email.com"
        And I fill in "fos_user_profile_form_current_password" with "secret"
        And I press "Update"
       Then I should see "Profile updated successfully"
