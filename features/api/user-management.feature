Feature: User Management

  Background:
    Given I am authenticated as "admin@example.com" with password "admin"

  Scenario: Create a new user with invalid data
    When I send a POST request to "/api/users" with:
      """
      {
        "email": "invalidmail",
        "password": ""
      }
      """
    Then the response status code should be 500
    And the response should contain "Validation failed"

