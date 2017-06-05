# PHP Object Builder
https://github.com/lgse/php-object-builder

PHP 7+ Recursive Object Builder

## About

**PHP Object Builder** will validate instantiate recursively all of your complex objects by passing in an array of values.

## Features
- Validates argument types
- Passes arguments to constructors in the right order
- Instantiates parameter classes
- Lightning Fast
- Easy to use

## Install
```sh
composer require php-object-builder
```

## Usage
```php
use PHPOB\Model;
use PHPOB\ObjectBuilder;

/**
 * Example Object Class To Instantiate
 * Extending our `Model` class will add the `getInstance` static method
 * to your object so you don't have to create an object builder every
 * time you want to instantiate a class.
 */
class Customer extends Model {
    public function __construct(
        int $id,
        string $name,
        Address $address
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
    }
    ...
}
class Address {
    public function __construct(
        string $street,
        string $city,
        string $state,
        int $zip
    ) {
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
    }
    ...
}

/**
 * Example instantiation using the `getInstance` static method
 */
$customer = Customer::getInstance([
    'id' => '0e2c0f21-2c46-4cf9-ad7e-2beeadb9282b',
    'name' => 'Microsoft',
    'address' => [
        'street' => '1 Microsoft Way',
        'city' => 'Redmond',
        'state' => 'WA',
        'zip' => 98052,
    ]
]);

/**
 * Example instantiation using the object builder
 * Note: You can pass in instantiated parameters that will be automatically passed through
 * to the object's constructor
 */
 $builder = new ObjectBuilder(Customer::class);
 $customer = $builder->getObject([
    'id' => '0e2c0f21-2c46-4cf9-ad7e-2beeadb9282b',
    'name' => 'Microsoft',
    'address' => new Address([
        'street' => '1 Microsoft Way',
        'city' => 'Redmond',
        'state' => 'WA',
        'zip' => 98052,
    ])
 ]);
``` 
## API
### Available methods for Model:

- `static getInstance(array|object $arguments)` Automatically creates a builder object and returns an instance of extended class.

### Available methods for ObjectBuilder:

- `__construct(string $className)` Instantiates a builder object for provided class name.
- `getObject(array $arguments)` Returns an instantiated object using an array of arguments.