# Lottery System

A full-stack lottery management system built with Node.js and Angular.

## Features
- User registration and authentication
- Multiple lottery games (Mon/Wed/Fri Lotto)
- Real-time countdown timers
- Lottery entry submission
- Results tracking and history
- Responsive design

## Quick Start

1. **Install Dependencies**
   ```bash
   cd api && npm install
   cd ../web && npm install
   ```

2. **Setup Database**
   ```bash
   cd api && node setup-database.js
   ```

3. **Start Development Servers**
   ```bash
   cd api && start-servers.bat
   ```

4. **Access Application**
   - Web App: http://localhost:4200
   - API: http://localhost:3002

## Project Structure
- `/api` - Node.js backend API
  - `setup-database.js` - Database initialization
  - `start-servers.bat` - Development server startup
- `/web` - Angular frontend application

## Environment Variables
Create `.env` file in `/api` directory:
```
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=your_password
DB_NAME=lottery_db
JWT_SECRET=your_secret_key
```