# Security Audit Report - The Bargain Website
**Date**: 2024  
**Auditor**: Automated Security Audit  
**Scope**: Complete codebase review

---

## Executive Summary

This audit identified **15 security issues** ranging from **Critical** to **Low** severity. The most critical issues are:

1. **CSRF Protection Not Implemented** (Critical)
2. **No Rate Limiting on Contact Form** (High)
3. **Sensitive Files Present in Repository** (High)
4. **Weak Password Storage** (High)
5. **Email Injection Vulnerabilities** (Medium)

---

## 🔴 CRITICAL ISSUES

### 1. CSRF Protection Not Implemented
**Severity**: Critical  
**File**: `contact.php`, `index.html`  
**Description**: 
- CSRF token is generated and loaded in JavaScript (`get-csrf-token.php` is called)
- However, the token is **never added to the form** and **never validated** in `contact.php`
- The contact form has no CSRF input field
- `contact.php` does not check for or validate CSRF tokens

**Impact**: 
- Contact form is vulnerable to Cross-Site Request Forgery attacks
- Attackers can submit forms on behalf of users
- Can lead to spam, data manipulation, or abuse

**Evidence**:
- `index.html` line 2808-2834: CSRF token is loaded but not used
- `index.html` line 2619-2654: Contact form has no CSRF input field
- `contact.php`: No CSRF validation code present

**Recommendation**:
```php
// In contact.php, add after session_start():
session_start();
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token']);
    exit;
}
```

```html
<!-- In index.html contact form, add hidden field: -->
<input type="hidden" name="csrf_token" id="csrf_token" value="">
```

```javascript
// In form submission, ensure token is included:
formData.append('csrf_token', csrfToken);
```

---

## 🟠 HIGH SEVERITY ISSUES

### 2. No Rate Limiting on Contact Form
**Severity**: High  
**File**: `contact.php`  
**Description**: 
- Contact form has no rate limiting mechanism
- Password validation has rate limiting (5 attempts per 15 minutes)
- Contact form can be spammed indefinitely

**Impact**:
- Email inbox flooding
- Server resource exhaustion
- Potential DoS attack vector
- Spam submissions

**Recommendation**:
Implement rate limiting similar to `validate-password.php`:
```php
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = 'rate_limit.txt';
$max_attempts = 5;
$lockout_time = 300; // 5 minutes

// Check and update rate limiting
// ... (similar to validate-password.php)
```

---

### 3. Sensitive Files Present in Repository
**Severity**: High  
**Files**: `passwords.txt`, `submissions.txt`, `rate_limit.txt`, `password_rate_limit.txt`  
**Description**: 
- Sensitive files are present in the working directory
- While `.gitignore` excludes them, they exist on the server
- If `.htaccess` fails or is misconfigured, these files could be exposed

**Impact**:
- Password file could be downloaded if protection fails
- Submission data could be exposed
- Rate limit data could reveal attack patterns

**Recommendation**:
1. Move sensitive files outside web root if possible
2. Add additional protection layers
3. Implement file integrity monitoring
4. Consider using environment variables or secure storage

---

### 4. Weak Password Storage
**Severity**: High  
**File**: `passwords.txt`  
**Description**: 
- Passwords stored in plain text file
- No hashing or encryption
- File contains default passwords that are weak

**Impact**:
- If file is compromised, all passwords are immediately usable
- Default passwords are predictable
- No password rotation mechanism

**Evidence**:
```php
// validate-password.php lines 94-105
$valid_passwords = [
    'thebargain2024',
    'partner2024',
    'invest2024',
    // ... more weak passwords
];
```

**Recommendation**:
1. Use password hashing (bcrypt/argon2)
2. Store hashed passwords in database or secure file
3. Implement password complexity requirements
4. Remove default passwords
5. Add password rotation policy

---

### 5. Email Header Injection Vulnerability
**Severity**: Medium-High  
**File**: `contact.php`  
**Description**: 
- Email headers use user-supplied data
- While `filter_var($email, FILTER_SANITIZE_EMAIL)` is used for Reply-To, other headers could be vulnerable
- Newline injection possible in email body

**Impact**:
- Email header injection attacks
- Spam relay attacks
- Email spoofing

**Current Code** (lines 50-52):
```php
$headers = "From: noreply@thebargain.com.ng\r\n";
$headers .= "Reply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
```

**Recommendation**:
```php
// Validate email format strictly
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Remove any newlines from email
$email = str_replace(["\r", "\n"], '', $email);

// Use additional validation
$headers = "From: noreply@thebargain.com.ng\r\n";
$headers .= "Reply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
```

---

