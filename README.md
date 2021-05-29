# To start the project:

- composer install
- sudo docker-compose up -d


- in prod page doesn't change? -- sudo php bin/console cache:clear -e prod
- sudo php bin/console cache:clear  
- can't modify some entity? -- sudo chown -R $USER /home/abonta/PhpstormProjects/preot.md/var/cache/prod