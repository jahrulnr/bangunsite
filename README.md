# BangunSite

A web-based server management tool with integrated Docker container management, and SSL configuration capabilities.

## Features

- 🔒 SSL/TLS Management
- 🐳 Docker Container Management
- 📁 File Manager
- 🔄 Reverse Proxy Support

## System Requirements

- Docker
- Docker Compose

### Port Requirements

#### Development Environment
- 7080 (HTTP)
- 7443 (HTTPS)
- 8080 (Web Management)

#### Production Environment
- 80 (HTTP)
- 443 (HTTPS)
- 8080 (Web Management)

## Quick Start

1. Clone this repository:
```bash
git clone git@github.com:jahrulnr/bangunsite.git
cd bangunsite
```

2. Start the services:
```bash
make up-vm
```

3. Initialize the database (first-time setup only):
```bash
docker exec bangunsite artisan db:seed
```

4. Open your browser and navigate to `https://localhost:8080/`
Then log in using:
- Username: `admin@demo.com`
- Password: `123456`

## Project Structure

```
.
├── config/               # Configuration files
│   ├── nginx/            # Nginx configurations
│   ├── php/              # PHP configurations
│   ├── webconfig/        # Web server configurations
│   └── supervisord.conf  # Supervisor configuration
├── proxy/                # Reverse proxy service
├── web/                  # Main web application
├── compose.yml           # Docker compose configuration
└── Dockerfile            # Main container configuration
```

## Components

### Web Management Interface
The main web interface for managing server configurations, containers, and files. Built with Laravel and AdminLTE.

### Reverse Proxy
A Go-based reverse proxy service handling SSL termination and request forwarding.

### Mail Server
Includes MailCatcher for email testing and development.

## Development

For development, the following directories are mounted as volumes:
- `./web/app`
- `./web/database`
- `./web/public`
- `./web/resources`
- `./web/routes`

This allows for real-time code changes without rebuilding the container.

## Configuration

### SSL Certificates
Default self-signed certificates are automatically generated on first run. For production, replace with valid certificates in:
```
data/webconfig/ssl/live/default/
```

### Nginx Configuration
Custom Nginx configurations can be added to:
```
data/webconfig/site.d/
```

### PHP Configuration
PHP configurations can be modified in:
```
data/php/php.ini
```

## License

This project is licensed under the [MIT License](LICENSE).

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request