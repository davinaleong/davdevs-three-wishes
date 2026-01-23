# Annual Wish Emails - Deployment Guide

## Overview
This system sends annual reminder emails to all users on December 31st, showing them their wishes from the current year and encouraging them to set new ones for the upcoming year.

## Components Created

### 1. Mail Class
- **File**: `app/Mail/AnnualWishReminder.php`
- **Purpose**: Handles the email template and content for annual wish reminders
- **Features**: Queued for better performance, includes user's wishes with themes

### 2. Console Commands
- **Main Command**: `app/Console/Commands/SendAnnualWishEmails.php`
  - Command: `php artisan wishes:send-annual-emails`
  - Options: `--dry-run` (test without sending), `--user=UUID` (send to specific user)
  
- **Daily Check Command**: `app/Console/Commands/SendAnnualWishEmailsIfDecember31st.php`
  - Command: `php artisan wishes:send-annual-emails-if-december-31st`
  - Runs daily but only sends emails on December 31st
  
- **Setup Helper**: `app/Console/Commands/SetupHerokuScheduler.php`
  - Command: `php artisan heroku:setup-scheduler`
  - Shows deployment instructions

### 3. Email Template
- **File**: `resources/views/emails/annual-wish-reminder.blade.php`
- **Features**: Responsive HTML design, shows user's wishes, reflection prompts, CTA for new wishes

### 4. Scheduler Configuration
- **File**: `bootstrap/app.php`
- **Schedule**: Daily check at 10:00 AM UTC for December 31st

## Heroku Deployment Steps

### 1. Install Heroku Scheduler Add-on
```bash
heroku addons:create scheduler:standard
```

### 2. Configure Email Service
Set these environment variables on Heroku:
```bash
# For SMTP (e.g., Gmail, SendGrid)
heroku config:set MAIL_MAILER=smtp
heroku config:set MAIL_HOST=smtp.gmail.com
heroku config:set MAIL_PORT=587
heroku config:set MAIL_USERNAME=your-email@gmail.com
heroku config:set MAIL_PASSWORD=your-app-password
heroku config:set MAIL_FROM_ADDRESS=your-email@gmail.com
heroku config:set MAIL_FROM_NAME="Three Wishes"

# Or for Mailgun
heroku config:set MAIL_MAILER=mailgun
heroku config:set MAILGUN_DOMAIN=your-domain.com
heroku config:set MAILGUN_SECRET=your-secret-key
```

### 3. Set up Queue Processing
The system uses queued emails for better performance. Make sure to:
1. Scale worker dyno: `heroku ps:scale worker=1`
2. Ensure queue configuration is set

### 4. Configure Heroku Scheduler
```bash
# Open scheduler dashboard
heroku addons:open scheduler

# Add job with these settings:
Command: php artisan wishes:send-annual-emails-if-december-31st
Frequency: Daily at 10:00 AM UTC
```

### 5. Test the Setup
```bash
# Test locally with dry run
php artisan wishes:send-annual-emails --dry-run

# Test on Heroku
heroku run php artisan wishes:send-annual-emails --dry-run

# Test specific user
heroku run php artisan wishes:send-annual-emails --user=USER_UUID --dry-run
```

## Testing Commands

### Local Testing
```bash
# Dry run for all users
php artisan wishes:send-annual-emails --dry-run

# Send to specific user (replace with actual UUID)
php artisan wishes:send-annual-emails --user=123e4567-e89b-12d3-a456-426614174000 --dry-run

# Test the daily check command (will only send on Dec 31st)
php artisan wishes:send-annual-emails-if-december-31st
```

### Production Testing
```bash
# Test on Heroku without sending emails
heroku run php artisan wishes:send-annual-emails --dry-run

# Check scheduler setup instructions
heroku run php artisan heroku:setup-scheduler

# View logs
heroku logs --tail
```

## Email Content Features

The annual wish email includes:
- Personalized greeting with user's name
- Display of all user's wishes from the current year, organized by theme
- Reflection prompts asking about progress and learnings
- Call-to-action button linking back to the app to create new wishes
- Professional, responsive HTML design
- Proper email headers for deliverability

## Monitoring & Logs

The system logs all email activities:
- Successful sends are logged to application logs
- Failed sends are logged with error details
- User activity logs track when emails are sent
- Command provides detailed output showing progress

## Important Notes

1. **Time Zone**: Scheduled for 10:00 AM UTC on December 31st
2. **Queue**: Emails are queued for better performance
3. **Error Handling**: Failed emails are logged and don't stop the process
4. **User Filter**: Only sends to users with verified email addresses
5. **Wish Filter**: Only sends to users who actually have wishes
6. **Heroku Limitation**: Uses daily check since Heroku Scheduler doesn't support yearly frequency

## Future Enhancements

Consider adding:
- Email preferences (allow users to opt-out)
- Multiple languages support
- Email analytics tracking
- Reminder emails leading up to December 31st
- Integration with calendar services
- Social sharing features for wishes