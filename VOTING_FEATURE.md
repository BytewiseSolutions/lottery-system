# Voting Feature Implementation

## Overview
The voting feature allows users to vote for lottery number combinations they want to see as winning numbers. Voting is available between 19:00 and 19:59 daily, with results published immediately after voting closes.

## Features Implemented

### 1. Voting Space
- **Location**: Accessible via footer navigation on home page
- **Functionality**: 
  - Users can vote for 5 main numbers and 2 bonus numbers
  - Voting only allowed between 19:00-19:59
  - Countdown timer shows time until voting opens/closes
  - Multi-step voting process with confirmation
  - Playing feature shows countdown to 20:00 during voting period

### 2. Leading Numbers Now
- **Real-time Display**: Shows numbers with most votes
- **Top Combination**: Displays the leading 5 numbers + 2 bonus numbers
- **Full List**: Shows all numbers sorted by vote count
- **Sections**: Separated into Section 1 (main numbers) and Section 2 (bonus numbers)

### 3. My Voting History
- **Personal History**: Shows all user's past votes
- **Details**: Displays lottery name, date, selected numbers, and timestamp
- **Format**: Similar to playing history for consistency

## Backend Implementation

### Database Schema (`voting-schema.sql`)
```sql
-- User votes table
CREATE TABLE `vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `numbers` JSON NOT NULL,
  `bonus_numbers` JSON NOT NULL,
  `vote_date` date NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
);

-- Admin allocated votes (for rigging)
CREATE TABLE `admin_vote` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `lottery` varchar(50) NOT NULL,
  `numbers` JSON NOT NULL,
  `bonus_numbers` JSON NOT NULL,
  `allocated_votes` int(11) DEFAULT 0,
  `vote_date` date NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
);
```

### API Endpoints

#### 1. `vote.php` (POST)
- **Purpose**: Submit a vote
- **Auth**: Required (JWT)
- **Time Restriction**: 19:00-19:59 only
- **Payload**:
```json
{
  "lottery": "Monday Lotto",
  "numbers": [3, 8, 33, 61, 70],
  "bonusNumbers": [9, 40],
  "voteDate": "2026-03-02"
}
```

#### 2. `voting-history.php` (GET)
- **Purpose**: Get user's voting history
- **Auth**: Required (JWT)
- **Response**:
```json
{
  "votes": [
    {
      "id": 1,
      "lottery": "Monday Lotto",
      "numbers": [3, 8, 33, 61, 70],
      "bonusNumbers": [9, 40],
      "voteDate": "2026-03-02",
      "createdAt": "2026-03-02 19:45:00"
    }
  ]
}
```

#### 3. `leading-numbers.php` (GET)
- **Purpose**: Get real-time leading numbers
- **Auth**: Not required (public)
- **Query Params**: `lottery`, `voteDate`
- **Response**:
```json
{
  "section1": [
    {"number": 36, "votes": 1456},
    {"number": 33, "votes": 1399},
    ...
  ],
  "section2": [
    {"number": 5, "votes": 2043},
    {"number": 24, "votes": 2001},
    ...
  ]
}
```

#### 4. `admin-vote.php` (POST/GET)
- **Purpose**: Admin portal to allocate votes (rigging system)
- **Auth**: Required (Admin only)
- **POST Payload**:
```json
{
  "lottery": "Monday Lotto",
  "numbers": [3, 8, 33, 61, 70],
  "bonusNumbers": [9, 40],
  "allocatedVotes": 5000,
  "voteDate": "2026-03-02"
}
```

## Frontend Implementation

### Files Created/Modified

#### New Files:
1. **`app/src/app/voting/voting.page.ts`** - Main voting page logic
2. **`app/src/app/voting/voting.page.html`** - Voting page template
3. **`app/src/app/voting/voting.page.scss`** - Voting page styles
4. **`app/src/app/services/voting.service.ts`** - Voting API service

#### Modified Files:
1. **`app/src/app/home/home.page.html`** - Added voting tab to footer
2. **`app/src/app/app-routing.module.ts`** - Added voting route (auto-generated)

### Voting Flow

1. **Step 1**: Select 5 numbers from Section 1
2. **Step 2**: Confirm 5 numbers (can edit)
3. **Step 3**: Select 2 bonus numbers from Section 2
4. **Step 4**: Confirm 2 bonus numbers (can edit)
5. **Step 5**: Final confirmation showing all selections
6. **Submit**: Vote is recorded

### Time Management
- **Before 19:00**: Shows countdown to voting time
- **19:00-19:59**: Voting active with countdown to close
- **After 20:00**: Shows voting closed message

## Admin Rigging System

### How It Works
1. Admin plays lottery 5+ times with different combinations during playing time
2. Admin accesses admin portal (separate from user app)
3. Admin allocates votes to specific combinations
4. Allocated votes are added to real user votes
5. Leading numbers reflect combined totals
6. "The house wins at the end of the day"

### Admin Portal Access
- Requires admin role in database
- Uses same JWT authentication
- Separate admin interface (not in mobile app)

## Usage Instructions

### For Users:
1. Navigate to home page
2. Click "Voting" tab in footer
3. Wait for voting time (19:00-19:59)
4. Click "Vote Now"
5. Select 5 numbers, confirm
6. Select 2 bonus numbers, confirm
7. Review and submit

### For Admins:
1. Play lottery with desired combinations
2. Access admin portal
3. Select combination to boost
4. Allocate votes (e.g., 5000 votes)
5. Submit to influence leading numbers

## Testing Checklist

- [ ] Voting only works between 19:00-19:59
- [ ] Countdown timer updates correctly
- [ ] Number selection limited to 5 + 2
- [ ] Can edit selections before final submit
- [ ] Vote appears in voting history
- [ ] Leading numbers update in real-time
- [ ] Admin can allocate votes
- [ ] Allocated votes affect leading numbers
- [ ] Playing disabled during voting time (19:00-19:59)
- [ ] Playing resumes at 20:00

## Security Considerations

1. **Time Validation**: Backend enforces 19:00-19:59 restriction
2. **Authentication**: All voting requires valid JWT token
3. **Admin Access**: Admin features require admin role
4. **Input Validation**: Numbers validated (5 main + 2 bonus)
5. **SQL Injection**: Prepared statements used throughout

## Future Enhancements

1. **Real-time Updates**: WebSocket for live vote counts
2. **Vote Limits**: Limit votes per user per day
3. **Analytics**: Track voting patterns
4. **Notifications**: Alert users when voting opens
5. **Social Features**: Share voting combinations
6. **Leaderboards**: Most popular combinations over time

## Notes

- Voting closes at 19:59, results published immediately
- Playing stops at 20:00 (1 minute after voting closes)
- Admin rigging is intentional per requirements
- Frontend handles UX, backend enforces rules
- All times are server time (ensure timezone consistency)
