Feature: User Registration

  Scenario: Register a user with a duplicate email
    Given I have already registered with email "testduplicate@example.com"
    When I send a POST request to "/api/register" with:
      """
      {
        "email": "testduplicate@example.com",
        "password": "password"
      }
      """
    Then the response status code should be 400
    And the response should contain "Email already registered"

