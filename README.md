# E-Shop

Project for the course Advanced Internet Technologies, year 2017, Master in CS.

## Prerequisites

1. Install [Docker](https://www.docker.com/)
2. On Linux, make sure your user is part of *docker* group: `sudo usermod -a -G docker $(whoami)` (Not needed on Mac OS/OSX and MS Windows)

## Installation

1. Clone repo: `git clone git@gitlab.inf.unibz.it:CS-master-AIT-2017-rog-paa/eshop.git`
2. Build image: `docker build -t unibz/eshop eshop`
3. Run: `docker run -d -p 80:80 --name eshop -v $(pwd)/eshop/src:/var/www/html unibz/eshop` (This command may fail on Linux, in that case prepend `sudo`)
4. Code in `src` and visit the website on `http://127.0.0.1`