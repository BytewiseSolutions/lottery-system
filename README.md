# Total Free Lotto - Lottery System

A comprehensive lottery management system with user registration, lottery entries, voting features, and admin panel.

## 🚀 Features

### User Features
- **User Registration & Authentication**
  - Register with email and/or phone number
  - Country selection with searchable dropdown
  - Secure password authentication with JWT tokens
  - Profile management with country field

- **Lottery Entries**
  - Submit lottery number entries
  - View upcoming draws
  - Check past results
  - View personal entry history

- **Voting System**
  - Vote for preferred lottery numbers
  - View voting history
  - Participate in community voting

- **Notifications**
  - Profile completion notifications (country field)
  - Draw result notifications
  - Winner announcements

- **Winnings Management**
  - View personal winnings
  - Track payment status
  - Payment history

### Admin Features
- **Dashboard**
  - System statistics and analytics
  - User management
  - Activity logs

- **Draw Management**
  - Create upcoming draws
  - Upload draw results
  - Manage past draws
  - Delete draws

- **Winner Management**
  - View all winners
  - Mark payments as paid
  - Track payment status

- **Voting Management**
  - Admin voting with allocated votes
  - View voting statistics
  - Manage voting results

- **User Management**
  - View all users
  - Manage user accounts
  - View user activity

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.3 or higher
- Node.js 16+ and npm
- Angular CLI 17+
- Web server (Apache/Nginx)

## 🛠️ Installation

### 1. Database Setup

```bash
# Create a new database
mysql -u root -p
CREATE DATABASE lottery_system;
USE lottery_system;

# Import the complete schema
mysql -u root -p lottery_system < api/complete-database-schema.sql
```

### 2. Backend Setup (API)

```bash
# Navigate to API directory
cd api

# Configure database connection
cp config/database.php.example config/database.php
# Edit database.php with your credentials

# Configure JWT secret
cp config/jwt.php.example config/jwt.php
# Edit jwt.php with your secret key

# Set proper permissions
chmod 755 .
chmod 644 *.php
```

### 3. Frontend Setup (Web)

```bash
# Navigate to web directory
cd web

# Install dependencies
npm install

# Configure API endpoint
# Edit src/environments/environment.ts
# Set apiUrl to your backend URL

# Development server
npm start
# Navigate to http://localhost:4200

# Production build
npm run build
# Deploy the dist/ folder to your web server
```

## 🔧 Configuration

### Environment Files

**Backend (api/config/database.php)**
```php
private $host = "localhost";
private $db_name = "lottery_system";
private $username = "your_username";
private $password = "your_password";
```

**Frontend (web/src/environments/environment.ts)**
```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost/lottery-system/api'
};
```

## 📊 Database Schema

### Core Tables
- **user** - User accounts with authentication
- **activity_log** - System activity tracking
- **notification** - User notifications

### Lottery Tables
- **entry** - User lottery entries
- **upcoming_draw** - Scheduled draws
- **result** - Published draw results
- **past_draw** - Historical draws

### Voting Tables
- **vote** - User votes
- **admin_vote** - Admin votes with allocation

### Payment Tables
- **winner** - Winner records
- **payment** - Payment transactions

## 🔐 Default Admin Accounts

```
Email: totalfreelotto494@gmail.com
Password: (Set during installation)

Email: lebomona78@gmail.com
Password: (Set during installation)
```

## 🎯 Key Features Implementation

### Country Notification System
- Users without a country in their profile see a notification badge
- Clicking the notification shows a message to update profile
- "Update Profile" button redirects to profile page
- Country field uses searchable dropdown with 195+ countries

### Voting Feature
- Users can vote for lottery numbers
- Admins can allocate votes to specific number combinations
- Voting history tracked per user
- Results influence draw outcomes

### Payment System
- Winners automatically detected after draw results
- Payment status tracking (pending/paid/failed)
- Admin can mark payments as completed
- Payment history maintained

## 📱 Responsive Design

The system is fully responsive and works on:
- Desktop (1920px+)
- Tablet (768px - 1024px)
- Mobile (320px - 767px)

## 🔒 Security Features

- Password hashing with bcrypt
- JWT token authentication
- SQL injection prevention with prepared statements
- XSS protection
- CORS configuration
- Rate limiting (optional)
- Activity logging

## 🚦 API Endpoints

### Authentication
- `POST /register` - User registration
- `POST /login` - User login

### User
- `GET /profile` - Get user profile
- `POST /update-profile` - Update profile
- `POST /change-password` - Change password
- `DELETE /delete-account` - Delete account

### Lottery
- `GET /upcoming-draw` - Get upcoming draws
- `POST /entry` - Submit lottery entry
- `GET /result` - Get draw results
- `GET /past-draws` - Get historical draws

### Voting
- `POST /vote` - Submit vote
- `GET /voting-history` - Get voting history
- `POST /admin-vote` - Admin vote submission

### Admin
- `GET /dashboard-stats` - Dashboard statistics
- `POST /upload-result` - Upload draw results
- `GET /winner` - Get winners list
- `POST /mark-paid` - Mark payment as paid

## 📝 Development Notes

### Adding New Features
1. Create database migrations if needed
2. Add API endpoints in `/api`
3. Create Angular components in `/web/src/app`
4. Update routing in `app.routes.ts`
5. Test thoroughly

### Code Structure
```
lottery-system/
├── api/                    # Backend PHP API
│   ├── config/            # Configuration files
│   ├── *.php              # API endpoints
│   └── complete-database-schema.sql
├── web/                   # Frontend Angular app
│   ├── src/
│   │   ├── app/
│   │   │   ├── admin/    # Admin components
│   │   │   ├── shared/   # Shared components
│   │   │   └── *.component.ts
│   │   └── environments/
│   └── package.json
└── README.md
```

## 🐛 Troubleshooting

### Database Connection Issues
- Verify database credentials in `config/database.php`
- Check MySQL service is running
- Ensure database exists and schema is imported

### CORS Issues
- Check `config/cors.php` settings
- Verify allowed origins match your frontend URL
- Clear browser cache

### JWT Token Issues
- Verify JWT secret is configured
- Check token expiration time
- Clear localStorage and re-login

## 📄 License

This project is proprietary software. All rights reserved.

## 👥 Support

For support, email: totalfreelotto494@gmail.com

## 🔄 Version History

### Version 1.0.0 (Current)
- Initial release
- User registration and authentication
- Lottery entry system
- Voting feature
- Admin panel
- Country notification system
- Payment tracking
