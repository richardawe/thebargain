# Quick Start Guide - GitHub to cPanel Deployment

## 🚀 Quick Setup (5 minutes)

### Step 1: Push Code to GitHub

Run the setup script:
```bash
cd /Users/3d7tech/thebargain.ng
./setup-git.sh
```

Or manually:
```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/richardawe/thebargain.git
git push -u origin main
```

### Step 2: Get FTP Credentials from cPanel

1. Login to cPanel
2. Go to **Files → FTP Accounts**
3. Create new FTP account or use existing
4. Note down:
   - **FTP Server**: Usually `ftp.thebargain.com.ng` or your server IP
   - **FTP Username**: e.g., `thebargain@thebargain.com.ng`
   - **FTP Password**: The password you set
   - **Directory**: Usually `/public_html`

### Step 3: Add GitHub Secrets

1. Go to: https://github.com/richardawe/thebargain/settings/secrets/actions
2. Click **New repository secret** for each:

   **FTP_SERVER**
   ```
   ftp.thebargain.com.ng
   ```

   **FTP_USERNAME**
   ```
   thebargain@thebargain.com.ng
   ```

   **FTP_PASSWORD**
   ```
   YourFTPPasswordHere
   ```

   **FTP_DIRECTORY**
   ```
   /public_html
   ```

### Step 4: Test Deployment

1. Make a small change to any file
2. Commit and push:
   ```bash
   git add .
   git commit -m "Test deployment"
   git push
   ```
3. Go to **Actions** tab in GitHub
4. Watch the deployment run
5. Check https://thebargain.com.ng - should be updated!

## ✅ That's It!

Every time you push to `main` branch, your site will automatically update.

## 🔧 Troubleshooting

**Deployment fails?**
- Check FTP credentials are correct
- Verify FTP directory path (usually `/public_html`)
- Check GitHub Actions logs for specific errors

**Files not updating?**
- Clear browser cache (Ctrl+F5)
- Check file timestamps in cPanel File Manager
- Verify workflow completed successfully

## 📚 More Details

See [DEPLOYMENT_SETUP.md](./DEPLOYMENT_SETUP.md) for detailed instructions.
