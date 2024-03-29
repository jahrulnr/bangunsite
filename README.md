![dashboard](/screenshots/dashboard.png)

# BangunSite

This project is a BangunSite built using Laravel, Docker, Nginx, Certbot, and PHP 8.2. It provides a convenient way to manage multiple web applications within a Dockerized environment. Laravel is utilized for its powerful features in managing web applications, while Docker ensures portability and consistency across different environments.

## Features

- **Laravel Integration**: Utilizes Laravel for managing web applications and provides a convenient interface for tasks such as database migrations, seeding, and running artisan commands.
  
- **Dockerized Environment**: Runs on Docker for easy deployment and scalability. Each component, including Nginx, Certbot, and PHP 8.2, is containerized for isolation and portability.

- **Nginx Server**: Acts as a server for web applications created using Laravel. Nginx handles incoming HTTP requests and forwards them to the appropriate Laravel application.

- **Certbot Integration**: Includes Certbot for automating the process of obtaining and renewing SSL certificates. This ensures secure communication between clients and the web server.

- **Cronjobs Integration**: With this feature, you can define and manage cronjobs directly from the web interface. This includes scheduling recurring tasks and executing commands.

- **PHP 8.2 Support**: Runs Laravel applications specifically on PHP 8.2, leveraging the latest features and improvements in the PHP language.

## Getting Started

### Prerequisites

- Docker
- Docker Compose

### Installation

1. Clone this repository to your local machine:

    ```bash
    git clone --branch development https://github.com/jahrulnr/bangunsite.git

2. Build and start the Docker containers:

    ```bash
    make install
    ```

4. Access the Laravel BangunSite at `http://localhost:8080` in your web browser.

### Default Account

email: ```admin@demo.com```\
password: ```123456```

### Configuration

You can change .env configuration at ```./infra/.env``` (See docker-compose.yml for details)

## Contributing

Contributions are welcome! Feel free to open issues or submit pull requests to improve this project.

## License

This project is licensed under the [MIT License](LICENSE).