# E-Shop

Project for the course Advanced Internet Technologies, year 2017, Master in CS.

## Prerequisites

1. Install [Docker](https://www.docker.com/)
2. On Linux, make sure your user is part of *docker* group: `sudo usermod -a -G docker $(whoami)` (Not needed on Mac OS/OSX and MS Windows)

## Installation

1. Clone repo recursively: `git clone --recursive https://github.com/leonixyz/2ndhand.git`
2. Build image: `docker build -t unibz/2ndhand 2ndhand`
3. Run: `docker run -d -p 80:80 -p 443:443 --name 2ndhand -v $(pwd)/2ndhand/src:/var/www/html unibz/2ndhand` (This command may fail on Linux, in that case prepend `sudo`)
4. Code in `src` and visit the website on `http://127.0.0.1`
5. Get database password `docker exec -it 2ndhand cat /var/www/postgresql-www-data.php` and manage database on `http://127.0.0.1/adminer.php`