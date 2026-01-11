# Production Deployment Checklist

## Pre-deployment
- [ ] Run `./build.sh` to build the Angular application
- [ ] Verify all environment variables in `api/.env`
- [ ] Test locally with production API URL

## File Upload to Hostinger
- [ ] Upload `web/dist/web/*` to `public_html/`
- [ ] Upload `api/*` to `public_html/api/`
- [ ] Upload root `.htaccess` to `public_html/`
- [ ] Upload API `.htaccess` to `public_html/api/`

## Configuration Files
- [ ] `public_html/api/.env` - Production database credentials
- [ ] `public_html/.htaccess` - Angular routing and security headers
- [ ] `public_html/api/.htaccess` - API configuration

## Database Setup
- [ ] Import database schema to u606331557_lottery_db
- [ ] Verify database connection with health check

## Testing
- [ ] Visit https://totalfreelotto.com
- [ ] Test API health: https://totalfreelotto.com/api/health
- [ ] Test user registration flow
- [ ] Test OTP verification
- [ ] Test login functionality
- [ ] Test lottery display
- [ ] Test admin panel (if applicable)

## Security
- [ ] SSL certificate is active
- [ ] CORS is properly configured
- [ ] Sensitive files (.env) are protected
- [ ] Error logging is enabled but errors are hidden from users

## Performance
- [ ] Gzip compression is enabled
- [ ] Static assets are cached
- [ ] Database queries are optimized

## Post-deployment
- [ ] Monitor error logs
- [ ] Test all user flows
- [ ] Verify email/SMS OTP functionality
- [ ] Check mobile responsiveness