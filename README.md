# Search Advanced

This plugin provides additional search features.

## Features

- adds search widget
- extends fulltext with wildcards
- adds counters to search menu items
- autocompletes users and groups
- direct filter menu for direct content search (or all)

## Hooks

This plugin provides the following hooks that you can build on to influence search results.

### Default Search Hooks

The following hooks are also present in the core Elgg Search plugin. 

`'search_types', 'get_types'`:
This hook allows you to register custom types for search.

`'search', '<type>'`:
This hook allows you to perform a search for a given (custom) type.

`'search', '<type>:<subtype>'`:
This hook allows you to perform a search for a given type/subtype combination.

### Search Advanced only hooks

`'search_params', 'search'`:
This hook allows you to control the search params that are used in performing all searches.
