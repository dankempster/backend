Feature: Magento 1 CSV module

  Background:
    Given I am Authenticated as "test@ergonode.com"
    And I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"

  Scenario: Create text attribute
    When I send a "POST" request to "/api/v1/en_GB/attributes" with body:
      """
      {
          "code": "IMPORT_M1_TEST_@@random_code@@",
          "type": "TEXT",
          "scope": "local",
          "groups": [],
          "parameters": []
      }
      """
    Then the response status code should be 201
    And store response param "id" as "attribute_id"

  Scenario: Create Magento 1 CSV Source with default attribute
    When I send a POST request to "/api/v1/en_GB/sources" with body:
      """
      {
        "type": "magento-1-csv",
        "name": "default attribute"
      }
      """
    Then the response status code should be 201


  Scenario: Create Magento 1 CSV Source
    When I send a POST request to "/api/v1/en_GB/sources" with body:
      """
      {
        "type": "magento-1-csv",
        "name": "name",
        "host": "http://test.host",
        "import" : [
           "templates",
           "attributes",
           "categories",
           "products"
        ],
        "mapping": {
          "default_language": "en_GB",
          "languages": [
              {
                 "store":"test",
                 "language":"en_GB"
              }
          ]
        },
        "attributes": [
          {
            "code": "name",
            "attribute": "@attribute_id@"
          }
        ]
      }
      """
    Then the response status code should be 201
    And store response param "id" as "source_id"

  Scenario: Create Magento 1 CSV Source with not exists attribute id value
    When I send a POST request to "/api/v1/en_GB/sources" with body:
     """
      {
        "type": "magento-1-csv",
        "name": "name",
        "host": "http://test.host",
        "import" : [
           "templates",
           "attributes",
           "categories",
           "products"
        ],
        "mapping": {
          "default_language": "en_GB",
          "languages": [
            {
               "store":"test",
               "language":"en_GB"
            }
          ]
        },
        "attributes": [
           {
             "code": "name-3",
             "attribute": "ad089ed5-92e0-4c1a-875c-430cde785e3f"
           }
        ]
      }
      """
    Then the response status code should be 400

  Scenario: Create Magento 1 CSV Source with empty body
    When I send a POST request to "/api/v1/en_GB/sources" with body:
     """
      {
      }
      """
    Then the response status code should be 400

  Scenario: Get Magento 1 CSV Source
    When I send a GET request to "/api/v1/en_GB/sources/@source_id@"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | type                     | magento-1-csv    |
      | name                     | name             |
      | host                     | http://test.host |
      | mapping.default_language | en_GB            |


  Scenario: Update Magento 1 CSV Source
    When I send a PUT request to "/api/v1/en_GB/sources/@source_id@" with body:
      """
      {
        "name": "name2",
        "host": "http://test.host",
        "import" : [
           "templates",
           "attributes",
           "categories",
           "products"
        ],
        "mapping": {
          "default_language": "en_GB",
          "languages": []
        }
      }
      """
    Then the response status code should be 201
    And store response param "id" as "source_id"

  Scenario: Update Magento 1 CSV Source with null attribute
    When I send a PUT request to "/api/v1/en_GB/sources/@source_id@" with body:
      """
      {
        "name": "name2",
        "host": "http://test.host",
        "import" : [
           "templates",
           "attributes",
           "categories",
           "products"
        ],
        "mapping": {
          "default_language": "en_GB",
          "languages": []
        },
        "attributes": [
           {
             "code": "name-2",
             "attribute": null
           }
        ]
      }
      """
    Then the response status code should be 400

  Scenario: Update Magento 1 CSV Source with note exists attribute
    When I send a PUT request to "/api/v1/en_GB/sources/@source_id@" with body:
      """
      {
        "name": "name2",
        "host": "http://test.host",
        "import" : [
           "templates",
           "attributes",
           "categories",
           "products"
        ],
        "mapping": {
          "default_language": "en_GB",
          "languages": []
        },
        "attributes": [
           {
             "code": "name-2",
             "attribute": "5bbd9479-e8d4-4a7c-8771-be29248df7d6"
           }
        ]
      }
      """
    Then the response status code should be 400

  Scenario: Get Magento 1 CSV Source after update
    When I send a GET request to "/api/v1/en_GB/sources/@source_id@"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | type                     | magento-1-csv    |
      | name                     | name2            |
      | host                     | http://test.host |
      | mapping.default_language | en_GB            |

  Scenario: Upload magento 1 test import file
    When I send a POST request to "/api/v1/en_GB/sources/@source_id@/upload" with params:
      | key    | value                 |
      | upload | @magento-1-import.csv |
    Then the response status code should be 201
    And the JSON node "id" should exist
    And store response param "id" as "import_id"

  Scenario: Upload magento 1 test import file with corupted csv file
    When I send a POST request to "/api/v1/en_GB/sources/@source_id@/upload" with params:
      | key    | value                 |
      | upload | @magento-1-import-error.csv |
    Then the response status code should be 201
    And the JSON node "id" should exist
    And store response param "id" as "error_import_id"

  Scenario: Get source imports grid
    When I send a GET request to "/api/v1/en_GB/sources/@source_id@/imports"
    Then the response status code should be 200

  Scenario: Get source import
    When I send a GET request to "/api/v1/en_GB/sources/@source_id@/imports/@import_id@"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | id        | @import_id@ |
      | source_id | @source_id@ |
      | status    | Ended       |
    And the JSON node "errors" should not be null
    And the JSON node "records" should not be null
    And the JSON node "created_at" should not be null
    And the JSON node "updated_at" should not be null
    And the JSON node "started_at" should not be null
    And the JSON node "ended_at" should exist

  Scenario: Get source error import
    When I send a GET request to "/api/v1/en_GB/sources/@source_id@/imports/@error_import_id@"
    Then the response status code should be 200
    And the JSON nodes should be equal to:
      | id        | @error_import_id@ |
      | source_id | @source_id@ |
      | status    | Stopped      |
    And the JSON node "errors" should not be null
    And the JSON node "records" should not be null
    And the JSON node "created_at" should not be null
    And the JSON node "updated_at" should not be null
    And the JSON node "started_at" should not be null
    And the JSON node "ended_at" should exist

  Scenario: Get error import grid
    When I send a GET request to "/api/v1/en_GB/sources/@source_id@/imports/@import_id@/errors"
    Then the response status code should be 200
