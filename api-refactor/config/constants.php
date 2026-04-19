<?php

define('ROLE_USER', 'user');
define('ROLE_ADMIN', 'admin');

define('DRAW_SCHEDULED', 'scheduled');
define('DRAW_CLOSED', 'closed');
define('DRAW_COMPLETED', 'completed');
define('DRAW_CANCELLED', 'cancelled');

define('VOTE_SOURCE_USER', 'user');
define('VOTE_SOURCE_ADMIN', 'admin');

define('RESULT_PUBLISHED', 'published');
define('RESULT_DRAFT', 'draft');

define('CLAIM_PENDING', 'pending');
define('CLAIM_CLAIMED', 'claimed');

define('PAYMENT_PENDING', 'pending');
define('PAYMENT_PROCESSING', 'processing');
define('PAYMENT_COMPLETED', 'completed');
define('PAYMENT_FAILED', 'failed');
define('PAYMENT_PAID', 'paid');

define('NOTIFICATION_INFO', 'info');
define('NOTIFICATION_SUCCESS', 'success');
define('NOTIFICATION_WARNING', 'warning');
define('NOTIFICATION_ERROR', 'error');

define('FILE_PROFILE_PICTURE', 'profile_picture');
define('FILE_ID_DOCUMENT', 'id_document');
define('FILE_PROOF_ADDRESS', 'proof_of_address');
define('FILE_OTHER', 'other');

define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_ACCEPTED', 202);
define('HTTP_NO_CONTENT', 204);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_METHOD_NOT_ALLOWED', 405);
define('HTTP_CONFLICT', 409);
define('HTTP_UNPROCESSABLE_ENTITY', 422);
define('HTTP_TOO_MANY_REQUESTS', 429);
define('HTTP_INTERNAL_ERROR', 500);
define('HTTP_SERVICE_UNAVAILABLE', 503);

define('MIN_MAIN_NUMBER', 1);
define('MAX_MAIN_NUMBER', 50);
define('MIN_BONUS_NUMBER', 1);
define('MAX_BONUS_NUMBER', 12);
define('REQUIRED_MAIN_NUMBERS', 5);
define('REQUIRED_BONUS_NUMBERS', 2);

define('MIN_PASSWORD_LENGTH', 8);
define('MAX_PASSWORD_LENGTH', 255);
define('MIN_NAME_LENGTH', 2);
define('MAX_NAME_LENGTH', 100);
define('MAX_EMAIL_LENGTH', 150);
define('MAX_PHONE_LENGTH', 20);
define('MAX_MESSAGE_LENGTH', 1000);

define('MAX_FILE_SIZE', 5 * 1024 * 1024);      
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024);      
define('MAX_DOCUMENT_SIZE', 10 * 1024 * 1024);  

define('ONE_MINUTE', 60);
define('ONE_HOUR', 3600);
define('ONE_DAY', 86400);
define('ONE_WEEK', 604800);
define('ONE_MONTH', 2592000);
define('ONE_YEAR', 31536000);

define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);
define('MIN_PAGE_SIZE', 1);

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900);                
define('SESSION_TIMEOUT', 1800);                 
define('JWT_EXPIRY', 3600);                      
define('REFRESH_TOKEN_EXPIRY', 604800);        

define('ACTION_USER_LOGIN', 'user_login');
define('ACTION_USER_LOGOUT', 'user_logout');
define('ACTION_USER_REGISTER', 'user_register');
define('ACTION_ENTRY_SUBMIT', 'entry_submit');
define('ACTION_VOTE_SUBMIT', 'vote_submit');
define('ACTION_DRAW_CREATE', 'draw_create');
define('ACTION_DRAW_CLOSE', 'draw_close');
define('ACTION_RESULT_PUBLISH', 'result_publish');
define('ACTION_PAYMENT_PROCESS', 'payment_process');
define('ACTION_FILE_UPLOAD', 'file_upload');
define('ACTION_PROFILE_UPDATE', 'profile_update');
define('ACTION_PASSWORD_CHANGE', 'password_change');
define('ACTION_PASSWORD_RESET', 'password_reset');

define('ERROR_INVALID_CREDENTIALS', 'Invalid email or password');
define('ERROR_USER_NOT_FOUND', 'User not found');
define('ERROR_USER_INACTIVE', 'User account is inactive');
define('ERROR_ACCOUNT_LOCKED', 'Account temporarily locked due to too many failed attempts');
define('ERROR_INVALID_TOKEN', 'Invalid or expired token');
define('ERROR_PERMISSION_DENIED', 'Permission denied');
define('ERROR_INVALID_INPUT', 'Invalid input data');
define('ERROR_DUPLICATE_ENTRY', 'Duplicate entry');
define('ERROR_FILE_TOO_LARGE', 'File size exceeds maximum limit');
define('ERROR_INVALID_FILE_TYPE', 'Invalid file type');
define('ERROR_DRAW_CLOSED', 'Draw is closed for voting');
define('ERROR_INVALID_NUMBERS', 'Invalid lottery numbers');
define('ERROR_MAX_VOTES_EXCEEDED', 'Maximum votes per draw exceeded');

define('SUCCESS_LOGIN', 'Login successful');
define('SUCCESS_LOGOUT', 'Logout successful');
define('SUCCESS_REGISTER', 'Registration successful');
define('SUCCESS_VOTE_SUBMIT', 'Vote submitted successfully');
define('SUCCESS_PROFILE_UPDATE', 'Profile updated successfully');
define('SUCCESS_PASSWORD_CHANGE', 'Password changed successfully');
define('SUCCESS_FILE_UPLOAD', 'File uploaded successfully');
define('SUCCESS_PAYMENT_PROCESS', 'Payment processed successfully');
