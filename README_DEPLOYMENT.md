# Deployment Instructions for The Bargain Website

## Files to Upload to cPanel

### Required Files:
1. `index.html` - Main website file
2. `contact.php` - Contact form handler
3. `.htaccess` - Security and configuration file
4. `thebargain.mp4` - Hero video
5. `thebargain2.mp4` - Film details video
6. `passwords.txt` - Password list (already protected by .htaccess)

### Files Created by PHP Script:
- `submissions.txt` - Stores form submissions (auto-created, protected)
- `rate_limit.txt` - Rate limiting data (auto-created, protected)

## cPanel Upload Steps

1. **Log into cPanel**
   - Access your cPanel dashboard

2. **Navigate to File Manager**
   - Go to Files → File Manager
   - Navigate to `public_html` (or your domain's root directory)

3. **Upload Files**
   - Upload all required files listed above
   - Make sure `index.html` is in the root directory
   - Ensure `contact.php` is in the same directory as `index.html`

4. **Set Permissions**
   - Right-click on the directory containing your files
   - Set permissions to `755` for directories
   - Set permissions to `644` for files
   - For `contact.php`, ensure it has execute permissions (`644` or `755`)

5. **Verify .htaccess**
   - Ensure `.htaccess` file is uploaded
   - This file protects sensitive files and configures security headers

## Email Configuration

The contact form uses PHP's `mail()` function. For best results:

1. **Configure cPanel Email**
   - Ensure email is set up in cPanel
   - The script uses `richard@thebargain.com.ng` as the admin email
   - Update `$admin_email` in `contact.php` if needed

2. **Test Email Functionality**
   - Submit a test form to verify emails are being sent
   - Check spam folder if emails don't arrive

3. **Alternative: Use SMTP (if mail() doesn't work)**
   - You may need to configure SMTP settings in `contact.php`
   - Contact your hosting provider for SMTP credentials

## Security Notes

- `submissions.txt` and `rate_limit.txt` are protected by `.htaccess`
- These files cannot be accessed via web browser
- Passwords are stored in `passwords.txt` (also protected)
- Rate limiting prevents spam (max 3 submissions per 5 minutes per IP)

## Testing

1. **Test the Contact Form**
   - Fill out and submit the form
   - Check that you receive the admin email
   - Check that the user receives confirmation email
   - Verify submission is stored in `submissions.txt`

2. **Test Password Protection**
   - Try accessing the investment page
   - Enter a password from `passwords.txt`
   - Verify access is granted

3. **Test Theme Toggle**
   - Click the theme toggle in navigation
   - Verify theme switches and persists

## Troubleshooting

### Emails Not Sending
- Check cPanel email configuration
- Verify PHP mail() function is enabled
- Check spam folders
- Consider using SMTP instead of mail()

### Form Not Submitting
- Check browser console for errors
- Verify `contact.php` is in correct location
- Check file permissions
- Verify PHP is enabled on your hosting

### Files Not Accessible
- Check file permissions (should be 644 for files, 755 for directories)
- Verify files are in correct directory
- Check `.htaccess` isn't blocking access

## Support

For issues or questions:
- Email: richard@thebargain.com.ng
- Phone: +447927292051
