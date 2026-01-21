# The Bargain - Faith-Based Thriller Film Website

Official website for "The Bargain" - A faith-based thriller film by Imoh Umoren and Richard Awe.

**Website**: https://thebargain.com.ng

## Features

- 🎬 Modern, bright, and interactive design
- 🌓 Light/Dark mode toggle
- 📧 Contact form with email notifications
- 🔒 Password-protected investment page
- 📱 Fully responsive design
- 🎥 Video backgrounds and trailers
- ⚡ Fast loading and optimized

## Tech Stack

- HTML5
- CSS3 (with CSS Variables for theming)
- JavaScript (Vanilla JS)
- PHP (Contact form handler)

## Project Structure

```
thebargain.ng/
├── index.html              # Main website file
├── contact.php             # Contact form handler
├── .htaccess              # Security and server configuration
├── .gitignore             # Git ignore rules
├── passwords.txt          # Investment page passwords (not in git)
├── thebargain.mp4         # Hero section video
├── thebargain2.mp4        # Film details video
├── .github/
│   └── workflows/
│       └── deploy.yml     # GitHub Actions deployment workflow
└── README.md              # This file
```

## Deployment

This project uses GitHub Actions for automatic deployment to cPanel.

### Automatic Deployment

Every push to the `main` branch automatically deploys to https://thebargain.com.ng

### Manual Deployment

1. Go to GitHub Actions tab
2. Select "Deploy to cPanel" workflow
3. Click "Run workflow"

See [DEPLOYMENT_SETUP.md](./DEPLOYMENT_SETUP.md) for detailed setup instructions.

## Local Development

1. Clone the repository:
```bash
git clone https://github.com/richardawe/thebargain.git
cd thebargain
```

2. Open `index.html` in a web browser or use a local server:
```bash
# Using Python
python3 -m http.server 8000

# Using PHP
php -S localhost:8000
```

3. For contact form testing, you'll need PHP enabled.

## Contact

- **Email**: richard@thebargain.com.ng
- **Phone**: +447927292051
- **Website**: https://thebargain.com.ng

## License

All rights reserved © 2024 The Bargain
