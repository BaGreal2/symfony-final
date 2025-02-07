Feature: Admin Dashboard

  Scenario: Access admin dashboard without authentication
    When I send a GET request to "/api/admin/dashboard"
    Then the response status code should be 401
    And the response should contain "Unauthorized"

