# Barokah Jaya - Sistem Kasir & Manajemen Toko Barokah Jaya

Aplikasi Point of Sale (POS) berbasis web modern yang dibangun dengan Laravel 12 dan Livewire 3.6 untuk mengelola transaksi penjualan, inventori produk, dan analisis bisnis dengan integrasi AI dari Google Gemini.

## 📋 Deskripsi Singkat

**Aplikasi Point of Sale (POS)** adalah sistem kasir komprehensif untuk "Toko Barokah Jaya" yang menawarkan:
- 💳 **Manajemen Transaksi** - Sistem pembayaran dengan berbagai metode (cash, transfer, QRIS)
- 📦 **Manajemen Inventori** - Kontrol stok produk otomatis dengan kategori
- 📊 **Dashboard Analitik** - Laporan penjualan real-time dan statistik bisnis
- 🤖 **Asisten AI** - Chatbot pintar berbasis Google Gemini untuk analisis bisnis
- 📱 **Interface Modern** - UI responsif dengan Livewire untuk pengalaman real-time
- 🔐 **Sistem Autentikasi** - Multi-role login (admin/karyawan)

## 🛠️ Teknologi Yang Digunakan

### Backend
- **Framework**: Laravel 12.0
- **Database**: SQLite (default), support MySQL/MariaDB
- **Authentication**: Laravel Authentication System
- **Queue System**: Database Queue untuk background processing

### Frontend
- **Styling**: Bootstrap 5 dengan custom CSS
- **JavaScript**: Vanilla JS + Alpine.js
- **Icons**: Font Awesome / Bootstrap Icons
- **Real-time Updates**: Livewire 3.6

### API & Integrasi
- **AI Service**: Google Gemini 2.5 Flash
- **HTTP Client**: Laravel HTTP Client
- **File Upload**: Laravel Filesystem

## 📊 Database Structure (Migrations)

### Core Tables
1. **users** - Manajemen pengguna (admin/karyawan)
2. **categories** - Kategori produk
3. **products** - Data produk dengan stok dan pricing
4. **transactions** - Header transaksi penjualan
5. **transaction_details** - Detail item yang terjual per transaksi
6. **announcements** - Sistem pengumuman (placeholder)

### Migration Details:
```sql
-- Categories (2025_10_14_032610)
- id, name, description, timestamps

-- Products (2025_10_14_032632)
- id, category_id (FK), name, sku (unique), description
- price (decimal 10,2), stock (integer), image, timestamps

-- Transactions (2025_10_14_032655)
- id, invoice_number (unique), user_id (FK)
- subtotal, discount, tax, total (decimal 10,2)
- paid, change (decimal 10,2)
- payment_method (enum: cash, transfer, qris)
- timestamps

-- Transaction Details (2025_10_14_032712)
- id, transaction_id (FK), product_id (FK)
- quantity, price, subtotal (decimal 10,2)
- timestamps
```

## 🎯 Fitur-Fitur Utama

### 1. Point of Sale (POS) System
- **Interface Kasir**: `/pos` - Interface POS untuk transaksi penjualan
- **Fitur Cart**: Tambah/edit/hapus item dari keranjang belanja
- **Kalkulasi Otomatis**: Subtotal, diskon, pajak, total, kembalian
- **Multi Payment**: Cash, Transfer, QRIS
- **Invoice Generation**: Nomor invoice unik otomatis
- **Stock Management**: Pengurangan stok otomatis setelah transaksi
- **Search & Filter**: Cari produk berdasarkan nama/kategori

### 2. Manajemen Produk & Kategori
- **Product Management**: `/products` - CRUD produk lengkap
- **Category Management**: `/categories` - Manajemen kategori produk
- **Stock Tracking**: Monitoring stok real-time dengan alert stok rendah
- **Product Search**: Pencarian produk yang cepat
- **Image Upload**: Upload gambar produk

### 3. Analisis & Laporan
- **Dashboard**: `/dashboard` - Overview bisnis real-time
- **Sales Analytics**: Penjualan harian, mingguan, bulanan
- **Top Products**: Produk terlaris berdasarkan quantity dan revenue
- **Low Stock Alerts**: Peringatan stok menipis (<10 unit)
- **Transaction History**: Riwayat transaksi lengkap

