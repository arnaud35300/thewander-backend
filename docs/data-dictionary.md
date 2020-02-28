# Data Dictionary

## Celestial Body (`celestial_body`)

|    Field    |    Type     |                  Specification                  |                    Description                    |
| :---------: | :---------: | :---------------------------------------------: | :-----------------------------------------------: |
|     id      |     INT     | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |              The celestial body's ID              |
|    name     | VARCHAR(50) |                    NOT NULL                     |             The celestial body's name             |
|    slug     | VARCHAR(50) |                    NOT NULL                     |             The celestial body's slug             |
|    y_position     |   INT    |                    NULL                     |           The celestial body's y position            |
|    x_position     |   INT    |                    NULL                     |           The celestial body's x position            |
|    user     |   ENTITY    |                    NOT NULL                     |           The celestial body's creator            |
|   picture   | VARCHAR(50) |                      NULL                       |           The celestial body's picture            |
|  nb_stars   |     INT     |                      NULL                       | The number of people who liked the celestial body |
| description |   TEXT()    |                      NULL                       |         The celestial body's description          |
| created_at  |  TIMESTAMP  |                    NOT NULL                     |        The celestial body's creation date         |
| updated_at  |  TIMESTAMP  |                    NOT NULL                     |         The celestial body's last update          |

## User (`user`)

|   Field    |     Type     |                  Specification                  |            Description             |
| :--------: | :----------: | :---------------------------------------------: | :--------------------------------: |
|     id     |     INT      | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |           The user's ID            |
|  nickname  | VARCHAR(30)  |                    NOT NULL                     |        The user's nickname         |
|    slug    | VARCHAR(30)  |                    NOT NULL                     |          The user's slug           |
|   email    | VARCHAR(100) |                    NOT NULL                     |      The user's email address      |
|  password  | VARCHAR(30)  |                    NOT NULL                     |        The user's password         |
|    role    |    ENTITY    |                    NOT NULL                     |          The user's role           |
|   avatar   | VARCHAR(50)  |                      NULL                       |         The user's avatar          |
|    rank    |    ENTITY    |                    NOT NULL                     |          The user's rank           |
| firstname  | VARCHAR(50)  |                      NULL                       |        The user's firstname        |
|  birthday  |   DATETIME   |                      NULL                       |       The user's birth date        |
|    bio     |     TEXT     |                      NULL                       |       The user's description       |
|   status   |   TINYINT    |                    NOT NULL                     |         The user's status          |
| created_at |  TIMESTAMP   |                    NOT NULL                     | The user's account's creation date |
| updated_at |  TIMESTAMP   |                    NOT NULL                     |  The user's profile's last update  |

## Comment (`comment`)

|     Field      |   Type    |                  Specification                  |             Description             |
| :------------: | :-------: | :---------------------------------------------: | :---------------------------------: |
|       id       |    INT    | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |          The comment's ID           |
|      body      |   TEXT    |                    NOT NULL                     |        The comment's message        |
|      user      |  ENTITY   |                    NOT NULL                     |        The comment's author         |
| celestial_body |  ENTITY   |                    NOT NULL                     | The comment's celestial body target |
|   created_at   | TIMESTAMP |                    NOT NULL                     |     The comment's creation date     |
|   updated_at   | TIMESTAMP |                    NOT NULL                     |      The comment's last update      |

## Role (`role`)

|   Field    |    Type     |                  Specification                  |       Description        |
| :--------: | :---------: | :---------------------------------------------: | :----------------------: |
|     id     |     INT     | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |      The role's ID       |
|    name    | VARCHAR(20) |                    NOT NULL                     |     The role's name      |
| created_at |  TIMESTAMP  |                    NOT NULL                     | The role's creation date |
| updated_at |  TIMESTAMP  |                    NOT NULL                     |  The role's last update  |

## Rank (`rank`)

|   Field    |    Type     |                  Specification                  |       Description        |
| :--------: | :---------: | :---------------------------------------------: | :----------------------: |
|     id     |     INT     | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |      The rank's ID       |
|    name    | VARCHAR(30) |                    NOT NULL                     |     The rank's name      |
|   badge    | VARCHAR(50) |                      NULL                       | The rank's badge (logo)  |
| created_at |  TIMESTAMP  |                    NOT NULL                     | The rank's creation date |
| updated_at |  TIMESTAMP  |                    NOT NULL                     |  The rank's last update  |

## Property (`type`)

|   Field    |     Type     |                  Specification                  |         Description          |
| :--------: | :----------: | :---------------------------------------------: | :--------------------------: |
|     id     |     INT      | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |      The property's ID       |
|    name    | VARCHAR(100) |                    NOT NULL                     |     The property's name      |
|    unit    |     INT      |                      NULL                       |     The property's unit      |
|   value    | VARCHAR(20)  |                      NULL                       |     The property's value     |
| created_at |  TIMESTAMP   |                    NOT NULL                     | The property's creation date |
| updated_at |  TIMESTAMP   |                    NOT NULL                     |  The property's last update  |

## CelestialBodyProperty (`celestial_body_property`)

|       Field       | Type  | Specification |       Description       |
| :---------------: | :---: | :-----------: | :---------------------: |
|    property_id    |  INT  |   NOT NULL    |    The property's ID    |
| celestial_body_id |  INT  |   NOT NULL    | The celestial body's ID |
