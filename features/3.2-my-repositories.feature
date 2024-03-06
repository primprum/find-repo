Feature: Get my list of repositories

  Scenario: I want to check the availability of my repository
    Given I am an authenticated user
    When I request a list of my repositories
    Then the results should include a repository named "get-issues"
