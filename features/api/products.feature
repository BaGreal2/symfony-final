Feature: Product Management
  Background:
    Given I am authenticated as "testuser@example.com" with password "testpassword"

  Scenario: List products
    When I send a GET request to "/api/products"
    Then the response status code should be 200
    And the response should contain a "data" array
