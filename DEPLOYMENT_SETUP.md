# GitHub Actions Deployment Setup Guide

This guide will help you set up automatic deployment from GitHub to your cPanel hosting at thebargain.com.ng.

## Prerequisites

1. GitHub repository: https://github.com/richardawe/thebargain
2. cPanel hosting with FTP access
3. FTP credentials from your hosting provider

## Step 1: Get FTP Credentials from cPanel

1. Log into your cPanel account
2. Navigate to **Files → FTP Accounts** or **FTP Accounts**
3. Create a new FTP account (or use existing):
   - **Username**: (e.g., `thebargain@thebargain.com.ng`)
   - **Password**: (create a strong password)
   - **Directory**: `/public_html` (or your domain's root directory)
4. Note down:
   - FTP Server/Hostname (usually `ftp.thebargain.com.ng` or your server IP)
   - FTP Username
   - FTP Password
   - Directory path (usually `/public_html`)

## Step 2: Add GitHub Secrets

1. Go to your GitHub repository: https://github.com/richardawe/thebargain
2. Click on **Settings** (top menu)
3. In the left sidebar, click **Secrets and variables → Actions**
4. Click **New repository secret** and add the following secrets:

### Required Secrets:

#### `FTP_SERVER`
- **Value**: Your FTP server hostname
- **Example**: `ftp.thebargain.com.ng` or `thebargain.com.ng` or IP address
- **How to find**: Check your cPanel FTP Accounts section

#### `FTP_USERNAME`
- **Value**: Your FTP username
- **Example**: `thebargain@thebargain.com.ng`
- **How to find**: From the FTP account you created in cPanel

#### `FTP_PASSWORD`
- **Value**: Your FTP password
- **Example**: `YourSecurePassword123!`
- **How to find**: The password you set when creating the FTP account

#### `FTP_DIRECTORY`
- **Value**: The directory path on the server
- **Example**: `/public_html` or `/home/username/public_html`
- **How to find**: 
  - Usually `/public_html` for main domain
  - Or `/home/yourusername/public_html`
  - Check in cPanel → File Manager → see the path at the top

## Step 3: Push Code to GitHub

If you haven't already, initialize git and push your code:

```bash
# Navigate to your project directory
cd /Users/3d7tech/thebargain.ng

# Initialize git (if not already done)
git init

# Add all files
git add .

# Commit
git commit -m "Initial commit - The Bargain website"

# Add remote repository
git remote add origin https://github.com/richardawe/thebargain.git

# Push to GitHub
git branch -M main
git push -u origin main
```

## Step 4: Verify Deployment

1. After pushing to GitHub, go to your repository
2. Click on the **Actions** tab
3. You should see a workflow run starting
4. Click on it to see the deployment progress
5. Once complete (green checkmark), your site should be updated

## Step 5: Test Your Website

1. Visit https://thebargain.com.ng
2. Verify all files are deployed correctly
3. Test the contact form
4. Check that videos are loading

## How It Works

- **Trigger**: Every time you push to the `main` branch, GitHub Actions automatically runs
- **Process**: 
  1. Checks out your code
  2. Connects to your cPanel via FTP
  3. Uploads all files (except excluded ones)
  4. Your website is updated!

## Manual Deployment

You can also trigger deployment manually:
1. Go to **Actions** tab in GitHub
2. Select **Deploy to cPanel** workflow
3. Click **Run workflow** button
4. Select branch and click **Run workflow**

## Troubleshooting

### Deployment Fails

1. **Check FTP Credentials**
   - Verify all secrets are correct
   - Test FTP connection manually using an FTP client

2. **Check FTP Directory**
   - Ensure `FTP_DIRECTORY` is correct
   - Usually `/public_html` for main domain
   - For subdomains, might be `/public_html/subdomain`

3. **Check File Permissions**
   - After deployment, check file permissions in cPanel
   - Files should be `644`, directories `755`

4. **Check GitHub Actions Logs**
   - Go to Actions tab → Click on failed workflow
   - Check the error messages
   - Common issues:
     - Wrong FTP server/hostname
     - Incorrect directory path
     - FTP account doesn't have write permissions

### Files Not Updating

1. **Clear Browser Cache**
   - Hard refresh (Ctrl+F5 or Cmd+Shift+R)

2. **Check .htaccess**
   - The workflow uploads .htaccess separately
   - Verify it's in the root directory

3. **Verify File Upload**
   - Check cPanel File Manager
   - Verify files were uploaded with correct timestamps

## Security Notes

- **Never commit sensitive files**:
  - `passwords.txt` - Already in .gitignore
  - `submissions.txt` - Already in .gitignore
  - `rate_limit.txt` - Already in .gitignore
  
- **FTP Credentials**:
  - Stored securely in GitHub Secrets
  - Never visible in code or logs
  - Only accessible to repository collaborators with proper permissions

## Alternative: SSH Deployment (If Available)

If your cPanel has SSH access enabled, you can use a more secure SSH-based deployment. Let me know if you'd like to set that up instead.

## Support

If you encounter issues:
- Check GitHub Actions logs for error messages
- Verify FTP credentials in cPanel
- Contact your hosting provider for FTP/SSH access details
