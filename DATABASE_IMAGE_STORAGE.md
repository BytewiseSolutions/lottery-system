# Database Image Storage Implementation

## Overview
Images are now stored in the database using a `data_file` table instead of the filesystem. This provides better data integrity, easier backups, and centralized management.

## Database Schema

### data_file Table
```sql
CREATE TABLE `data_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_data` longblob NOT NULL,
  `file_category` varchar(50) DEFAULT 'profile_picture',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_category` (`file_category`),
  CONSTRAINT `data_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### user Table Update
```sql
-- Change profile_picture from VARCHAR to INT (foreign key to data_file)
ALTER TABLE user MODIFY COLUMN profile_picture INT(11) DEFAULT NULL;
ALTER TABLE user ADD CONSTRAINT `user_profile_picture_fk` 
  FOREIGN KEY (`profile_picture`) REFERENCES `data_file` (`id`) ON DELETE SET NULL;
```

## API Endpoints

### 1. Upload Profile Picture
**Endpoint:** `POST /upload-profile-picture`

**Request:**
- Headers: `Authorization: Bearer {token}`
- Body: `multipart/form-data`
  - `profilePicture`: File (image)
  - `userId`: Integer

**Process:**
1. Validates file type (JPG, PNG, GIF)
2. Validates file size (max 5MB)
3. Reads file data into memory
4. Deletes old profile picture if exists
5. Inserts new file into `data_file` table
6. Updates `user.profile_picture` with file ID
7. Returns file ID

**Response:**
```json
{
  "success": true,
  "profilePictureId": 123,
  "message": "Profile picture uploaded successfully"
}
```

### 2. Get File
**Endpoint:** `GET /get-file.php?id={fileId}`

**Process:**
1. Retrieves file from `data_file` table
2. Sets appropriate Content-Type header
3. Sets cache headers (1 year)
4. Outputs binary image data

**Response:**
- Binary image data with appropriate headers

## Frontend Implementation

### Profile Component
```typescript
async uploadProfilePicture() {
  // Upload file
  const response = await fetch('/upload-profile-picture', {
    method: 'POST',
    body: formData
  });
  
  const result = await response.json();
  
  // Build URL to retrieve image
  const imageUrl = `${apiUrl}/get-file.php?id=${result.profilePictureId}`;
  
  // Update UI and localStorage
  this.profile.profilePicture = imageUrl;
  user.profilePicture = imageUrl;
  localStorage.setItem('user', JSON.stringify(user));
}
```

### Login Response
```typescript
// User object now contains full URL to image
{
  "id": 1,
  "fullName": "John Doe",
  "email": "john@example.com",
  "profilePicture": "https://api.example.com/get-file.php?id=123"
}
```

### Display Image
```html
<img [src]="user.profilePicture || 'assets/images/default-avatar.png'" 
     alt="Profile Picture">
```

## Advantages

### 1. Data Integrity
- Images are part of database backups
- Foreign key constraints ensure referential integrity
- Automatic cleanup when user is deleted (CASCADE)

### 2. Security
- No direct file system access
- Centralized access control
- No file path traversal vulnerabilities

### 3. Scalability
- Easy to replicate with database
- No need to sync file systems
- Works with database clustering

### 4. Management
- Easy to query and manage
- Can track file metadata (size, type, upload date)
- Can implement versioning

### 5. Portability
- Database contains everything
- No external dependencies
- Easy to migrate

## Performance Considerations

### Caching
- Browser caching enabled (1 year)
- Consider adding CDN for production
- Can implement application-level caching

### Database Size
- LONGBLOB can store up to 4GB per file
- Monitor database size
- Consider archiving old images

### Optimization Tips
1. Use appropriate image compression before upload
2. Implement lazy loading for images
3. Use CDN for frequently accessed images
4. Consider thumbnail generation for large images

## Migration Steps

### 1. Run SQL Migration
```bash
mysql -u root -p lottery_system < api/create-data-file-table.sql
```

### 2. Migrate Existing Images (if any)
```php
// Script to migrate existing file-based images to database
$files = glob('uploads/profile-pictures/*');
foreach ($files as $file) {
    $userId = extractUserIdFromFilename($file);
    $fileData = file_get_contents($file);
    $fileType = mime_content_type($file);
    
    // Insert into data_file
    $stmt = $db->prepare("INSERT INTO data_file (user_id, file_name, file_type, file_size, file_data) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, basename($file), $fileType, filesize($file), $fileData]);
    
    $fileId = $db->lastInsertId();
    
    // Update user table
    $updateStmt = $db->prepare("UPDATE user SET profile_picture = ? WHERE id = ?");
    $updateStmt->execute([$fileId, $userId]);
}
```

### 3. Remove Old Upload Directory
```bash
# After migration is complete and verified
rm -rf api/uploads/profile-pictures
```

## Testing

### 1. Upload Test
```bash
curl -X POST http://localhost/api/upload-profile-picture \
  -H "Authorization: Bearer {token}" \
  -F "profilePicture=@test.jpg" \
  -F "userId=1"
```

### 2. Retrieve Test
```bash
curl http://localhost/api/get-file.php?id=1 --output test-output.jpg
```

### 3. Verify in Database
```sql
SELECT id, user_id, file_name, file_type, file_size, LENGTH(file_data) as data_length 
FROM data_file;
```

## Files Created/Modified

### New Files:
1. `api/create-data-file-table.sql` - Database schema
2. `api/get-file.php` - Retrieve image endpoint
3. `api/upload-profile-picture.php` - Upload endpoint (rewritten)

### Modified Files:
1. `api/login.php` - Returns image URL instead of file path
2. `web/src/app/profile/profile.component.ts` - Uses image URL

### Removed Files:
1. `api/add-profile-picture-column.sql` - Replaced by create-data-file-table.sql

## Security Notes

1. **File Type Validation**: Only JPG, PNG, GIF allowed
2. **File Size Limit**: 5MB maximum
3. **Authorization**: JWT token required for upload
4. **SQL Injection**: Prepared statements used
5. **XSS Prevention**: Binary data served with proper headers

## Future Enhancements

1. **Thumbnail Generation**: Create smaller versions for lists
2. **Image Compression**: Automatic compression on upload
3. **Multiple Images**: Support for galleries
4. **Image Cropping**: Client-side crop before upload
5. **CDN Integration**: Serve images from CDN
6. **Lazy Loading**: Load images on demand
7. **Progressive Images**: Load low-res first, then high-res

## Status: ✅ COMPLETE

All images are now stored in the database with proper retrieval mechanism!
