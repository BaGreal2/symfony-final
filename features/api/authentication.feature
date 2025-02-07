Feature: User Authentication
  Background:
    Given I set the "Content-Type" header to "application/json"

  Scenario: Successful user registration
    When I send a POST request to "/api/register" with:
      """
      {
        "email": "testuser@example.com",
        "password": "testpassword"
      }
      """
    Then the response status code should be 201
    And the response should be JSON

  Scenario: Login with valid credentials
    When I send a POST request to "/api/login" with:
      """
      {
        "email": "testuser@example.com",
        "password": "testpassword"
      }
      """
    Then the response status code should be 200
    And the response should contain a valid JWT token

  Scenario: Register with duplicate email
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
