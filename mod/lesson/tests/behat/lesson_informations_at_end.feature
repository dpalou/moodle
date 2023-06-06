@mod @mod_lesson
Feature: In a lesson activity, if custom scoring is not enabled, student should see
  some informations at the end of lesson: questions answered, correct answers, grade, score

  Scenario: Informations at end of lesson if custom scoring not enabled
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name             | course | idnumber  | custom |
      | lesson     | Test lesson name | C1     | lesson1   | 0      |
    And the following "mod_lesson > pages" exist:
      | lesson           | qtype   | title                 | content             |
      | Test lesson name | content | First page name       | First page contents |
      | Test lesson name | numeric | Hardest question ever | 1 + 1?              |
    And the following "mod_lesson > answers" exist:
      | page                  | answer    | response         | jumpto    | score |
      | First page name       | Next page |                  | Next page | 0     |
      | Hardest question ever | 2         | Correct answer   | Next page | 1     |
      | Hardest question ever | 1         | Incorrect answer | This page | 0     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "First page contents"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Number of questions answered: 1"
    And I should see "Number of correct answers: 0"
    And I should see "Your score is 0 (out of 1)."
    And I should see "Your current grade is 0.0 out of 100"
