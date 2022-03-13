<?php

declare(strict_types=1);

namespace Brd6\NotionSdkPhp\Constant;

class NotionErrorCodeConstant
{
    public const REQUEST_TIMEOUT = 'notionhq_client_request_timeout';
    public const RESPONSE_ERROR = 'notionhq_client_response_error';

    public const UNAUTHORIZED = 'unauthorized';
    public const RESTRICTED_RESOURCE = 'restricted_resource';
    public const OBJECT_NOT_FOUND = 'object_not_found';
    public const RATE_LIMITED = 'rate_limited';
    public const INVALID_JSON = 'invalid_json';
    public const INVALID_REQUEST_URL = 'invalid_request_url';
    public const INVALID_REQUEST = 'invalid_request';
    public const VALIDATION_ERROR = 'validation_error';
    public const MISSING_VERSION = 'missing_version';
    public const CONFLICT_ERROR = 'conflict_error';
    public const INTERNAL_SERVER_ERROR = 'internal_server_error';
    public const SERVICE_UNAVAILABLE = 'service_unavailable';
    public const DATABASE_CONNECTION_UNAVAILABLE = 'database_connection_unavailable';

    public const CLIENT_ERROR_CODES = [
        self::REQUEST_TIMEOUT,
        self::RESPONSE_ERROR,
    ];

    public const API_ERROR_CODES = [
        self::UNAUTHORIZED,
        self::RESTRICTED_RESOURCE,
        self::OBJECT_NOT_FOUND,
        self::RATE_LIMITED,
        self::INVALID_JSON,
        self::INVALID_REQUEST_URL,
        self::INVALID_REQUEST,
        self::VALIDATION_ERROR,
        self::MISSING_VERSION,
        self::CONFLICT_ERROR,
        self::INTERNAL_SERVER_ERROR,
        self::SERVICE_UNAVAILABLE,
        self::DATABASE_CONNECTION_UNAVAILABLE,
    ];
}