## 🟡 MEDIUM SEVERITY ISSUES

### 6. Insufficient Input Validation
**Severity**: Medium  
**File**: `contact.php`  
**Description**: 
- Only basic empty checks performed
- No length validation
- No format validation for phone numbers
- No sanitization of investment field

**Impact**:
- Potential buffer overflow (though PHP handles this)
- Invalid data in emails
- Potential XSS if email is displayed elsewhere

**Recommendation**:
```php
// Add comprehensive validation
if (strlen($name) > 100 || strlen($email) > 255) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Input too long']);
    exit;
}

// Validate phone format
if (!empty($phone) && !preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid phone format']);
    exit;
}

// Validate investment field
$allowed_investments = ['minimum', 'associate', 'executive', 'other'];
if (!empty($investment) && !in_array($investment, $allowed_investments)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid investment option']);
    exit;
}
```

---

### 7. Session Security Could Be Improved
**Severity**: Medium  
**Files**: `check-auth.php`, `validate-password.php`, `get-csrf-token.php`  
**Description**: 
- Session configuration is good but missing some security features
- No session regeneration on login
- Session timeout is fixed (1 hour) but not configurable

**Current Configuration**:
```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
```

**Recommendation**:
```php
// Add session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict'); // Change from Lax to Strict
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 1); // Require HTTPS (enable when SSL is active)
ini_set('session.gc_maxlifetime', 3600); // 1 hour

// Regenerate session ID on successful login
session_regenerate_id(true);
```

---

### 8. Error Information Disclosure
**Severity**: Medium  
**File**: `contact.php`  
**Description**: 
- Error messages could reveal system information
- Exception handling swallows errors silently
- No logging of failed attempts

**Current Code** (lines 55-60):
```php
try {
    $sent = @mail($to, $subject, $body, $headers);
} catch (Exception $e) {
    $sent = false;
}
```

**Impact**:
- Errors are silently ignored
- No way to debug email issues
- Potential information leakage through error messages

**Recommendation**:
```php
try {
    $sent = @mail($to, $subject, $body, $headers);
    if (!$sent) {
        error_log("Failed to send email to: $to from: $email");
    }
} catch (Exception $e) {
    error_log("Email exception: " . $e->getMessage());
    $sent = false;
}
```

---

### 9. No HTTPS Enforcement
**Severity**: Medium  
**File**: `.htaccess`  
**Description**: 
- HTTPS redirect is commented out
- HSTS header is commented out
- No secure cookie flags

**Impact**:
- Data transmitted in plain text
- Session hijacking possible
- Man-in-the-middle attacks

**Recommendation**:
1. Enable SSL certificate
2. Uncomment HTTPS redirect in `.htaccess`
3. Uncomment HSTS header
4. Enable `session.cookie_secure` in PHP

---

### 10. Rate Limit File Race Condition
**Severity**: Medium  
**File**: `validate-password.php`  
**Description**: 
- Rate limit file is read, modified, and written without locking
- Race condition could allow bypassing rate limits
- Multiple concurrent requests could overwrite each other's data

**Current Code** (lines 37-64):
```php
if (file_exists($rate_limit_file)) {
    $rate_data = json_decode(file_get_contents($rate_limit_file), true);
    // ... modifications ...
}
file_put_contents($rate_limit_file, json_encode($rate_data));
```

**Recommendation**:
```php
// Use file locking
$fp = fopen($rate_limit_file, 'c+');
if (flock($fp, LOCK_EX)) {
    $rate_data = json_decode(file_get_contents($rate_limit_file), true) ?: [];
    // ... modifications ...
    ftruncate($fp, 0);
    fwrite($fp, json_encode($rate_data));
    flock($fp, LOCK_UN);
}
fclose($fp);
```

---

## 🟢 LOW SEVERITY ISSUES

### 11. Missing Content Security Policy for Inline Scripts
**Severity**: Low  
**File**: `.htaccess`  
**Description**: 
- CSP allows `'unsafe-inline'` for scripts
- This reduces effectiveness of CSP

**Recommendation**:
- Move inline scripts to external files
- Use nonces for inline scripts
- Implement strict CSP

---

### 12. No reCAPTCHA Implementation
**Severity**: Low  
**Files**: `index.html`, `contact.php`  
**Description**: 
- reCAPTCHA is mentioned in documentation but not implemented
- Code is commented out

**Impact**:
- Increased vulnerability to automated spam
- Bot submissions possible

**Recommendation**:
- Implement reCAPTCHA v3 as documented in `SECURITY_SETUP.md`

---

