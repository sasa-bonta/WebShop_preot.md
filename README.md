# To start the project:

- composer install
- composer require symfonycasts/verify-email-bundle
- (optional) composer require --dev symfony/profiler-pack
- sudo docker-compose up -d


- in prod page doesn't change? -- sudo php bin/console cache:clear -e prod
- can't modify some entity? -- sudo chown -R $USER /home/abonta/PhpstormProjects/preot.md/var/cache/prod