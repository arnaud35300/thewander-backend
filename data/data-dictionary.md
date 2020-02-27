# Data Dictionary

## Star (`star`)

|    Field    |    Type     |                  Specification                  |               Description               |
| :---------: | :---------: | :---------------------------------------------: | :-------------------------------------: |
|     id      |     INT     | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |              The star's ID              |
|    name     | VARCHAR(50) |                    NOT NULL                     |             The star's name             |
|    slug     | VARCHAR(50) |                    NOT NULL                     |             The star's slug             |
|    user     |   ENTITY    |                    NOT NULL                     |           The star's creator            |
|    type     |   ENTITY    |                    NOT NULL                     |             The star's type             |
|   subtype   |   ENTITY    |                    NOT NULL                     |           The star's subtype            |
|   picture   | VARCHAR(50) |                      NULL                       |           The star's picture            |
|  nb_stars   |     INT     |                      NULL                       | The number of people who liked the star |
| description |   TEXT()    |                      NULL                       |         The star's description          |
| created_at  |  TIMESTAMP  |                    NOT NULL                     |        The star's creation date         |
| updated_at  |  TIMESTAMP  |                    NOT NULL                     |         The star's last update          |

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

|   Field    |   Type    |                  Specification                  |         Description         |
| :--------: | :-------: | :---------------------------------------------: | :-------------------------: |
|     id     |    INT    | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |      The comment's ID       |
|    body    |   TEXT    |                    NOT NULL                     |    The comment's message    |
|    user    |  ENTITY   |                    NOT NULL                     |    The comment's author     |
|    star    |  ENTITY   |                    NOT NULL                     |  The comment's star target  |
| created_at | TIMESTAMP |                    NOT NULL                     | The comment's creation date |
| updated_at | TIMESTAMP |                    NOT NULL                     |  The comment's last update  |

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

## Type (`type`)

|   Field    |     Type     |                  Specification                  |       Description        |
| :--------: | :----------: | :---------------------------------------------: | :----------------------: |
|     id     |     INT      | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |      The type's ID       |
|    name    | VARCHAR(100) |                    NOT NULL                     |     The type's name      |
| created_at |  TIMESTAMP   |                    NOT NULL                     | The type's creation date |
| updated_at |  TIMESTAMP   |                    NOT NULL                     |  The type's last update  |

## Subtype (`subtype`)

|   Field    |     Type     |                  Specification                  |            Description             |
| :--------: | :----------: | :---------------------------------------------: | :--------------------------------: |
|     id     |     INT      | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT |          The subtype's ID          |
|    name    | VARCHAR(255) |                    NOT NULL                     |         The subtype's name         |
|    type    |    ENTITY    |                    NOT NULL                     | The type which the subtype belongs |
| created_at |  TIMESTAMP   |                    NOT NULL                     |    The subtype's creation date     |
| updated_at |  TIMESTAMP   |                    NOT NULL                     |     The subtype's last update      |
