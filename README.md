# 🙏 Dav/Devs Three Wishes

> *"Trust in the Lord with all your heart and lean not on your own understanding; in all your ways submit to him, and he will make your paths straight."* - Proverbs 3:5-6

A **Christian faith-based platform** for setting spiritual intentions and experiencing God's faithfulness through yearly reflection.

## 🔒 **Privacy-First Platform**
**Your spiritual journey is completely private.** This platform has been designed with maximum privacy protection - administrators cannot view, access, or manage any user data. Your three wishes, personal information, and spiritual content remain entirely under your control.

## ✨ About Dav/Devs Three Wishes

Dav/Devs Three Wishes is a sacred digital space where believers can prayerfully set three meaningful spiritual intentions for each year, trusting God to work in His perfect timing. Our platform combines modern technology with timeless faith principles to create a meaningful spiritual journey tracking experience.

### 🎯 Core Purpose
- **Spiritual Goal Setting**: Set three meaningful intentions for the year in prayer
- **Annual Reflection**: Receive beautiful reminder emails every December 31st
- **Faith Community**: Join believers worldwide in intentional spiritual growth
- **God's Faithfulness**: Track and celebrate how God works through your prayers

## 🚀 Features

### � Privacy-First Design
- **No User Data Viewing**: Admin cannot view user information for complete privacy
- **User-Controlled Data**: Users have full control over their spiritual intentions
- **Privacy by Design**: Built with data protection from the ground up
- **Secure Processing**: No unauthorized access to personal spiritual content

### 📧 Email Ministry System (User-Controlled)
- **Custom Verification Emails**: Beautifully designed welcome emails with Christian branding
- **Personal Reflection System**: Each user manages their own spiritual journey
- **Theme Integration**: Dynamic email styling based on yearly spiritual themes
- **Individual Privacy**: No bulk email access or user data aggregation

### 🛡️ Privacy-Focused Administration
- **Theme Management Only**: Admins manage yearly spiritual themes and content
- **No User Access**: Complete separation between admin and user data
- **Activity Logging**: Admin actions are logged for accountability
- **Content Management**: Spiritual themes, Bible verses, and site content

### 🎨 Dynamic Theming
- **Yearly Themes**: Each year features a unique spiritual theme with colors and verses
- **Scripture Integration**: Bible verses and Christian messaging throughout the platform
- **Responsive Design**: Beautiful experience on desktop and mobile devices
- **Accessibility**: WCAG compliant design for all users

### 🔐 User Experience
- **Secure Authentication**: Email verification and optional 2FA support
- **UUID-based URLs**: Privacy-focused user identification
- **Activity Logging**: Track your spiritual journey over time
- **Data Portability**: Export your spiritual intentions anytime

### ⚖️ Enhanced Privacy & GDPR Compliance
- **Privacy by Design**: No admin access to user data - ultimate privacy protection
- **User Rights**: Full access, rectification, erasure, and portability rights
- **Zero Admin Visibility**: Admins cannot view, export, or access user information
- **Transparent Data Handling**: Clear privacy policy with enhanced protection measures
- **Secure Processing**: User-controlled data with no unauthorized administrative access

### 🛠️ Technical Excellence
- **Laravel 12**: Modern PHP framework with robust security
- **Database Queue System**: Reliable background job processing
- **Heroku Deployment**: Scalable cloud hosting with automatic deployments
- **Mailgun Integration**: Professional email delivery service
- **PostgreSQL Database**: Reliable data storage with backups

## 🏗️ Technology Stack

### Backend
- **PHP 8.5** with **Laravel 12**
- **PostgreSQL** database
- **Mailgun** for email delivery
- **Heroku** cloud hosting

### Frontend
- **Tailwind CSS** for styling
- **Alpine.js** for interactivity
- **Vite** for asset bundling
- **Blade Templates** for server-side rendering

### Infrastructure
- **Heroku Dynos**: Web and worker processes
- **Database Queue**: Laravel job processing
- **SSL/HTTPS**: Secure communications
- **Environment Configuration**: Separate development/production settings

## 📋 Installation & Setup

### Prerequisites
- PHP 8.5+
- Composer
- Node.js & npm
- PostgreSQL (for production)

### Local Development
```bash
# Clone the repository
git clone https://github.com/your-username/davdevs-three-wishes.git
cd davdevs-three-wishes

# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database and email settings in .env
# Run migrations and seeders
php artisan migrate --seed

# Build assets
npm run build

# Start the development server
php artisan serve

# In a separate terminal, run the queue worker
php artisan queue:work
```

### Environment Variables
```env
APP_NAME="Dav/Devs Three Wishes"
APP_ENV=production
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

# Mailgun Configuration
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-secret

# Queue Configuration
QUEUE_CONNECTION=database
```

## 🚀 Deployment

### Heroku Deployment
```bash
# Login to Heroku
heroku login

# Create a new app
heroku create your-app-name

# Add buildpacks
heroku buildpacks:add heroku/nodejs
heroku buildpacks:add heroku/php

# Set environment variables
heroku config:set APP_NAME="Dav/Devs Three Wishes"
heroku config:set APP_ENV=production
heroku config:set MAILGUN_DOMAIN=your-domain.com
heroku config:set MAILGUN_SECRET=your-secret

# Deploy
git push heroku main

# Run migrations
heroku run php artisan migrate:fresh --seed

# Scale worker dyno for queue processing
heroku ps:scale worker=1
```

