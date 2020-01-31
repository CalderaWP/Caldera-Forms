# Shared Functions

This directory provides functions at solve cross-concerns.

- Each file SHOULD export one function.

## Naming
- Use the camelcase name of the function.
-  Capitalize the first name of function if it is a constructor function.
    - If you call the function with the new keyword, then it's probably a constructor functions.
    - Josh likes constructor functions that encapsulated a chunk of UI that are provided all dependencies through the constructor.

