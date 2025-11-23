# SplashReels

**AI-Powered Short-Form Video Creation SaaS Platform**

SplashReels is a complete multi-tenant SaaS platform for creating engaging short-form videos (TikTok, Reels, YouTube Shorts) from long-form content. Built with pure PHP and MySQL using a custom lightweight MVC architecture.

---

## Features

### Core Capabilities
- **AI Highlight Detection** (simulated) - Automatically identify engaging segments from long videos
- **Multi-Format Support** - Create clips for TikTok, Instagram Reels, YouTube Shorts
- **Project Management** - Organize content by campaigns, shows, or topics
- **Media Library** - Upload videos or import from YouTube URLs
- **Clip Editor** - Fine-tune start/end times, templates, and platforms
- **Brand Kits** - Consistent branding with custom colors, fonts, and logos
- **Template System** - Pre-built and custom templates for different platforms
- **Export & Download** - Generate final clips ready for publishing

### Multi-Tenant SaaS
- **Isolated Tenants** - Each customer has completely isolated data
- **Subscription Plans** - Free, Pro, and Agency tiers
- **Quota Management** - Processing minutes, exports, storage limits
- **Team Collaboration** - Multiple users per tenant with role-based access
- **Usage Tracking** - Monitor consumption and enforce limits

### User Roles
- **Platform Admin** - Manage all tenants and system settings
- **Tenant Admin** - Manage users, billing, and team settings
- **Editor** - Create projects, upload media, generate clips
- **Viewer** - View-only access to projects and clips

### Developer-Friendly
- **REST API** - Trigger clip generation programmatically
- **API Keys** - Per-tenant authentication with rate limiting
- **Webhook-Ready Architecture** - Easy to integrate external services
- **Extensible Pipeline** - Replace simulated AI with real processing

---

## Tech Stack

- **Backend**: PHP 7.0+ (compatible with PHP 7.x - 8.x)
- **Database**: MySQL 5.7+ with InnoDB
- **Architecture**: Custom lightweight MVC (no frameworks)
- **Frontend**: Vanilla JavaScript, HTML5, CSS3
- **Security**: CSRF protection, password hashing, SQL injection prevention

### Why No Framework?
SplashReels uses a custom MVC architecture to:
- Maximize compatibility (PHP 7.0+)
- Minimize dependencies and overhead
- Provide clear, beginner-friendly code
- Enable easy customization and learning

---

## Requirements

### System Requirements
- PHP 7.0 or higher (tested up to PHP 8.x)
- MySQL 5.7 or higher
- Apache or Nginx web server
- 500MB+ disk space

### PHP Extensions
- `pdo_mysql` - Database connectivity
- `mbstring` - String functions
- `json` - JSON handling
- `openssl` - Secure operations
- `fileinfo` - File type detection

---

## Installation

### 1. Clone or Download

```bash
git clone https://github.com/yourusername/splashreels.git
cd splashreels
```

### 2. Configure Environment

```bash
cp .env.example .env
```

Edit `.env` with your database credentials:

```env
DB_HOST=localhost
DB_NAME=splashreels
DB_USER=your_db_user
DB_PASS=your_db_password
```

### 3. Create Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE splashreels CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'splashreels_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON splashreels.* TO 'splashreels_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Import Database Schema

```bash
mysql -u splashreels_user -p splashreels < database.sql
```

This will create all tables and insert seed data including:
- 3 subscription plans (Free, Pro, Agency)
- Platform admin user
- Demo tenant with users and sample data
- Global clip templates

### 5. Set File Permissions

```bash
chmod -R 755 storage/
chmod -R 755 public/assets/
```

### 6. Configure Web Server

#### Apache (.htaccess included)

Update your virtual host or `.conf` file:

```apache
<VirtualHost *:80>
    ServerName splashreels.local
    DocumentRoot /path/to/splashreels/public

    <Directory /path/to/splashreels/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/splashreels_error.log
    CustomLog ${APACHE_LOG_DIR}/splashreels_access.log combined
</VirtualHost>
```

Enable mod_rewrite:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx

```nginx
server {
    listen 80;
    server_name splashreels.local;
    root /path/to/splashreels/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 7. Access the Application

Visit: `http://splashreels.local` (or your configured domain)

---

## Default Credentials

### Platform Admin
- **Email**: `admin@splashreels.com`
- **Password**: `password`

### Demo Tenant Admin
- **Email**: `admin@demo-agency.com`
- **Password**: `password`

### Demo Editor
- **Email**: `editor@demo-agency.com`
- **Password**: `password`

