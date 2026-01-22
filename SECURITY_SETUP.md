# Security Setup Guide

This document explains the security features implemented and how to configure them.

## ✅ Security Features Implemented

### 1. Server-Side Password Validation
- **File**: `validate-password.php`
- Passwords are validated on the server, not in JavaScript
- Session-based authorization (1 hour timeout)
- Rate limiting: Max 5 attempts per 15 minutes per IP
- Passwords are never exposed in client-side code

### 2. CSRF Protection
- **Files**: `get-csrf-token.php`, `contact.php`
- All form submissions require a valid CSRF token
- Tokens are regenerated after each submission
- Prevents cross-site request forgery attacks

### 3. Enhanced Email Security
- Email header injection protection
- Proper email sanitization
- Secure email headers

### 4. Security Headers (.htaccess)
- X-Content-Type-Options: nosniff
- X-Frame-Options: SAMEORIGIN
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin
- Permissions-Policy
- Content Security Policy (CSP)

### 5. File Protection
- Sensitive files protected by .htaccess
- Directory listing disabled
- Rate limit files protected

## 🔧 Optional: reCAPTCHA Setup

### Step 1: Get reCAPTCHA Keys

1. Go to: https://www.google.com/recaptcha/admin/create
2. Register your site:
   - **Label**: The Bargain
   - **reCAPTCHA type**: reCAPTCHA v3
   - **Domains**: thebargain.com.ng, www.thebargain.com.ng
3. Accept terms and submit
4. Copy your **Site Key** and **Secret Key**

### Step 2: Add Site Key to HTML

Edit `index.html` and uncomment the reCAPTCHA script:

```html
<!-- Find this line and uncomment it, replace YOUR_SITE_KEY -->
<script src="https://www.google.com/recaptcha/api.js?render=YOUR_SITE_KEY"></script>
```

Replace `YOUR_SITE_KEY` with your actual site key.

### Step 3: Add Secret Key to PHP

Edit `contact.php` and update:

```php
$recaptcha_secret = 'YOUR_SECRET_KEY_HERE';
```

Replace `YOUR_SECRET_KEY_HERE` with your actual secret key.

### Step 4: Update JavaScript (if needed)

The JavaScript will automatically use reCAPTCHA if the script is loaded. No additional changes needed.

## 🔒 HTTPS Setup (Recommended)

### Step 1: Enable SSL in cPanel

1. Log into cPanel
2. Go to **SSL/TLS Status**
3. Install SSL certificate (Let's Encrypt is free)
4. Force HTTPS redirect

### Step 2: Enable HTTPS in .htaccess

Edit `.htaccess` and uncomment the HTTPS redirect section:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

Also uncomment the HSTS header:

```apache
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

## 📋 Security Checklist

- [x] Server-side password validation
- [x] CSRF protection
- [x] Input sanitization
- [x] Rate limiting
- [x] Security headers
- [x] File access protection
- [x] Email security
- [ ] reCAPTCHA (optional - follow steps above)
- [ ] HTTPS enforcement (enable when SSL is configured)

## 🔍 Testing Security

### Test Password Protection
1. Try accessing investment page without password
2. Try incorrect password multiple times (should lockout after 5 attempts)
3. Verify session expires after 1 hour

### Test CSRF Protection
1. Submit form normally (should work)
2. Try submitting without CSRF token (should fail)
3. Try submitting with invalid token (should fail)

### Test Rate Limiting
1. Submit contact form 4 times quickly (should work)
2. Submit 5th time immediately (should be blocked)
3. Wait 5 minutes and try again (should work)

## 🚨 Important Security Notes

1. **Never commit sensitive files**:
   - `passwords.txt` - Already in .gitignore
   - `submissions.txt` - Already in .gitignore
   - `rate_limit.txt` - Already in .gitignore
   - `password_rate_limit.txt` - Already in .gitignore

2. **Keep passwords.txt secure**:
   - Use strong, unique passwords
   - Change passwords regularly
   - Don't share passwords publicly

3. **Monitor submissions**:
   - Regularly check `submissions.txt` for suspicious activity
   - Review rate limit files for abuse patterns

4. **Update regularly**:
   - Keep PHP version updated
   - Monitor security advisories
   - Update dependencies if any are added

## 📞 Support

For security concerns:
- Email: richard@thebargain.com.ng
- Phone: +447927292051
