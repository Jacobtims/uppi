# Uppi

[![GitHub release (latest by date)](https://img.shields.io/github/v/release/janyksteenbeek/uppi)](https://github.com/janyksteenbeek/uppi/releases)
[![GitHub](https://img.shields.io/github/license/janyksteenbeek/uppi)](LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/janyksteenbeek/uppi)](https://github.com/janyksteenbeek/uppi/issues)

A robust uptime monitoring solution built with Laravel, designed to track the availability of your web services and
notify you when issues arise.

## Features

- **Real-time Monitoring**: Continuously monitor the status of your web services
- **Smart Alerting**: Get notified when services go down and when they recover
- **Dashboard Overview**: Visual representation of your monitors' status
- **Anomaly Detection**: Track and manage service disruptions
- **Flexible Notifications**: Multiple notification channels for alerts

## Installation

1. Clone the repository:

```bash
git clone https://github.com/janyksteenbeek/uppi.git
cd uppi
```

2. Install dependencies:

```bash
composer install
```

3. Set up your environment:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env` and run migrations:

```bash
php artisan migrate
```

5. Build the assets

```bash
npm install
npm run build
```

6. Start the queue worker:

```bash
php artisan queue:work
```

## Usage

1. Access the dashboard at the URL you configured
2. Add monitors for the services you want to track
3. Configure alerts and notification preferences
4. Monitor your services through the dashboard

## Supervisor configuration

```cnf
[program:uppi]
command=php artisan queue:work --queue=default
autostart=true
autorestart=true
user=www-data
directory=/var/www/uppi
numprocs=10
redirect_stderr=true
stdout_logfile=/var/log/uppi.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start uppi
```

## License

Uppi is released under the Creative Commons Attribution-NonCommercial 4.0 International license. See
the [LICENSE](LICENSE). A human friendly summary is available
at [creativecommons.org](https://creativecommons.org/licenses/by-nc/4.0/).

Underlying packages is under their respective licenses.

## Security

If you discover any security-related issues, please email [uppi@janyk.dev](mailto:uppi@janyk.dev) instead of using the
issue tracker. All security vulnerabilities will be promptly addressed.