### Demo Viewer
- **Email**: `viewer@demo-agency.com`
- **Password**: `password`

**Important**: Change these passwords immediately in production!

---

## Quick Start Guide

### 1. Create a Project
1. Login as tenant admin or editor
2. Navigate to Projects → New Project
3. Enter project name and description
4. Click "Create Project"

### 2. Upload Media
1. Go to Media → Upload Media
2. Select your project
3. Upload a video file or paste YouTube URL
4. Add title and description
5. Click "Upload"

### 3. Generate AI Highlights
1. View your uploaded media file
2. Click "Generate AI Highlights"
3. Wait for clip suggestions to appear
4. Review suggested clips with timestamps

### 4. Create & Edit Clips
1. Accept a clip suggestion or create manually
2. Adjust start/end times
3. Select template and platform
4. Click "Render Clip"

### 5. Export & Download
1. Once clip is "Ready"
2. Click "Export"
3. Download the final video file

---

## API Documentation

### Authentication

Include your API key in the header:

```
X-API-KEY: sk_live_your_api_key_here
```

Get your API key from: **Billing → API Keys**

### Endpoints

#### Create Project
```http
POST /api/v1/projects/create
Content-Type: application/json

{
  "name": "My New Project",
  "description": "Optional description"
}
```

#### Add Media from URL
```http
POST /api/v1/media/create
Content-Type: application/json

{
  "project_id": 123,
  "title": "My Video",
  "source_type": "youtube_url",
  "source_url": "https://youtube.com/watch?v=..."
}
```

#### Generate AI Highlights
```http
POST /api/v1/media/{media_id}/generate-clips
```

#### Get Project Clips
```http
GET /api/v1/projects/{project_id}/clips
```

#### Get Clip Details
```http
GET /api/v1/clips/{clip_id}
```

### Rate Limiting
Default: 60 requests per minute per API key (configurable per key)

---

## Project Structure

```
splashreels/
├── app/
│   ├── core/              # MVC framework classes
│   │   ├── Router.php
│   │   ├── Controller.php
│   │   ├── Model.php
│   │   ├── View.php
│   │   ├── Database.php
│   │   ├── Auth.php
│   │   ├── Session.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   └── CSRF.php
│   ├── controllers/       # Application controllers
│   ├── models/            # Data models
│   ├── views/             # HTML templates
│   └── helpers/           # Utility classes
│       ├── ValidationHelper.php
│       ├── FileUploadHelper.php
│       ├── VideoPipelineHelper.php
│       ├── UsageHelper.php
│       └── ...
├── config/
│   ├── config.php         # App configuration
│   └── routes.php         # Route definitions
├── public/
│   ├── index.php          # Entry point
│   ├── .htaccess          # Apache rewrite rules
│   └── assets/            # CSS, JS, images
├── storage/
│   ├── uploads/           # Media files
│   └── logs/              # Application logs
├── tests/                 # Test suite
├── database.sql           # Database schema + seed data
├── .env.example           # Environment template
└── README.md              # This file
```

---

## Running Tests

Execute tests from the `/tests` directory:

```bash
cd tests

# Test database connection
php test_db_connection.php

# Test authentication
php test_auth_login.php

# Test project creation
php test_create_project.php

# Test media upload (stubbed)
php test_upload_media_stub.php

# Test AI clip generation
php test_generate_clip_suggestions.php

# Test clip creation
php test_create_clip.php

# Test quota enforcement
php test_quota_enforcement.php

# Test API key authentication
php test_api_key_auth.php

# Test tenant isolation
php test_tenant_isolation.php
```

Expected output for each test:
```
OK - [test description]
```

---

## Architecture Overview

### MVC Pattern
- **Models**: Data access and business logic
- **Views**: HTML templates with embedded PHP
- **Controllers**: Request handling and response generation
- **Router**: Maps URLs to controller actions

### Multi-Tenancy
- **Single Database**: All tenants share one database
- **Tenant Isolation**: Every query filtered by `tenant_id`
- **Row-Level Security**: Models enforce tenant scoping
- **Session-Based Auth**: User sessions include tenant context

### Simulated AI Pipeline
The video processing pipeline is currently simulated for demonstration. Replace these helpers with real services:

- **VideoPipelineHelper::generateTranscript()** → Integrate Whisper, AssemblyAI, etc.
- **VideoPipelineHelper::generateClipSuggestions()** → Use ML models for highlight detection
- **VideoPipelineHelper::renderClip()** → Integrate FFmpeg for actual rendering
- **VideoPipelineHelper::exportClip()** → Format conversion with FFmpeg