## 📊 Database Schema

### Core Tables
- **users**: User accounts with UUID, email verification, 2FA support
- **themes**: Yearly spiritual themes with colors, titles, and Bible verses
- **wishes**: User's three spiritual intentions linked to themes
- **user_activity_logs**: Activity tracking for user actions
- **jobs**: Laravel queue system for email processing

### Key Relationships
- Users have many Wishes
- Wishes belong to Themes
- Activity logs track user actions
- Queue jobs handle email delivery

## 🎨 Spiritual Themes

Each year features a unique theme with:
- **Theme Title**: Inspirational yearly focus
- **Theme Colors**: Primary, accent, and light color schemes
- **Bible Verse**: Scripture foundation for the year
- **Tagline**: Memorable phrase for reflection

Example themes:
- **2026**: "The Year of Much More" (Jeremiah 33:3)
- **2025**: "Walking in Faith" (2 Corinthians 5:7)

## 📧 Email System (Privacy-Protected)

### Email Types
1. **Verification Email**: Welcome new users with Christian branding
2. **Welcome Email**: Post-verification introduction to the platform  
3. **Personal Reflection**: User-controlled spiritual journey emails

### Privacy Features
- **User-Controlled**: Each user manages their own email preferences
- **No Admin Access**: Administrators cannot send emails to or view user addresses
- **Personal Data Protection**: Email content remains private to each user
- **Scripture Integration**: Bible verses in every email
- **Theme Colors**: Dynamic styling based on yearly themes
- **GDPR Compliance**: Privacy policy and unsubscribe links

## 👥 Ministry Team

**Dav/Devs Three Wishes Ministry Team**
- Email: support@gracesoft.dev
- Privacy: privacy@gracesoft.dev
- Website: https://davdevs-three-wishes.herokuapp.com

## 🛡️ Security & Enhanced Privacy

### Security Measures
- **HTTPS Encryption**: All data transmission secured
- **Password Hashing**: Bcrypt encryption for user passwords
- **CSRF Protection**: Laravel's built-in security features
- **SQL Injection Prevention**: Eloquent ORM protection
- **Admin Data Separation**: Complete isolation of user data from administrative access

### Enhanced Privacy Compliance
- **Zero Admin Access**: Administrators cannot view any user data
- **Privacy by Design**: Built with maximum privacy protection
- **GDPR Compliant**: Full user rights implementation with enhanced protection
- **Data Isolation**: User spiritual content remains completely private
- **User Rights**: Access, rectification, erasure, portability - fully user-controlled

## 🧪 Testing

```bash
# Run the test suite
php artisan test

# Test email functionality
php artisan test:welcome-email
php artisan test:verification-email
php artisan test:annual-emails

# Queue processing test
php artisan queue:work --stop-when-empty
```

## 🎯 Commands

### Theme Management
```bash
# Manage spiritual themes
php artisan theme:create [year] [title]
php artisan theme:activate [year]
```

### Database Management
```bash
# Fresh migration with seeders
php artisan migrate:fresh --seed

# Seed themes only
php artisan db:seed --class=ThemeSeeder
```

### Privacy Note
User email functionality has been disabled for enhanced privacy protection. Users control their own spiritual journey without administrative oversight.

## 📜 Scripture Foundation

> *"And we know that in all things God works for the good of those who love him, who have been called according to his purpose."* - Romans 8:28

> *"For I know the plans I have for you," declares the Lord, "plans to prosper you and not to harm you, to give you hope and a future."* - Jeremiah 29:11

> *"Trust in the Lord with all your heart and lean not on your own understanding; in all your ways submit to him, and he will make your paths straight."* - Proverbs 3:5-6

## 📄 Legal

- [Terms & Conditions](https://davdevs-three-wishes.herokuapp.com/terms)
- [Privacy Policy](https://davdevs-three-wishes.herokuapp.com/privacy)
- GDPR & PDPA Compliant
- Christian Ministry Platform

## 🤝 Contributing

This is a ministry project built with love for the Christian community. While primarily developed for Dav/Devs ministry purposes, we welcome feedback and suggestions that align with our Christian values.

### Development Guidelines
- Maintain Christian focus and appropriate content
- Follow Laravel best practices
- Ensure GDPR/PDPA compliance in any changes
- Test email functionality thoroughly
- Respect the spiritual nature of user data

## 📞 Support

For technical support, privacy concerns, or spiritual questions:
- **Technical Support**: support@gracesoft.dev
- **Privacy Officer**: privacy@gracesoft.dev
- **Ministry Contact**: The Dav/Devs Three Wishes Ministry Team

---

*Built with ❤️ and 🙏 for the body of Christ*

**"Now to him who is able to do immeasurably more than all we ask or imagine, according to his power that is at work within us, to him be glory in the church and in Christ Jesus throughout all generations, for ever and ever! Amen."** - Ephesians 3:20-21
