# Architecture Overview - New Features

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     FRONTEND (Angular/Ionic)                 │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Settings   │  │  Past Draws  │  │   Entries    │      │
│  │     Page     │  │     Page     │  │  Component   │      │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘      │
│         │                  │                  │              │
│         └──────────────────┼──────────────────┘              │
│                            │                                 │
│  ┌─────────────────────────▼──────────────────────────┐     │
│  │              Services Layer                         │     │
│  ├────────────────────────────────────────────────────┤     │
│  │  • AuthService (profile, password, notifications)  │     │
│  │  • LotteryService (past draws, entry limits)       │     │
│  │  • ShareService (native share, clipboard)          │     │
│  │  • ToastService (user feedback)                    │     │
│  └─────────────────────────┬──────────────────────────┘     │
│                            │                                 │
└────────────────────────────┼─────────────────────────────────┘
                             │
                    HTTP/REST API
                             │
┌────────────────────────────▼─────────────────────────────────┐
│                      BACKEND (PHP)                            │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────────────────────────────────────────────┐   │
│  │                  API Endpoints                        │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │  GET  /api/profile                                   │   │
│  │  PUT  /api/profile                                   │   │
│  │  POST /api/change-password                           │   │
│  │  POST /api/notification-preferences                  │   │
│  │  GET  /api/entry-limit                               │   │
│  │  GET  /api/past-draws                                │   │
│  └──────────────────────┬───────────────────────────────┘   │
│                         │                                    │
│  ┌──────────────────────▼───────────────────────────────┐   │
│  │              Middleware                               │   │
│  ├──────────────────────────────────────────────────────┤   │
│  │  • JWT Authentication                                │   │
│  │  • CORS Headers                                      │   │
│  │  • Input Validation                                  │   │
│  └──────────────────────┬───────────────────────────────┘   │
│                         │                                    │
└─────────────────────────┼────────────────────────────────────┘
                          │
                     SQL Queries
                          │
┌─────────────────────────▼────────────────────────────────────┐
│                    DATABASE (MySQL)                           │
├───────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │     user     │  │    entry     │  │    result    │      │
│  ├──────────────┤  ├──────────────┤  ├──────────────┤      │
│  │ • full_name  │  │ • user_id    │  │ • lottery    │      │
│  │ • email      │  │ • lottery    │  │ • draw_date  │      │
│  │ • phone      │  │ • numbers    │  │ • winning_#  │      │
│  │ • password   │  │ • draw_date  │  │ • jackpot    │      │
│  │ • notif_en ✨│  │ • created_at │  │ • status     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│                                                               │
└───────────────────────────────────────────────────────────────┘

✨ = New field added
```

## Feature Flow Diagrams

### 1. Settings/Account Management Flow

```
User → Profile Menu → Settings Page
                          │
                          ├─→ Update Profile
                          │   └─→ PUT /api/profile
                          │       └─→ Update user table
                          │
                          ├─→ Change Password
                          │   └─→ POST /api/change-password
                          │       ├─→ Verify current password
                          │       └─→ Update password hash
                          │
                          ├─→ Toggle Notifications
                          │   └─→ POST /api/notification-preferences
                          │       └─→ Update notification_enabled
                          │
                          └─→ View Terms/Privacy
                              └─→ Open /api/terms.html
```

### 2. Past Draws History Flow

```
User → Profile Menu → Past Draws Page
                          │
                          ├─→ Load Past Draws
                          │   └─→ GET /api/past-draws
                          │       └─→ Query result + draw tables
                          │
                          ├─→ Filter by Lottery
                          │   └─→ Client-side filtering
                          │
                          └─→ Share Result
                              └─→ ShareService
                                  ├─→ Native Share API (mobile)
                                  └─→ Clipboard API (web)
