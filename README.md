# CnCNet Map DB

> A Rewrite of the legacy map db.

For now, it's just a rewrite of the legacy version with the same features, and it is working the same way. At some point it MUST be refactored.

Stack :
- Laravel 11

## Getting started

### Production

Set up the `.env file`.
```
TODO ...
```

Run the container
```
docker run -d --name mapdb2 -p 3003:8000 \
              --env-file .env \
              --restart unless-stopped \
              --volume=./maps:/app/storage/app/private \
              --volume=./caddy_config:/config \
              --volume=./data:/data \
              ghcr.io/$IMAGE_NAME:latest
```

### Development

> Laravel Sail is a light-weight command-line interface for interacting with Laravel's default Docker development environment.

Install dependencies
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

Configure the sail alias by adding this to your shell configuration file (`~/.zshrc` or `~/.bashrc`)
```
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

Start the app
```
sail up -d
```

## Endpoints

Here is the available endpoints.

### Upload endpoint 

Method : POST
```
/upload
```

accept a `multipart/form-data` with the following fields :
- `game` : the game abbreviation (yr, ra, ...)
- `file` : the zip file binary

Return the following status codes :
- `200` : if map uploaded successfully
- `400` : if file invalid

### Search endpoint

**WIP**

Method : GET
```
/search
```

Accept the following query string parameters :
- `game` : the game abbreviation (yr, ra, ...)
- `search` : the search text
- `age` : (optional) the age of the map ?
- `raw` : boolean for content raw or html


## Sponsored by
<a href="https://www.digitalocean.com/?refcode=337544e2ec7b&utm_campaign=Referral_Invite&utm_medium=opensource&utm_source=CnCNet" title="Powered by Digital Ocean" target="_blank">
    <img src="https://opensource.nyc3.cdn.digitaloceanspaces.com/attribution/assets/PoweredByDO/DO_Powered_by_Badge_blue.svg" width="201px" alt="Powered By Digital Ocean" />
</a>
