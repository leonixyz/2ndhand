# E-Shop

Project for the course Advanced Internet Technologies, year 2017, Master in CS.

## Prerequisites

1. [Docker](https://www.docker.com/)
2. On Linux make sure your user is part of *docker* group: `sudo usermod -a -G docker $(whoami)` (Not needed on Mac OS/OSX)

## Installation

1. Clone repo: `git clone git@gitlab.inf.unibz.it:CS-master-AIT-2017-rog-paa/eshop.git && cd eshop`
2. Build image: `docker build -t unibz/ait2017 .`
3. Run: `docker run -d -p 80:80 -v $(pwd)/src:/var/www/html unibz/ait2017` (This command may fail on Linux, in that case prepend `sudo`)
4. Code in `src` and visit the website on `http://127.0.0.1`