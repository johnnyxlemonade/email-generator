# Email Generator

![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)
![License](https://img.shields.io/badge/license-MIT-green)

Email Generator is a PHP library for generating emails using the [Twig](https://twig.symfony.com/) templating system. 
This library allows easy template handling and provides a flexible way to integrate email generation into your project.

## Basic Functionality

Email Generator provides an easy-to-use PHP library that leverages the power of the [Twig templating engine](https://twig.symfony.com/) to generate dynamic emails. The library is highly flexible and allows you to define custom templates for your emails, inject data into those templates, and render them as HTML or plain text emails. Here's how it works:

1. **Template Rendering**: The core feature is the ability to create email templates using Twig. These templates can contain placeholders (variables) that will be replaced by dynamic content at runtime.
2. **Data Injection**: You can pass data (such as user information, order details, etc.) into the templates. The library supports various data types, including arrays, objects, and scalars, allowing you to use your own application logic to populate the email content.
3. **Multi-Language Support**: The library supports translations out of the box, allowing you to generate emails in different languages by swapping dictionaries based on the user's preferred language.
4. **Flexible Email Content**: You can customize the content, including using inline CSS and HTML, embedding dynamic variables, or using more complex email layouts. You can also include attachments or images if necessary.
5. **Seamless Integration**: It is designed to integrate seamlessly with any PHP project. The library can be used with frameworks like Laravel, Symfony, or as a standalone tool in any PHP application.
6. **Testable**: With the built-in unit tests, you can easily test the behavior of your templates and email generation logic. This ensures that emails are generated correctly under different conditions and with various data inputs.

---


### ContainerBuilder Class Overview

The `ContainerBuilder` class is responsible for managing and lazy-loading services used in the `EmailGenerator` module. It follows a Dependency Injection (DI) pattern and ensures that services are only instantiated when needed, optimizing performance and resource usage.

#### Design Patterns Used

1. **Dependency Injection (DI)**:
    - The class uses **Dependency Injection** to receive its dependencies (such as `LoggerInterface`, `TemplateRenderer`, `Translator`) via the constructor. This pattern helps to decouple components, making the class more flexible and easier to test because it doesn't contain hard-coded dependencies. Dependencies are injected externally, usually by a dependency container.

2. **Lazy Loading**:
    - The class implements **Lazy Loading**, where services are not instantiated until they are requested for the first time. This helps to improve performance by avoiding unnecessary instantiation of services that might not be used during the application’s lifecycle. The lazy-loading is implemented via the `getOrCreateService` method, which checks if a service is already initialized and creates it only if necessary.

3. **Factory Pattern**:
    - The **Factory Pattern** is used in the service getter methods (e.g., `getProductCollectionService`, `getFormItemCollectionService`). A `ServiceFactoryManager` is responsible for creating instances of these services. This centralizes the object creation logic, which makes it easier to modify service instantiation and provides flexibility in how services are created.

4. **Singleton Pattern (Partial)**:
    - The class implements a **partial Singleton pattern**, where each service is created only once and reused throughout the lifetime of the application. Although not a traditional singleton, the services are cached after the first instantiation and reused whenever needed, reducing redundant object creation.

#### Class Structure

The `ContainerBuilder` class is organized as follows:

- **Private Properties**: The class has private properties for each service (e.g., `FormItemCollectionService`, `ProductCollectionService`). These properties are initialized to `null` and are lazily loaded when accessed for the first time.

- **Constructor**: The constructor takes essential services as dependencies (e.g., `logger`, `translator`, `templateRenderer`, etc.). These dependencies are stored in protected properties and are provided to other methods for email generation.

- **`getOrCreateService` Method**: This private method checks whether a service has already been initialized. If not, it creates the service using a factory function and logs the action. This is the key part of the lazy loading implementation.

- **Getter Methods**: For each service, there is a public getter method that allows access to the respective service. Each getter calls the `getOrCreateService` method to ensure the service is lazily instantiated if it hasn't been created yet.

#### Benefits of This Approach

- **Modularity**: Each service is responsible for a single functionality, making the class `ContainerBuilder` highly modular. If a service’s implementation needs to change, it can be done without affecting other parts of the code.

- **Testability**: With Dependency Injection, the class is highly testable. Dependencies can be mocked, making it possible to test the class's behavior without needing to interact with real services.

- **Flexibility and Extensibility**: The class is designed to be flexible and easily extendable. If new services are added in the future, they can be included by simply adding them to the constructor and providing the corresponding getter methods.

#### Summary

The `ContainerBuilder` class is a good example of using several important design patterns, such as **Dependency Injection**, **Lazy Loading**, and **Factory Pattern**, to manage and instantiate services. It helps improve the performance, modularity, and testability of the application, and provides a clean and efficient way to manage the dependencies needed for email generation.



---

## Requirements

- PHP 8.1 or newer
- Composer

---

## Installation

To install the library, use Composer:

```bash
composer require lemonade/email-generator
```

---

## Usage

Examples for different scenarios can be found in the [demo/](./demo) directory.

---

## Folders and Structure

- `src/` - Contains the main source files of the library.
- `tests/` - Contains unit tests for the library.
- `demo/` - Contains example implementations for various use cases.

---

## Testing

To run the tests, use the following command:


```bash
./vendor/bin/phpunit vendor/lemonade/email-generator/tests
```

If you have added PHPUnit to your dependencies, the tests will be executed using the `phpunit.xml` configuration.

---

## Contributing

We welcome contributions to this project! To contribute, please follow these steps:

1. Fork this repository.
2. Create a new branch for your changes.
3. Submit your changes via a pull request.

---

## Licence

This project is licensed under the MIT License. For more details, see the [LICENSE](./LICENSE) file.

---

## Contact

For more information or to report an issue, visit the [GitHub repozitář](https://github.com/johnnyxlemonade/email-generator).