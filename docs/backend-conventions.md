# Code Conventions (Symfony)

## Basics

**Everything in english.**  
**Camel case syntax.**  
**An indentation of 4 spaces.**  

**Empty arrays will be written as `array()` while the others will keep the `[]` standards.**

**We will focus on using PHP functions, not raw values as in using `Response::HTTP_OK` instead of `200`.**

**Method chaining will be as following:**

```
$this
    ->getDoctrine()
    ->getManager()
;
```
```
$user
    ->setNickname()
    ->setEmail()
    ->setPassword()
    ->setAvatar()
    ...
;
```

## Type hinting

**We'll use type hinting as much as possible.**

## Annotations

**Plain documented doc blocks.**

```
// UserController.php

/**
 * Returns a user from the database.
 *
 * @param object $user An instance of the User entity.
 * @param int $id The user's ID.
 *
 * @return object
 *
 * @Route("/users", name="api_users_list", methods={"GET"})
 */
public function getOne(?User $user = null, $id): ?JsonResponse
{
    # Instructions...
}
```

## Services

- **Every service will be automatically declared as a methods' parameters, exception made of the services' that will only be used within conditions, loops and such processes.**

- **The services' representative variables will be named as shortly and relevant as possible :**
    - *EntityManagerInterface => $manager*
    - *SerializerInterface => $serializer*
    - *UserRepository => $userRepository*

## Entities

**The constructor will be used for the 'dynamic' properties of entities, such as a user's nickname, email, password, etc.**

```
// User.php

public function __construct($nickname, $email, $password, ...)
{
    $this->nickname = $nickname;
    $this->email = $email;
    $this->password = $password;
    ...
}
```

**The entities event prePersist() will be used on generic properties and object hinting instances, such as array collections, date times, etc.**

```
// User.php

/**
 * @ORM\PrePersist
 */
public function setRole()
{
    $this->role = new ArrayCollection();
    $this->createdAt = new \DateTime();
    $this->updatedAt = new \DateTime();
}
```

## Controllers

- **Exceptions and error cases will be treated in `if` conditions to respect the normal process logic.**

- **We will refactor the code as much as possible without denying the relevance of what needs to be specified.**

*Example:*

```
// StarController.php

public function getAll()
{
    $stars = $starRepository->findAll();

    return $this->json(
         $stars,
         Response::HTTP_OK,
         array(),
         ['groups' => 'stars']
    );
}
```
*Instead of:*

```
// StarController.php

public function getAll()
{
    return $this->json($starRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'stars']);
}
```

## Events

**Every non-Doctrine related event will be created in a separate class file (named according to the event goal), the said file being stored in an EventListener or EventSubscriber folder and declared within services.yaml.**