### 4. Katalog Publik
- **Public Catalog**: `/` - Katalog produk untuk customer
- **Product Browsing**: Browse produk tanpa login
- **Category Filter**: Filter berdasarkan kategori

### 5. Asisten AI (Google Gemini)
- **AI Chat**: Chatbot terintegrasi di seluruh interface
- **Business Intelligence**: Analisis penjualan dengan AI
- **Natural Language**: Query data dalam bahasa Indonesia
- **Real-time Insights**: Jawaban berdasarkan data transaksi terkini
- **Multi-context Analysis**:
  - Sales performance analysis
  - Product inventory status
  - Transaction details reporting

## 🤖 Integrasi AI - Google Gemini

### Architecture
- **Service Class**: `App\Services\GeminiAIService`
- **Endpoint**: `gemini-2.5-flash` model
- **Livewire Component**: `AIChat.php`

### AI Capabilities
1. **Sales Analysis**:
   - "Berapa total penjualan hari ini?"
   - "Produk apa saja yang terjual kemarin?"
   - "Tampilkan laporan penjualan minggu ini"

2. **Product Intelligence**:
   - "Produk mana yang stoknya rendah?"
   - "Berapa total nilai inventori kita?"
   - "Kategori apa yang paling laku?"

3. **Transaction Queries**:
   - "Tampilkan detail transaksi terakhir"
   - "Berapa total transaksi bulan ini?"
   - "Metode pembayaran apa yang paling populer?"

### AI Integration Details
```php
// Context Preparation Methods:
- getSalesContext()       // Data penjualan & statistik
- getProductContext()      // Data inventori & produk
- getTransactionContext() // Data transaksi detail

// Smart Query Detection:
- isSalesRelated()       // Deteksi query penjualan
- isProductRelated()      // Deteksi query produk
- isTransactionRelated() // Deteksi query transaksi
```

## 🗂️ Struktur Project

### Livewire Components
```
app/Livewire/
├── Auth/
│   ├── Login.php          # Form login admin/karyawan
│   └── Register.php       # Registrasi user
├── Dashboard.php          # Dashboard analytics
├── Products/
│   └── ProductIndex.php   # Management produk
├── Categories/
│   └── CategoryIndex.php  # Management kategori
├── Transactions/
│   └── TransactionIndex.php # History transaksi
├── Pos/
│   └── PosIndex.php       # Interface POS kasir
├── Catalog/
│   └── CatalogPublic.php  # Katalog publik
└── AIChat.php             # Asisten AI chatbot
```

### Models
```
app/Models/
├── User.php               # Model user (extends Authenticatable)
├── Category.php           # Model kategori produk
├── Product.php            # Model produk dengan relasi
├── Transaction.php        # Model transaksi header
└── TransactionDetail.php  # Model detail transaksi
```

### Services
```
app/Services/
└── GeminiAIService.php    # Service untuk Google Gemini AI
```

## 🚀 Instalasi & Setup

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM (untuk asset compilation)
- Database (SQLite/MySQL/PostgreSQL)

### Installation Steps

1. **Clone Repository**
```bash
git clone <repository-url>
cd "Barokah-Jaya"
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Configuration**
```bash
# Untuk SQLite (default)
touch database/database.sqlite

# Atau untuk MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=pos_madura
# DB_USERNAME=root
# DB_PASSWORD=
```

5. **Run Migrations**
```bash
php artisan migrate
```

6. **AI Configuration (Optional)**
```bash
# Tambahkan ke .env
GEMINI_API_KEY=your_gemini_api_key_here
```

7. **Link Storage**
```bash
php artisan storage:link
```

8. **Compile Assets**
```bash
npm run build
```

9. **Start Development Server**
```bash
php artisan serve
npm run dev  # In separate terminal
```

## 🔐 Konfigurasi

### Environment Variables
```env
# Basic App Config
APP_NAME="Toko Barokah Jaya"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite

