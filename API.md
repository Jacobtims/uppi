# API Documentation

## Authentication
All API endpoints are protected using Laravel Sanctum authentication. You need to include a Bearer token in the Authorization header:

```
Authorization: Bearer <your-token>
```

To obtain a token, use the `/api/app/token` endpoint.

## Endpoints

### Authentication
#### Generate API Token
```http
POST /api/app/token
```

Returns a new API token for authentication.

### Profile
#### Get User Profile
```http
GET /api/profile
```

Returns the authenticated user's profile information.

### Monitors

#### List Monitors
```http
GET /api/monitors
```

Returns a list of all monitors belonging to the authenticated user.

**Response:**
```json
[
  {
    "id": "string",
    "address": "string",
    "type": "enum",
    "is_enabled": "boolean",
    "status": "enum",
    "last_checked_at": "datetime",
    "consecutive_threshold": "integer",
    "created_at": "datetime",
    "updated_at": "datetime",
    "last_check": {
      "id": "string",
      "status": "enum",
      "checked_at": "datetime",
      "response_time": "integer",
      // ... other check details
    },
    "anomalies": [
      // Only includes active anomalies (not ended)
      {
        "id": "string",
        "started_at": "datetime",
        "ended_at": null,
        // ... other anomaly details
      }
    ]
  }
]
```

#### Get Monitor Details
```http
GET /api/monitors/{monitor}
```

Returns detailed information about a specific monitor.

**Response:**
```json
{
  "id": "string",
  "address": "string",
  "type": "enum",
  "is_enabled": "boolean",
  "status": "enum",
  "last_checked_at": "datetime",
  "consecutive_threshold": "integer",
  "created_at": "datetime",
  "updated_at": "datetime",
  "last_check": {
    "id": "string",
    "status": "enum",
    "checked_at": "datetime",
    "response_time": "integer"
  },
  "anomalies": [
    // Only includes active anomalies
  ],
  "checks": [
    // Latest 10 checks
    {
      "id": "string",
      "status": "enum",
      "checked_at": "datetime",
      "response_time": "integer"
    }
  ]
}
```

#### List Monitor Anomalies
```http
GET /api/monitors/{monitor}/anomalies
```

Returns a paginated list of anomalies for a specific monitor.

**Response:**
```json
{
  "data": [
    {
      "id": "string",
      "started_at": "datetime",
      "ended_at": "datetime|null",
      "checks": [
        {
          "id": "string",
          "status": "enum",
          "checked_at": "datetime",
          "response_time": "integer"
        }
      ]
    }
  ],
  "links": {
    "first": "string",
    "last": "string",
    "prev": "string|null",
    "next": "string|null"
  },
  "meta": {
    "current_page": "integer",
    "last_page": "integer",
    "per_page": 15,
    "total": "integer"
  }
}
```

#### Get Monitor Anomaly Details
```http
GET /api/monitors/{monitor}/anomalies/{anomaly}
```

Returns detailed information about a specific anomaly for a monitor.

**Response:**
```json
{
  "id": "string",
  "started_at": "datetime",
  "ended_at": "datetime|null",
  "checks": [
    {
      "id": "string",
      "status": "enum",
      "checked_at": "datetime",
      "response_time": "integer"
    }
  ],
  "triggers": [
    {
      "id": "string",
      "alert": {
        "id": "string",
        "name": "string",
        // ... alert details
      }
    }
  ]
}
```

### Account-wide Anomalies

#### List All Anomalies
```http
GET /api/anomalies
```

Returns a paginated list of all anomalies across all monitors.

**Response:**
```json
{
  "data": [
    {
      "id": "string",
      "started_at": "datetime",
      "ended_at": "datetime|null",
      "monitor": {
        "id": "string",
        "address": "string",
        // ... monitor details
      },
      "checks": [
        {
          "id": "string",
          "status": "enum",
          "checked_at": "datetime",
          "response_time": "integer"
        }
      ]
    }
  ],
  "links": {
    "first": "string",
    "last": "string",
    "prev": "string|null",
    "next": "string|null"
  },
  "meta": {
    "current_page": "integer",
    "last_page": "integer",
    "per_page": 15,
    "total": "integer"
  }
}
```

#### Get Anomaly Details
```http
GET /api/anomalies/{anomaly}
```

Returns detailed information about any anomaly.

**Response:**
```json
{
  "id": "string",
  "started_at": "datetime",
  "ended_at": "datetime|null",
  "monitor": {
    "id": "string",
    "address": "string",
    // ... monitor details
  },
  "checks": [
    {
      "id": "string",
      "status": "enum",
      "checked_at": "datetime",
      "response_time": "integer"
    }
  ],
  "triggers": [
    {
      "id": "string",
      "alert": {
        "id": "string",
        "name": "string",
        // ... alert details
      }
    }
  ]
}
```

## Error Responses

All endpoints may return the following error responses:

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized."
}
```

### 404 Not Found
```json
{
  "message": "Resource not found."
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message"
    ]
  }
}
```

### 500 Server Error
```json
{
  "message": "Server error message"
}
``` 
