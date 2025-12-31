<?php

use Knuckles\Scribe\Extracting\Strategies;

return [
    /*
     * The HTML <title> for the generated documentation. If this is empty, Scribe will infer it from config('app.name').
     */
    'title' => 'Task Management API Documentation',

    /*
     * A short description of your API.
     */
    'description' => 'RESTful API for managing tasks with role-based access control. This API provides endpoints for user authentication, task management, and user administration.',

    /*
     * The base URL displayed in the docs. If this is empty, Scribe will use the value of config('app.url').
     */
    'base_url' => env('APP_URL', 'http://localhost:8000'),

    /*
     * Routes to include in the documentation.
     */
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/*'],
                'domains' => ['*'],
            ],
            'include' => [],
            'exclude' => [],
            'apply' => [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'response_calls' => [
                    'methods' => ['GET'],
                    'config' => [
                        'app.env' => 'documentation',
                    ],
                    'queryParams' => [],
                    'bodyParams' => [],
                    'fileParams' => [],
                    'cookies' => [],
                ],
            ],
        ],
    ],

    /*
     * The type of documentation output to generate.
     */
    'type' => 'static',

    /*
     * Settings for the 'static' documentation type.
     */
    'static' => [
        'output_path' => 'public/docs',
    ],

    /*
     * Settings for the 'laravel' documentation type.
     */
    'laravel' => [
        'add_routes' => true,
        'docs_url' => '/docs',
        'assets_directory' => null,
        'middleware' => [],
    ],

    /*
     * How is your API authenticated?
     */
    'auth' => [
        'enabled' => true,
        'default' => false,
        'in' => 'bearer',
        'name' => 'Authorization',
        'use_value' => env('SCRIBE_AUTH_KEY', 'your-token-here'),
        'placeholder' => '{ACCESS_TOKEN}',
        'extra_info' => 'You can obtain a token by calling the login or register endpoint. Include the token in the Authorization header as: Bearer {ACCESS_TOKEN}',
    ],

    /*
     * Text to place in the Intro section.
     */
    'intro_text' => <<<INTRO
# Introduction

Welcome to the Task Management API documentation. This API allows you to manage tasks with role-based access control.

## Features

- **User Authentication**: Register, login, and logout with token-based authentication
- **Task Management**: Create, read, update, and delete tasks
- **Role-Based Access Control**: Admin and Member roles with different permissions
- **Task Status Tracking**: Track tasks as pending, in_progress, or completed

## Roles

| Role | Permissions |
|------|-------------|
| Admin | Can manage all tasks and users |
| Member | Can create tasks, view all tasks, but only update/delete own tasks |

## Test Credentials

After running database seeders, you can use these credentials:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Member | member@example.com | password |
INTRO,

    /*
     * Example code languages to generate.
     */
    'example_languages' => [
        'bash',
        'javascript',
        'php',
    ],

    /*
     * Generate a Postman collection in addition to the HTML docs.
     */
    'postman' => [
        'enabled' => true,
        'overrides' => [
            'info.version' => '1.0.0',
        ],
    ],

    /*
     * Generate an OpenAPI spec (v3.0.1) in addition to the HTML docs.
     */
    'openapi' => [
        'enabled' => true,
        'overrides' => [
            'info.version' => '1.0.0',
        ],
    ],

    /*
     * Custom logo path.
     */
    'logo' => false,

    /*
     * The router your API uses.
     */
    'default_group' => 'Endpoints',

    /*
     * Strategies Scribe will use to extract information about your routes.
     */
    'strategies' => [
        'metadata' => [
            Strategies\Metadata\GetFromDocBlocks::class,
            Strategies\Metadata\GetFromMetadataAttributes::class,
        ],
        'urlParameters' => [
            Strategies\UrlParameters\GetFromLaravelAPI::class,
            Strategies\UrlParameters\GetFromUrlParamAttribute::class,
            Strategies\UrlParameters\GetFromUrlParamTag::class,
        ],
        'queryParameters' => [
            Strategies\QueryParameters\GetFromFormRequest::class,
            Strategies\QueryParameters\GetFromInlineValidator::class,
            Strategies\QueryParameters\GetFromQueryParamAttribute::class,
            Strategies\QueryParameters\GetFromQueryParamTag::class,
        ],
        'headers' => [
            Strategies\Headers\GetFromRouteRules::class,
            Strategies\Headers\GetFromHeaderAttribute::class,
            Strategies\Headers\GetFromHeaderTag::class,
        ],
        'bodyParameters' => [
            Strategies\BodyParameters\GetFromFormRequest::class,
            Strategies\BodyParameters\GetFromInlineValidator::class,
            Strategies\BodyParameters\GetFromBodyParamAttribute::class,
            Strategies\BodyParameters\GetFromBodyParamTag::class,
        ],
        'responses' => [
            Strategies\Responses\UseResponseAttributes::class,
            Strategies\Responses\UseTransformerTags::class,
            Strategies\Responses\UseApiResourceTags::class,
            Strategies\Responses\UseResponseTag::class,
            Strategies\Responses\UseResponseFileTag::class,
            Strategies\Responses\ResponseCalls::class,
        ],
        'responseFields' => [
            Strategies\ResponseFields\GetFromResponseFieldAttribute::class,
            Strategies\ResponseFields\GetFromResponseFieldTag::class,
        ],
    ],

    /*
     * For response calls.
     */
    'fractal' => [
        'serializer' => null,
    ],

    /*
     * Database connections to begin transactions on for response calls.
     */
    'database_connections_to_transact' => [config('database.default')],

    /*
     * If docs are in a different base path (for example, if hosted on a different domain).
     */
    'external' => [
        'html_attributes' => [],
    ],
];