# AI Integration
GEMINI_API_KEY=your_gemini_api_key

# Queue & Cache
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### User Roles
- **Admin**: Full access ke seluruh fitur
- **Karyawan**: Akses terbatas ke POS dan dashboard

## 🎨 UI/UX Features

### Design System
- **Responsive Layout**: Mobile-first design
- **Bootstrap 5**: Modern CSS framework
- **Livewire Components**: Real-time tanpa page refresh
- **Toast Notifications**: Feedback interaksi user
- **Loading States**: Smooth UX dengan skeleton loaders

### Key Interface Elements
- **Sidebar Navigation**: Menu yang terorganisir
- **Search Interface**: Quick search produk
- **Shopping Cart**: Interactive cart dengan live updates
- **Payment Modal**: Smooth payment flow
- **Chat Interface**: Floating chat AI dengan typing animation

## 📱 Routing Structure

### Public Routes
- `/` - Katalog publik produk
- `/register` - Registrasi user

### Guest Routes
- `/login` - Login form

### Authenticated Routes
- `/dashboard` - Dashboard analytics
- `/products` - Management produk
- `/categories` - Management kategori
- `/transactions` - History transaksi
- `/pos` - Interface POS kasir
- `/logout` - Logout

### API Routes
- `POST /gemini/chat` - AI chat endpoint

## 🔧 Development Commands

### Artisan Commands
```bash
php artisan migrate:fresh --seed  # Fresh database with seeders
php artisan queue:work          # Run queue worker
php artisan storage:link        # Link storage directory
php artisan serve               # Start dev server
```

### Asset Commands
```bash
npm run dev                     # Development build
npm run build                   # Production build
npm run watch                   # Watch for changes
```

## 🚨 Testing & Debugging

### Debug Routes (Development)
- `/test-gemini-debug` - Test Gemini API connection
- `/test-gemini` - Test AI service class

### Logging
- AI interactions logged to `laravel.log`
- Error handling dengan detailed logging
- Performance monitoring untuk API calls

## 📈 Performance Optimization

### Caching Strategy
- **Query Caching**: Cache untuk data yang sering diakses
- **View Caching**: Optimasi rendering Livewire
- **Asset Optimization**: Minified CSS/JS production

### Database Optimization
- **Eager Loading**: Prevent N+1 query problems
- **Database Indexing**: Optimized queries
- **Connection Pooling**: Efficient database connections

## 🔮 Future Enhancements

### Planned Features
- [ ] **Invoice PDF Generation**: Export PDF invoice
- [ ] **Advanced Analytics**: Chart & graph integration
- [ ] **Mobile App**: React Native mobile app
- [ ] **Multi-Store**: Support multiple locations
- [ ] **Supplier Management**: Purchase order system
- [ ] **Customer Management**: CRM features
- [ ] **Barcode Scanner**: Product scanning integration
- [ ] **Email Notifications**: Low stock alerts
- [ ] **Backup System**: Automated backup system

### Technical Improvements
- [ ] **API REST**: Full REST API development
- [ ] **WebSocket**: Real-time updates
- [ ] **Redis Caching**: Advanced caching layer
- [ ] **Docker**: Containerization support
- [ ] **CI/CD**: Automated deployment pipeline

## 🤝 Kontribusi

### Development Guidelines
1. **Code Style**: Follow PSR-12 standards
2. **Testing**: Write unit tests untuk new features
3. **Documentation**: Update README untuk changes
4. **Git Flow**: Use feature branches
5. **Code Review**: PR reviews required

### Commit Convention
```
feat: tambah fitur payment QRIS
fix: bug calculation di cart
docs: update README installation
refactor: optimize AI service
test: tambah unit test untuk POS
```

## 📞 Support & Contact

### Developer Information
- **Project Name**: POS Madura 222
- **Store**: Toko Barokah Jaya
- **Version**: 1.0.0
- **Laravel Version**: 12.0
- **PHP Version**: >= 8.2

