Feature: User Management

  Background:
    Given I am authenticated as "admin@example.com" with password "admin"

  Scenario: Create a new user with valid data
    When I send a POST request to "/api/users" with:
      """
      {
        "email": "testuser1@example.com",
        "password": "password"
      }
      """
    Then the response status code should be 201
    And the response "data.email" should equal "testuser1@example.com"

