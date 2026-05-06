HMAC Library Management System
API Documentation & Project Overview
Built with Laravel · Secured with HMAC-SHA256

About the Project
The HMAC Library Management System is a RESTful API built using the Laravel framework. It manages a library's book inventory and enforces secure access through HMAC-SHA256 (Hash-based Message Authentication Code) request signing, ensuring that all API requests are authenticated and tamper-proof.

Features
•	HMAC-SHA256 authentication on all API routes
•	Full CRUD operations for book management
•	Timestamp-based replay attack prevention (±5 minute window)
•	Public/secret key pair authentication per user
•	Structured JSON responses with descriptive error messages
•	Laravel Eloquent ORM with database migrations and factories

Tech Stack
Component	Technology
Framework	Laravel 11 (PHP)
Authentication	HMAC-SHA256 custom middleware
Database	MySQL / SQLite
API Testing	Postman
Version Control	Git & GitHub

How HMAC Authentication Works
Every API request must include the following three HTTP headers:

Header	Purpose
X-PUBLIC-KEY	Identifies the requesting user
X-TIMESTAMP	Unix timestamp — must be within ±5 minutes of server time
X-SIGNATURE	HMAC-SHA256 hash of the request (see formula below)

Signature Formula
HMAC-SHA256( METHOD + fullURL + requestBody + timestamp, secret_key )

The server independently recomputes the signature using the user's stored secret_key and compares it using a timing-safe hash_equals() check to prevent timing attacks.

API Endpoints
All routes are prefixed with /api and protected by the hmac middleware.

Method	Endpoint	Description
GET	/api/books	Retrieve all books
POST	/api/books	Create a new book
GET	/api/books/{id}	Retrieve a specific book
PUT	/api/books/{id}	Update a book
DELETE	/api/books/{id}	Delete a book

Installation & Setup
1. Clone the repository
git clone https://github.com/Nathansathan/HMAC-LIBRARY-M-SYSTEM.git
cd HMAC-LIBRARY-M-SYSTEM
2. Install dependencies
composer install
3. Configure environment
cp .env.example .env
php artisan key:generate
Update your .env file with your database credentials.
4. Run migrations
php artisan migrate
5. Seed test users (optional)
php artisan db:seed
6. Serve the application
php artisan serve

Team Members
The following members contributed to the HMAC Library Management System with evenly distributed responsibilities:

Name	Role	Description
Ron Karlo Lanzanas	Postman & Coding	API development, backend coding, and Postman integration testing
Lovely Dumalag	Documentation	Technical writing, documentation, and project reporting
Maui Comia	System Design	System architecture, database design, and ERD modeling
Catrissia Par	UI/UX & Testing	User interface design, frontend layout, and functional testing
Nathaniel Estabaya	Backend Support	Server-side logic, middleware configuration, and debugging
Trixjon Umandap	QA & Testing	Quality assurance, test case preparation, and bug tracking
Kyle Anoras	DevOps & Setup	Environment setup, deployment configuration, and Git management


