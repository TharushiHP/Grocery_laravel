# Grocery Laravel API Documentation

## Base URL

-   **Local Development**: `http://localhost:8000/api`
-   **Production (Railway)**: `https://your-railway-domain.com/api`

## Authentication

This API uses **Laravel Sanctum** for authentication. After logging in, you'll receive a Bearer token that should be included in the `Authorization` header for protected routes.

```
Authorization: Bearer YOUR_TOKEN_HERE
```

## Response Format

All API responses follow this structure:

```json
{
    "success": true|false,
    "message": "Description of the result",
    "data": { ... },  // Present on successful requests
    "errors": { ... } // Present on validation errors
}
```

---

## 🔐 Authentication Endpoints

### Register User

**POST** `/auth/register`

Register a new user account.

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2023-10-01T12:00:00.000000Z",
            "updated_at": "2023-10-01T12:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Login User

**POST** `/auth/login`

Authenticate a user and receive an access token.

**Request Body:**

```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2023-10-01T12:00:00.000000Z",
            "updated_at": "2023-10-01T12:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Logout User 🔒

**POST** `/auth/logout`

**Headers:** `Authorization: Bearer TOKEN`

Logout the authenticated user and revoke the current token.

**Response (200):**

```json
{
    "success": true,
    "message": "Logout successful"
}
```

### Get Authenticated User 🔒

**GET** `/auth/user`

**Headers:** `Authorization: Bearer TOKEN`

Get the currently authenticated user's information.

**Response (200):**

```json
{
    "success": true,
    "message": "User data retrieved successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2023-10-01T12:00:00.000000Z",
            "updated_at": "2023-10-01T12:00:00.000000Z"
        }
    }
}
```

### Update User Profile 🔒

**PUT** `/auth/profile`

**Headers:** `Authorization: Bearer TOKEN`

Update the authenticated user's profile.

**Request Body:**

```json
{
    "name": "John Smith",
    "email": "johnsmith@example.com",
    "current_password": "oldpassword123", // Required if changing password
    "password": "newpassword123", // Optional
    "password_confirmation": "newpassword123" // Required if password provided
}
```

---

## 📦 Product Endpoints

### Get All Products

**GET** `/products`

Get a list of all products with optional filtering and pagination.

**Query Parameters:**

-   `category` (string): Filter by category
-   `search` (string): Search in product name/description
-   `min_price` (number): Minimum price filter
-   `max_price` (number): Maximum price filter
-   `in_stock` (boolean): Filter products in stock (true/false)
-   `sort_by` (string): Sort by field (name, price, category, quantity, created_at)
-   `sort_order` (string): Sort order (asc, desc)
-   `per_page` (number): Items per page (1-100, default: 15)

**Example:** `/products?category=fruits&sort_by=price&sort_order=asc&per_page=20`

**Response (200):**

```json
{
    "success": true,
    "message": "Products retrieved successfully",
    "data": {
        "products": [
            {
                "id": 1,
                "name": "Fresh Apples",
                "description": "Organic red apples",
                "price": "2.99",
                "category": "Fruits",
                "quantity": 50,
                "image": "apples.jpg",
                "created_at": "2023-10-01T12:00:00.000000Z",
                "updated_at": "2023-10-01T12:00:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 75,
            "from": 1,
            "to": 15
        }
    }
}
```

### Get Product by ID

**GET** `/products/{id}`

Get a specific product by its ID.

**Response (200):**

```json
{
    "success": true,
    "message": "Product retrieved successfully",
    "data": {
        "product": {
            "id": 1,
            "name": "Fresh Apples",
            "description": "Organic red apples",
            "price": "2.99",
            "category": "Fruits",
            "quantity": 50,
            "image": "apples.jpg",
            "created_at": "2023-10-01T12:00:00.000000Z",
            "updated_at": "2023-10-01T12:00:00.000000Z"
        }
    }
}
```

### Get All Categories

**GET** `/products/categories`

Get a list of all unique product categories.

**Response (200):**

```json
{
    "success": true,
    "message": "Categories retrieved successfully",
    "data": {
        "categories": ["Fruits", "Vegetables", "Dairy", "Bakery", "Meat"]
    }
}
```

### Get Products by Category

**GET** `/products/category/{category}`

Get all products in a specific category.

**Example:** `/products/category/Fruits`

**Response (200):**

```json
{
    "success": true,
    "message": "Products in 'Fruits' category retrieved successfully",
    "data": {
        "category": "Fruits",
        "products": [
            {
                "id": 1,
                "name": "Fresh Apples",
                "description": "Organic red apples",
                "price": "2.99",
                "category": "Fruits",
                "quantity": 50,
                "image": "apples.jpg",
                "created_at": "2023-10-01T12:00:00.000000Z",
                "updated_at": "2023-10-01T12:00:00.000000Z"
            }
        ]
    }
}
```

### Search Products

**GET** `/products/search`

Search for products by name, description, or category.

**Query Parameters:**

-   `query` (required): Search term

**Example:** `/products/search?query=apple`

**Response (200):**

```json
{
    "success": true,
    "message": "Search completed successfully",
    "data": {
        "search_query": "apple",
        "results_count": 3,
        "products": [
            {
                "id": 1,
                "name": "Fresh Apples",
                "description": "Organic red apples",
                "price": "2.99",
                "category": "Fruits",
                "quantity": 50,
                "image": "apples.jpg",
                "created_at": "2023-10-01T12:00:00.000000Z",
                "updated_at": "2023-10-01T12:00:00.000000Z"
            }
        ]
    }
}
```

### Get Featured Products

**GET** `/products/featured`

Get featured/popular products.

**Query Parameters:**

-   `limit` (number): Number of products to return (1-50, default: 10)

**Response (200):**

```json
{
    "success": true,
    "message": "Featured products retrieved successfully",
    "data": {
        "products": [...]
    }
}
```

### Get Low Stock Products

**GET** `/products/low-stock`

Get products with low stock levels.

**Query Parameters:**

-   `threshold` (number): Stock threshold (default: 10)

---

## 👥 User Management Endpoints

### Get All Users 🔒

**GET** `/users`

**Headers:** `Authorization: Bearer TOKEN`

Get a paginated list of all users (typically for admin use).

**Response (200):**

```json
{
    "success": true,
    "message": "Users retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com",
                "email_verified_at": null,
                "created_at": "2023-10-01T12:00:00.000000Z",
                "updated_at": "2023-10-01T12:00:00.000000Z"
            }
        ],
        "last_page": 1,
        "per_page": 20,
        "total": 1
    }
}
```

---

## 🛠️ Admin Product Management (Optional)

### Create Product 🔒

**POST** `/admin/products`

**Headers:** `Authorization: Bearer TOKEN`

Create a new product.

**Request Body:**

```json
{
    "name": "Fresh Bananas",
    "description": "Organic yellow bananas",
    "price": 1.99,
    "category": "Fruits",
    "quantity": 100,
    "image": "bananas.jpg"
}
```

### Update Product 🔒

**PUT** `/admin/products/{id}`

**Headers:** `Authorization: Bearer TOKEN`

Update an existing product.

### Delete Product 🔒

**DELETE** `/admin/products/{id}`

**Headers:** `Authorization: Bearer TOKEN`

Delete a product.

---

## 🏥 Health Check Endpoints

### API Status

**GET** `/status`

Check if the API is online.

### Health Check

**GET** `/health`

Detailed health check including database connection.

---

## 🚨 Error Responses

### Validation Error (422)

```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Authentication Error (401)

```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Not Found (404)

```json
{
    "success": false,
    "message": "Product not found"
}
```

### Server Error (500)

```json
{
    "success": false,
    "message": "Internal server error",
    "error": "Detailed error message"
}
```

---

## 📱 Flutter Integration Examples

### Login Example

```dart
final response = await http.post(
  Uri.parse('$baseUrl/auth/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'user@example.com',
    'password': 'password123',
  }),
);

if (response.statusCode == 200) {
  final data = jsonDecode(response.body);
  final token = data['data']['token'];
  // Store token securely
}
```

### Fetch Products with Authentication

```dart
final response = await http.get(
  Uri.parse('$baseUrl/products'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

---

## 🔄 Next Steps

1. **Deploy to Railway**: Commit and push these changes to deploy the API endpoints
2. **Test API**: Use Postman or similar tools to test the endpoints
3. **Flutter Integration**: Update your Flutter app to use these endpoints
4. **Database Seeding**: Add sample products to test with your Flutter app

The API is now ready for your Flutter grocery app! 🚀