### Scalability Considerations

**Current Architecture** (Single Server):
- PHP application server
- MySQL database
- Local file storage

**Production Scaling Recommendations**:

1. **Offload Video Processing**
   - Use job queues (Redis/RabbitMQ)
   - Separate worker servers for FFmpeg
   - Consider cloud transcoding (AWS Elastic Transcoder, Cloudflare Stream)

2. **Object Storage**
   - Move uploads to S3/CloudFlare R2/DigitalOcean Spaces
   - Use CDN for static assets and downloads

3. **Database Optimization**
   - Add read replicas for reporting
   - Consider caching layer (Redis/Memcached)
   - Index optimization for large datasets

4. **Microservices Split** (if needed):
   - PHP SaaS Core (web app + API)
   - Media Processing Service (Node.js/Python + FFmpeg)
   - Transcription Service (Python + Whisper)
   - AI Highlights Service (Python + ML models)

---

## Security Best Practices

### Implemented Security Features
✅ Password hashing with `password_hash()`
✅ CSRF token validation on all forms
✅ SQL injection prevention (PDO prepared statements)
✅ XSS prevention (output escaping)
✅ Session fixation protection
✅ Login brute-force mitigation
✅ Tenant isolation (row-level security)
✅ Role-based access control
✅ Secure file uploads with MIME validation

### Additional Recommendations for Production
- Enable HTTPS (Let's Encrypt)
- Set secure cookie flags in production
- Implement rate limiting on login endpoints
- Add two-factor authentication (2FA)
- Regular security audits and penetration testing
- Set up logging and monitoring (Sentry, LogRocket)
- Regular database backups
- Implement CORS policies for API

---

## Deployment Checklist

- [ ] Update all default passwords
- [ ] Configure `.env` with production database
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Enable HTTPS and force SSL
- [ ] Configure backup strategy
- [ ] Set up error logging (not display)
- [ ] Configure file upload limits
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Enable PHP OPcache
- [ ] Configure CRON jobs (if needed)
- [ ] Set up monitoring and alerts
- [ ] Review and harden web server config
- [ ] Implement CDN for static assets

---

## Extending the Platform

### Adding Real Video Processing

Replace the simulated pipeline with FFmpeg:

```php
// In VideoPipelineHelper.php

public static function renderClip($clipId) {
    $clip = // ... fetch clip data

    $inputFile = $clip['media_file_path'];
    $outputFile = "/storage/uploads/clips/{$clip['tenant_id']}/clip_{$clipId}.mp4";

    $cmd = "ffmpeg -i {$inputFile} " .
           "-ss {$clip['start_seconds']} " .
           "-t {$clip['duration_seconds']} " .
           "-vf 'scale=1080:1920' " .
           "-c:v libx264 -c:a aac " .
           "{$outputFile}";

    exec($cmd, $output, $returnCode);

    // Update clip status, handle errors
}
```

### Adding Real AI Transcription

Integrate Whisper or AssemblyAI:

```php
// In VideoPipelineHelper.php

public static function generateTranscript($mediaFileId) {
    $mediaFile = // ... fetch media

    // Using AssemblyAI
    $apiKey = getenv('ASSEMBLYAI_API_KEY');
    $url = "https://api.assemblyai.com/v2/transcript";

    $data = json_encode(['audio_url' => $mediaFile['source_url']]);

    // Send request, poll for completion
    // Store transcript in database
}
```

---

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

This project is open-source and available under the MIT License.

---

## Support

For issues, questions, or feature requests:
- Open an issue on GitHub
- Email: support@splashreels.com (if applicable)

---

## Roadmap

### Phase 1 (Current) - MVP
✅ Multi-tenant architecture
✅ Project & media management
✅ Simulated AI pipeline
✅ Basic clip editor
✅ REST API
✅ Subscription & quota management

### Phase 2 - Real Processing
- [ ] FFmpeg integration for clip rendering
- [ ] Whisper integration for transcription
- [ ] ML-based highlight detection
- [ ] Automated caption generation
- [ ] Social media posting (OAuth integrations)

### Phase 3 - Advanced Features
- [ ] Real-time collaboration
- [ ] Advanced template editor
- [ ] Custom fonts and animations
- [ ] A/B testing for clips
- [ ] Analytics dashboard
- [ ] Mobile app (React Native)

---

## Credits

Built with ❤️ by the SplashReels team.

Special thanks to the PHP and open-source communities.

---

**Ready to create amazing short-form content? Start with SplashReels today!**