### 13. IP Address Spoofing Vulnerability
**Severity**: Low  
**File**: `validate-password.php`, `contact.php`  
**Description**: 
- Rate limiting uses `$_SERVER['REMOTE_ADDR']`
- Can be bypassed if behind proxy without proper configuration
- X-Forwarded-For header not validated

**Recommendation**:
```php
function getClientIP() {
    $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
```

---

### 14. Missing Security Headers
**Severity**: Low  
**File**: `.htaccess`  
**Description**: 
- Some modern security headers are missing
- Could improve overall security posture

**Recommendation**:
```apache
Header set X-Permitted-Cross-Domain-Policies "none"
Header set Cross-Origin-Embedder-Policy "require-corp"
Header set Cross-Origin-Opener-Policy "same-origin"
Header set Cross-Origin-Resource-Policy "same-origin"
```

---

### 15. No Logging/Monitoring
**Severity**: Low  
**Files**: All PHP files  
**Description**: 
- No centralized logging system
- No monitoring for suspicious activity
- No alerting mechanism

**Recommendation**:
- Implement structured logging
- Log all authentication attempts
- Log all form submissions
- Set up monitoring and alerting

---

## 📊 Security Score Summary

| Category | Score | Status |
|----------|-------|--------|
| Authentication | 6/10 | ⚠️ Needs Improvement |
| Authorization | 7/10 | ⚠️ Needs Improvement |
| Input Validation | 5/10 | ⚠️ Needs Improvement |
| Output Encoding | 8/10 | ✅ Good |
| Session Management | 7/10 | ⚠️ Needs Improvement |
| Error Handling | 6/10 | ⚠️ Needs Improvement |
| Cryptography | 3/10 | 🔴 Poor |
| Security Headers | 7/10 | ⚠️ Needs Improvement |
| Rate Limiting | 5/10 | ⚠️ Needs Improvement |
| Logging | 2/10 | 🔴 Poor |

**Overall Security Score: 5.6/10** ⚠️

---

## ✅ Positive Security Features

1. ✅ **Good Session Configuration**: HTTP-only cookies, SameSite protection
2. ✅ **Input Sanitization**: Using `htmlspecialchars()` for output
3. ✅ **Security Headers**: Basic security headers implemented
4. ✅ **File Protection**: `.htaccess` protects sensitive files
5. ✅ **Password Rate Limiting**: Implemented for password attempts
6. ✅ **Method Validation**: Only POST requests accepted
7. ✅ **Error Reporting**: Errors logged, not displayed

---

## 🔧 Immediate Action Items (Priority Order)

### Priority 1 (Critical - Fix Immediately)
1. ✅ Implement CSRF protection in contact form
2. ✅ Add rate limiting to contact form
3. ✅ Move or better protect sensitive files

### Priority 2 (High - Fix This Week)
4. ✅ Implement password hashing
5. ✅ Fix email header injection
6. ✅ Add comprehensive input validation

### Priority 3 (Medium - Fix This Month)
7. ✅ Enable HTTPS and HSTS
8. ✅ Fix rate limit race condition
9. ✅ Improve error handling and logging
10. ✅ Enhance session security

### Priority 4 (Low - Fix When Possible)
11. ✅ Implement reCAPTCHA
12. ✅ Improve CSP
13. ✅ Add monitoring and alerting
14. ✅ Add additional security headers

---

## 📝 Code Quality Issues

### General Issues
1. **Inconsistent Error Handling**: Some functions return success even on failure
2. **Magic Numbers**: Hard-coded timeouts and limits should be constants
3. **Code Duplication**: Rate limiting logic could be extracted to a function
4. **Missing Documentation**: Some security functions lack comments

### Recommendations
```php
// Define constants at top of file
define('SESSION_TIMEOUT', 3600);
define('MAX_PASSWORD_ATTEMPTS', 5);
define('PASSWORD_LOCKOUT_TIME', 900);
define('MAX_CONTACT_SUBMISSIONS', 5);
define('CONTACT_LOCKOUT_TIME', 300);
```

---

## 🧪 Testing Recommendations

1. **Penetration Testing**:
   - Test CSRF protection
   - Test rate limiting bypass
   - Test input validation
   - Test session management

2. **Automated Security Scanning**:
   - Use OWASP ZAP
   - Use Burp Suite
   - Run PHP security linters

3. **Code Review**:
   - Review all user inputs
   - Review all file operations
   - Review all database operations (if any added)

---

## 📚 References

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [OWASP CSRF Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [OWASP Session Management](https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html)

---

## 📞 Contact

For questions about this audit:
- Review the codebase security documentation
- Consult with security professionals
- Implement fixes in priority order

---

**Report Generated**: 2024  
**Next Review**: After implementing Priority 1 fixes
