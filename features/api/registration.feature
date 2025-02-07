Feature: User Registration

  Scenario: User registration with invalid email format
    When I send a POST request to "/api/register" with:
      """
      {
        "email": "invalidemail",
        "password": "testpassword"
      }
      """
    Then the response status code should be 400
    And the response should contain "Invalid email format"

