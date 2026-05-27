# API POS Madura - Postman Guide

## Base URL
```
http://localhost/api
```

## 🔐 Authentication

### 1. Login
- **URL**: `POST /api/auth/login`
- **Headers**:
  ```
  Content-Type: application/json
  ```
- **Body** (raw JSON):
  ```json
  {
    "email": "admin@example.com",
    "password": "password"
  }
  ```
- **Response**:
  ```json
  {
    "message": "Login successful",
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com"
    },
    "token": "1|abc123def456..."
  }
  ```

### 2. Get Current User
- **URL**: `GET /api/auth/me`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```

## 📦 Products

### 1. Get All Products (Untuk POS)
- **URL**: `GET /api/products`
- **Headers**:
  ```
  Content-Type: application/json
  ```
- **Optional Query Parameters**:
  - `?search=nama_produk` - Search products
  - `?category_id=1` - Filter by category
  - `?in_stock=1` - Only products with stock
  - `?per_page=10` - Items per page

### 2. Get Product Detail
- **URL**: `GET /api/products/{id}`
- **Headers**:
  ```
  Content-Type: application/json
  ```

### 3. Create Product (Authenticated)
- **URL**: `POST /api/products`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Body**:
  ```json
  {
    "category_id": 1,
    "name": "Produk Baru",
    "sku": "PRD-001",
    "description": "Deskripsi produk",
    "price": 15000,
    "stock": 50,
    "image": "product.jpg"
  }
  ```

## 🏷️ Categories

### 1. Get All Categories
- **URL**: `GET /api/categories`
- **Headers**:
  ```
  Content-Type: application/json
  ```

### 2. Create Category (Authenticated)
- **URL**: `POST /api/categories`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Body**:
  ```json
  {
    "name": "Makanan",
    "description": "Kategori makanan"
  }
  ```

## 🛒 POS Operations

### 1. POS Checkout
- **URL**: `POST /api/pos/checkout`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Body**:
  ```json
  {
    "items": [
      {
        "product_id": 1,
        "quantity": 2
      },
      {
        "product_id": 3,
        "quantity": 1
      }
    ],
    "payment_method": "cash",
    "cash_received": 50000,
    "discount_amount": 5000
  }
  ```
- **Response**:
  ```json
  {
    "success": true,
    "message": "Checkout berhasil",
    "transaction": {
      "id": 123,
      "invoice_number": "INV-20251129-0001",
      "total": 45000,
      "paid": 50000,
      "change": 5000,
      "payment_method": "cash"
    }
  }
  ```

### 2. Get Receipt
- **URL**: `GET /api/pos/receipt/{transaction_id}`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```

### 3. Get Daily Sales Report
- **URL**: `GET /api/pos/daily-sales`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Optional Query Parameters**:
  - `?date=2025-11-29` - Specific date (default: today)

## 💰 Transactions

### 1. Get All Transactions
- **URL**: `GET /api/transactions`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Optional Query Parameters**:
  - `?search=INV-20251129` - Search by invoice number
  - `?start_date=2025-11-01` - Filter start date
  - `?end_date=2025-11-30` - Filter end date
  - `?payment_method=cash` - Filter by payment method

### 2. Create Transaction
- **URL**: `POST /api/transactions`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Body**:
  ```json
  {
    "details": [
      {
        "product_id": 1,
        "quantity": 2
      }
    ],
    "payment_method": "cash",
    "paid": 50000,
    "discount": 0
  }
  ```

### 3. Get Daily Report
- **URL**: `GET /api/transactions/daily`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```

## 🤖 AI Chat

### 1. Chat with AI Assistant
- **URL**: `POST /api/ai/chat`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Body**:
  ```json
  {
    "message": "Berapa total penjualan hari ini?"
  }
  ```
- **Response**:
  ```json
  {
    "success": true,
    "response": "Berdasarkan data, total penjualan hari ini adalah Rp 1.250.000 dari 15 transaksi.",
    "timestamp": "14:30"
  }
  ```

## 📊 Admin Dashboard

### 1. Get Dashboard Statistics
- **URL**: `GET /api/admin/dashboard/stats`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Optional Query Parameters**:
  - `?period=today` - today, week, month, year

### 2. Get Sales Report
- **URL**: `GET /api/admin/reports/sales`
- **Headers**:
  ```
  Authorization: Bearer YOUR_TOKEN_HERE
  Content-Type: application/json
  ```
- **Query Parameters**:
  - `?start_date=2025-11-01`
  - `?end_date=2025-11-30`
  - `?group_by=daily` - daily, weekly, monthly

## 📋 Contoh Workflow di Postman

### 1. Testing Basic API Connection

**Step 1: Get Products (Public)**
```
GET http://localhost/api/products
```

**Step 2: Login to Get Token**
```
POST http://localhost/api/auth/login
{
  "email": "your_email@example.com",
  "password": "your_password"
}
```

**Step 3: Use Token for Authenticated Requests**
Copy token from login response and use in Authorization header:
```
Authorization: Bearer 1|your_token_here
```

### 2. Testing POS Workflow

**Step 1: Get Available Products**
```
GET http://localhost/api/products?in_stock=1
Authorization: Bearer YOUR_TOKEN
```

**Step 2: Perform Checkout**
```
POST http://localhost/api/pos/checkout
Authorization: Bearer YOUR_TOKEN
{
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    }
  ],
  "payment_method": "cash",
  "cash_received": 50000
}
```

**Step 3: Get Receipt**
```
GET http://localhost/api/pos/receipt/{transaction_id}
Authorization: Bearer YOUR_TOKEN
```

## ⚠️ Important Notes

1. **CORS**: Make sure your frontend domain is added to CORS configuration
2. **Authentication**: Most endpoints require Bearer token in Authorization header
3. **Validation**: All requests with `422` status indicate validation errors
4. **Not Found (404)**: Check if you're using correct endpoint URLs
5. **Unauthorized (401)**: Check if your token is valid and not expired

## 🔧 Debugging Tips

### Common Issues:
1. **404 Not Found**:
   - Check if base URL is correct
   - Verify API routes are registered (`php artisan route:list --name=api`)

2. **401 Unauthorized**:
   - Check if token is included in Authorization header
   - Verify token format: `Bearer space token`

3. **422 Validation Error**:
   - Check request body format
   - Ensure required fields are included
   - Validate data types

4. **500 Server Error**:
   - Check Laravel logs (`storage/logs/laravel.log`)
   - Verify database connection
   - Check if all required dependencies are installed