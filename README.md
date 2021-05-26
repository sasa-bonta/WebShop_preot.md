# To start the project:

- composer install
- composer require symfonycasts/verify-email-bundle
- (optional) composer require --dev symfony/profiler-pack 
- composer require ramsey/uuid
- sudo docker-compose up -d


- ? composer require symfony/webpack-encore-bundle
- in prod page doesn't change? -- sudo php bin/console cache:clear -e prod
- sudo php bin/console cache:clear  
- can't modify some entity? -- sudo chown -R $USER /home/abonta/PhpstormProjects/preot.md/var/cache/prod