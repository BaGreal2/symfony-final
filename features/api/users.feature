Feature: User Management
  Background:
    Given I am authenticated as "admin@example.com" with password "admin"

  Scenario: Create a new user
    When I send a POST request to "/api/users" with:
      """
      {
        "email": "newuser@example.com",
        "password": "password"
      }
      """
    Then the response status code should be 201
    And the response "data.email" should equal "newuser@example.com"

  Scenario: Get non-existent user
    When I send a GET request to "/api/users/999"
    Then the response status code should be 404
    And the response should contain "User not found"
