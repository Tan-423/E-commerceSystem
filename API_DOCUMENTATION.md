# REST API Documentation

This document outlines the REST API endpoints that have been added to your Laravel application.

## Base URL
All API endpoints are prefixed with `/api/`

## Authentication
The API includes a default authentication route:
- `GET /api/user` - Get authenticated user (requires Sanctum auth)

## Available Resources

### Products
- `GET /api/products` - List all products (with category and brand)
- `GET /api/products/{id}` - Get specific product
- `POST /api/products` - Create new product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product
- `GET /api/products/{id}/brand` - Get product's brand
- `GET /api/products/{id}/category` - Get product's category

### Brands
- `GET /api/brands` - List all brands
- `GET /api/brands/{id}` - Get specific brand
- `POST /api/brands` - Create new brand
- `PUT /api/brands/{id}` - Update brand
- `DELETE /api/brands/{id}` - Delete brand (only if no products)
- `GET /api/brands/{id}/products` - Get brand's products

### Categories
- `GET /api/categories` - List all categories
- `GET /api/categories/{id}` - Get specific category
- `POST /api/categories` - Create new category
- `PUT /api/categories/{id}` - Update category
- `DELETE /api/categories/{id}` - Delete category (only if no products)
- `GET /api/categories/{id}/products` - Get category's products

### Users
- `GET /api/users` - List all users (sensitive data hidden)
- `GET /api/users/{id}` - Get specific user
- `POST /api/users` - Create new user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user
- `GET /api/users/{id}/orders` - Get user's orders
- `GET /api/users/{id}/addresses` - Get user's addresses

### Orders
- `GET /api/orders` - List all orders (with user, items, transaction)
- `GET /api/orders/{id}` - Get specific order
- `POST /api/orders` - Create new order
- `PUT /api/orders/{id}` - Update order
- `DELETE /api/orders/{id}` - Delete order (only if no items/transactions)
- `GET /api/orders/{id}/items` - Get order's items
- `GET /api/orders/{id}/transaction` - Get order's transaction

### Order Items
- `GET /api/order-items` - List all order items
- `GET /api/order-items/{id}` - Get specific order item
- `POST /api/order-items` - Create new order item
- `PUT /api/order-items/{id}` - Update order item
- `DELETE /api/order-items/{id}` - Delete order item

### Addresses
- `GET /api/addresses` - List all addresses
- `GET /api/addresses/{id}` - Get specific address
- `POST /api/addresses` - Create new address
- `PUT /api/addresses/{id}` - Update address
- `DELETE /api/addresses/{id}` - Delete address

### Transactions
- `GET /api/transactions` - List all transactions
- `GET /api/transactions/{id}` - Get specific transaction
- `POST /api/transactions` - Create new transaction
- `PUT /api/transactions/{id}` - Update transaction
- `DELETE /api/transactions/{id}` - Delete transaction

## Response Format

All API responses follow this consistent format:

### Success Response
```json
{
    "success": true,
    "data": { ... },
    "message": "Operation completed successfully" // (for create/update/delete)
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "error": "Detailed error message", // (for 500 errors)
    "errors": { ... } // (for validation errors - 422)
}
```

## HTTP Status Codes
- `200` - Success
- `201` - Created
- `404` - Not Found
- `422` - Validation Error
- `409` - Conflict (e.g., trying to delete resource with dependencies)
- `500` - Server Error

## Validation
All endpoints include proper validation with detailed error messages returned in JSON format.

## Relationships
The API automatically loads relevant relationships where appropriate:
- Products include category and brand
- Orders include user, order items, and transaction
- Order items include product and order details
- Addresses include user information
- Transactions include order information

## Features Implemented
1. ✅ Complete CRUD operations for all models
2. ✅ JSON responses for all endpoints
3. ✅ Proper validation with JSON error responses
4. ✅ Relationship endpoints for related data
5. ✅ Consistent error handling
6. ✅ Protection against deleting resources with dependencies
7. ✅ Password hashing for users
8. ✅ Sensitive data filtering (passwords, tokens hidden)

## Testing
You can test these endpoints using tools like Postman, curl, or any HTTP client. Make sure to set the `Accept: application/json` header for proper JSON responses.