### Troubleshooting
- **AI Issues**: Check `GEMINI_API_KEY` di .env
- **Database Issues**: Run `php artisan migrate:reset`
- **Asset Issues**: Run `npm run build`
- **Permission Issues**: Check storage permissions

## 🌐 API Endpoints - Postman Testing

### Authentication Required
Semua API endpoints (kecuali public dan guest routes) memerlukan authentication token.

#### Getting Authentication Token
```bash
# Login untuk mendapatkan token
POST /login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}

# Response berisi token yang harus digunakan di headers
# X-CSRF-TOKEN: [token_from_cookie]
```

### Public Endpoints (No Authentication)
```http
# Katalog publik - GET
GET http://localhost:8000/
Headers: Accept: application/json

# Registrasi user - POST
POST http://localhost:8000/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Authentication Endpoints
```http
# Login - POST
POST http://localhost:8000/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}

# Logout - POST (Require Auth)
POST http://localhost:8000/logout
Headers:
  X-CSRF-TOKEN: [token]
  Cookie: [laravel_session]
```

### Product Management API
```http
# GET all products
GET http://localhost:8000/api/products
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# GET single product
GET http://localhost:8000/api/products/{id}
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# POST create product
POST http://localhost:8000/api/products
Content-Type: application/json
Authorization: Bearer [token]

{
  "category_id": 1,
  "name": "Product Name",
  "sku": "SKU001",
  "description": "Product description",
  "price": 10000.00,
  "stock": 50,
  "image": "base64_image_or_url"
}

# PUT update product
PUT http://localhost:8000/api/products/{id}
Content-Type: application/json
Authorization: Bearer [token]

{
  "name": "Updated Product Name",
  "price": 15000.00,
  "stock": 30
}

# PATCH partial update product
PATCH http://localhost:8000/api/products/{id}
Content-Type: application/json
Authorization: Bearer [token]

{
  "stock": 25
}

# DELETE product
DELETE http://localhost:8000/api/products/{id}
Authorization: Bearer [token]
```

### Category Management API
```http
# GET all categories
GET http://localhost:8000/api/categories
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# GET single category with products
GET http://localhost:8000/api/categories/{id}?include=products
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# POST create category
POST http://localhost:8000/api/categories
Content-Type: application/json
Authorization: Bearer [token]

{
  "name": "Category Name",
  "description": "Category description"
}

# PUT update category
PUT http://localhost:8000/api/categories/{id}
Content-Type: application/json
Authorization: Bearer [token]

{
  "name": "Updated Category Name",
  "description": "Updated description"
}

# DELETE category
DELETE http://localhost:8000/api/categories/{id}
Authorization: Bearer [token]
```

### Transaction API
```http
# GET all transactions
GET http://localhost:8000/api/transactions
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# GET transaction by date range
GET http://localhost:8000/api/transactions?start_date=2025-01-01&end_date=2025-01-31
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# GET single transaction with details
GET http://localhost:8000/api/transactions/{id}?include=details,products
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# POST create transaction
POST http://localhost:8000/api/transactions
Content-Type: application/json
Authorization: Bearer [token]

{
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 10000.00
    },
    {
      "product_id": 2,
      "quantity": 1,
      "price": 15000.00
    }
  ],
  "payment_method": "cash",
  "discount": 0,
  "tax": 0,
  "paid": 35000.00
}

# GET transaction by invoice number
GET http://localhost:8000/api/transactions/invoice/INV-2025-001
Headers:
  Accept: application/json
  Authorization: Bearer [token]
```

### Analytics & Reports API
```http
# GET dashboard statistics
GET http://localhost:8000/api/analytics/dashboard
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# GET sales report
GET http://localhost:8000/api/analytics/sales?period=daily&start_date=2025-01-01&end_date=2025-01-31
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# GET top products
GET http://localhost:8000/api/analytics/products/top?limit=10&period=monthly
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# GET inventory report
GET http://localhost:8000/api/analytics/inventory
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# GET low stock products
GET http://localhost:8000/api/analytics/inventory/low-stock?threshold=10
Headers:
  Accept: application/json
  Authorization: Bearer [token]
