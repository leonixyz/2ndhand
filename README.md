#Installation

1. Clone repo: `git clone https://gitlab.inf.unibz.it/CS-master-AIT-2017-rog-paa/eshop && cd eshop`
2. Build image: `docker build -t unibz/ait2017 .`
3. Run: `docker run -d -p 80:8080 -v $(pwd)/src:/var/www/html unibz/ait2017`
4. Code in `src` and check changes on `http://127.0.0.1:8080`