```

### 3. Entry Management Flow

```
User → Entries Tab
         │
         ├─→ Load Entry Limit
         │   └─→ GET /api/entry-limit
         │       └─→ Count today's entries
         │           └─→ Return remaining/total
         │
         ├─→ Load Entries
         │   └─→ GET /api/entries
         │       └─→ Query entry table
         │
         ├─→ Filter by Lottery
         │   └─→ Client-side filtering
         │
         └─→ Submit New Entry
             └─→ POST /api/play
                 ├─→ Check daily limit
                 ├─→ Create entry
                 └─→ Refresh limit display
```

### 4. Share Feature Flow

```
User clicks Share Button
         │
         ├─→ Format share text
         │   └─→ Include lottery, date, numbers, jackpot
         │
         └─→ ShareService.share()
             │
             ├─→ Check if navigator.share exists
             │   ├─→ YES: Use native share
             │   │   └─→ Show system share dialog
             │   │
             │   └─→ NO: Use clipboard fallback
             │       └─→ Copy to clipboard
             │           └─→ Show success toast
```

## Data Flow

### Profile Update
```
Settings Page
    ↓ (user input)
AuthService.updateProfile()
    ↓ (HTTP PUT)
/api/profile.php
    ↓ (SQL UPDATE)
user table
    ↓ (response)
Update localStorage
    ↓
Update BehaviorSubject
    ↓
UI reflects changes
```

### Entry Limit Check
```
Entries Component
    ↓ (on load)
LotteryService.getEntryLimit()
    ↓ (HTTP GET)
/api/entry-limit.php
    ↓ (SQL COUNT)
entry table (today's entries)
    ↓ (calculate)
remaining = limit - used
    ↓ (response)
Display badge: "X/10 remaining"
```

### Past Draws with Filter
```
Past Draws Page
    ↓ (on load)
LotteryService.getPastDraws()
    ↓ (HTTP GET)
/api/past-draws.php
    ↓ (SQL JOIN)
result + draw tables
    ↓ (response)
Store in draws array
    ↓ (user selects filter)
Client-side filter
    ↓
Display filtered results
```

## Component Hierarchy

```
App
├── Home
│   ├── Profile Component
│   │   ├── Settings Link ✨
│   │   └── Past Draws Link ✨
│   │
│   ├── Entries Component
│   │   ├── Entry Limit Badge ✨
│   │   └── Lottery Filter ✨
│   │
│   └── Results Component
│       └── Share Button ✨
│
├── Settings Page ✨
│   ├── Profile Update Form
│   ├── Password Change Form
│   ├── Notification Toggle
│   └── Legal Links
│
└── Past Draws Page ✨
    ├── Lottery Filter
    ├── Results List
    └── Share Buttons

✨ = New or modified
```

## Security Architecture

```
Frontend Request
    ↓
    ├─→ Add JWT token to headers
    │   (from localStorage)
    ↓
Backend Endpoint
    ↓
    ├─→ JWT::authenticate()
    │   ├─→ Verify token signature
    │   ├─→ Check expiration
    │   └─→ Extract user data
    ↓
    ├─→ Validate input
    │   ├─→ Sanitize data
    │   └─→ Check permissions
    ↓
Database Operation
    ↓
    ├─→ Prepared statements
    │   (prevent SQL injection)
    ↓
Response
    └─→ Return JSON
```

## State Management

```
┌─────────────────────────────────────┐
│         Application State           │
├─────────────────────────────────────┤
│                                     │
│  AuthService                        │
│  ├─→ user$ (BehaviorSubject)       │
│  │   └─→ Profile data              │
│  │                                  │
│  NotificationService                │
│  ├─→ unreadCount$ (BehaviorSubject)│
│  │   └─→ Notification count        │
│  │                                  │
│  Component State                    │
│  ├─→ entries[]                      │
│  ├─→ filteredEntries[] ✨          │
│  ├─→ selectedLottery ✨            │
│  ├─→ entryLimit ✨                 │
│  └─→ draws[]                        │
│                                     │
└─────────────────────────────────────┘

✨ = New state
```

---

**Legend:**
- ✨ = New feature/component
- → = Data flow direction
- ├─→ = Branch in flow
- └─→ = End of branch