```

### AI Chat API
```http
# POST AI Chat
POST http://localhost:8000/gemini/chat
Content-Type: application/json
Authorization: Bearer [token]

{
  "message": "Berapa total penjualan hari ini?",
  "context": "sales" // optional: sales, products, transactions
}

# Response:
{
  "success": true,
  "data": {
    "message": "Total penjualan hari ini adalah Rp 1.500.000 dari 15 transaksi.",
    "context_used": "sales",
    "timestamp": "2025-01-29T10:30:00Z"
  }
}
```

### Search & Filter API
```http
# Search products
GET http://localhost:8000/api/search/products?q=product_name&category_id=1&min_price=1000&max_price=50000
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# Search transactions
GET http://localhost:8000/api/search/transactions?invoice=INV-001&payment_method=cash&start_date=2025-01-01
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# Advanced product filter
GET http://localhost:8000/api/products/filter?category=electronics&stock_min=5&stock_max=100&sort=price_asc
Headers:
  Accept: application/json
  Authorization: Bearer [token]
```

### User Management API (Admin Only)
```http
# GET all users
GET http://localhost:8000/api/users
Headers:
  Accept: application/json
  Authorization: Bearer [admin_token]

# GET single user
GET http://localhost:8000/api/users/{id}
Headers:
  Accept: application/json
  Authorization: Bearer [admin_token]

# POST create user
POST http://localhost:8000/api/users
Content-Type: application/json
Authorization: Bearer [admin_token]

{
  "name": "New User",
  "email": "user@example.com",
  "password": "password",
  "role": "karyawan" // admin or karyawan
}

# PUT update user
PUT http://localhost:8000/api/users/{id}
Content-Type: application/json
Authorization: Bearer [admin_token]

{
  "name": "Updated Name",
  "email": "updated@example.com"
}

# DELETE user
DELETE http://localhost:8000/api/users/{id}
Authorization: Bearer [admin_token]
```

### File Upload API
```http
# Upload product image
POST http://localhost:8000/api/upload/product-image
Content-Type: multipart/form-data
Authorization: Bearer [token]

Form Data:
- image: [file]
- product_id: 1 (optional)

# Upload in bulk
POST http://localhost:8000/api/upload/products-csv
Content-Type: multipart/form-data
Authorization: Bearer [token]

Form Data:
- file: [csv_file]
```

### Settings & Configuration API
```http
# GET app settings
GET http://localhost:8000/api/settings
Headers:
  Accept: application/json
  Authorization: Bearer [token]

# PUT update settings
PUT http://localhost:8000/api/settings
Content-Type: application/json
Authorization: Bearer [token]

{
  "store_name": "Toko Barokah Jaya",
  "tax_rate": 10,
  "low_stock_threshold": 10
}
```

## 📋 Postman Collection Template

### Environment Variables
```json
{
  "base_url": "http://localhost:8000",
  "token": "your_auth_token_here",
  "user_id": "1"
}
```

### Collection Structure
1. **Authentication** - Login, Logout, Refresh Token
2. **Products** - CRUD operations, Search, Filter
3. **Categories** - CRUD operations
4. **Transactions** - Create, Read, Reports
5. **Analytics** - Dashboard, Sales, Inventory reports
6. **AI Chat** - AI assistant integration
7. **Users** - User management (Admin only)
8. **Upload** - File upload operations
9. **Settings** - App configuration

### Common Headers untuk semua authenticated requests
```json
{
  "Content-Type": "application/json",
  "Accept": "application/json",
  "Authorization": "Bearer {{token}}",
  "X-CSRF-TOKEN": "{{csrf_token}}"
}
```

### Error Response Format
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": [
      "Validation error message"
    ]
  }
}
```

### Success Response Format
```json
{
  "success": true,
  "data": {
    // response data
  },
  "message": "Success message"
}
```

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## 📄 License

This project is proprietary software for Toko Barokah Jaya. All rights reserved.

---

**🎯 Developed for: Toko Barokah Jaya**
**🔨 Built with: Laravel 12 + Livewire 3.6 + Google Gemini AI**
**📅 Version: 1.0.0 | Last Updated: 2025**
